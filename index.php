<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Landing page for local_backup_gateway
 *
 * @package    local_backup_gateway
 * @copyright  2015 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once('search_form.php');

$id = required_param('id', PARAM_INT);
$remote = optional_param('remote', 0, PARAM_INT);
$search = optional_param('search', null, PARAM_NOTAGS);
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_login($course);
$PAGE->set_url('/local/backup_gateway/index.php', array('id' => $id));
$PAGE->set_pagelayout('report');
$returnurl = new moodle_url('/course/view.php', array('id' => $id));

// Check permissions.
$context = context_course::instance($course->id);
require_capability('local/backup_gateway:access', $context);

// Get config settings.
$token = get_config('local_backup_gateway', 'wstoken');
$remotesite = get_config('local_backup_gateway', 'remotesite');
if (empty($token) || empty($remotesite)) {
    print_error('pluginnotconfigured', 'local_backup_gateway', $returnurl);
}

$options = [
        'CURLOPT_SSL_VERIFYPEER' => false,
        'CURLOPT_SSL_VERIFYHOST' => false,
];

if (empty($search) && !$remote) {
    $search = $course->idnumber;
}

// Get the courses.
if (!empty($search)) {

    $user = $DB->get_record('user', array('id' => $USER->id));
    $url = $remotesite . '/webservice/rest/server.php?wstoken=' . $token .
            '&wsfunction=local_backup_gateway_find_courses&moodlewsrestformat=json';
    $params = array('search' => $search, 'email' => $user->email);

    $curl = new curl;
    $results = json_decode($curl->post($url, $params, $options));
    $data = array();
    foreach ($results as $result) {
        $data[] = html_writer::link(
                new moodle_url('/local/backup_gateway/index.php',
                        array('id' => $id, 'remote' => $result->id)
                ),
                '[' . $result->shortname . '] ' . $result->fullname
        );
    }

} else if ($remote !== 0) {
    // Generate the backup file.
    $fs = get_file_storage();
    $url = $remotesite . '/webservice/rest/server.php?wstoken=' . $token .
            '&wsfunction=local_backup_gateway_get_course_backup_by_id&moodlewsrestformat=json';
    $params = array('id' => $remote, 'username' => $USER->username);
    $curl = new curl;
    $resp = json_decode($curl->post($url, $params, $options));

    // Import the backup file.
    $timestamp = time();
    $filerecord = array(
            'contextid' => $context->id,
            'component' => 'local_backup_gateway',
            'filearea' => 'backup',
            'itemid' => $timestamp,
            'filepath' => '/',
            'filename' => 'foo',
            'timecreated' => $timestamp,
            'timemodified' => $timestamp
    );

    $storedfile = $fs->create_file_from_url($filerecord, $resp->url . '?token=' . $token, $options, true);
    $restoreurl = new moodle_url(
            '/backup/restore.php',
            array(
                    'contextid' => $context->id,
                    'pathnamehash' => $storedfile->get_pathnamehash(),
                    'contenthash' => $storedfile->get_contenthash()
            )
    );
    redirect($restoreurl);
}

$PAGE->requires->js_call_amd('local_backup_gateway/state', 'checkstate',
        array('wait' => get_string('wait', 'local_backup_gateway')));
$PAGE->set_title($course->shortname . ': ' . get_string('import', 'local_backup_gateway'));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

// Display the courses.
if (!empty($data)) {
    echo html_writer::tag('h2', get_string('availablecourses', 'local_backup_gateway'));
    echo html_writer::tag('i', 'מקור: ' . $remotesite);
    echo html_writer::alist($data, array('class' => 'backupremote'));
}

// Show the search form.
$mform = new local_backup_gateway_search_form();
if (!$mform->is_cancelled()) {
    $toform = new stdClass();
    $toform->id = $id;
    $mform->set_data($toform);
    $mform->display();
}
echo $OUTPUT->footer();
