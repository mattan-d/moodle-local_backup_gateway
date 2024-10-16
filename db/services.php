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
 * Web service definitions for local_backup_gateway
 *
 * @package    local_backup_gateway
 * @copyright  2015 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'local_backup_gateway_find_courses' => array(
         'classname' => 'local_backup_gateway_external',
         'methodname' => 'find_courses',
         'classpath' => 'local/backup_gateway/externallib.php',
         'description' => 'Find courses matching a given string.',
         'type' => 'read',
         'capabilities' => 'moodle/course:viewhiddencourses',
    ),
    'local_backup_gateway_get_course_backup_by_id' => array(
         'classname' => 'local_backup_gateway_external',
         'methodname' => 'get_course_backup_by_id',
         'classpath' => 'local/backup_gateway/externallib.php',
         'description' => 'Generate a course backup file and return a link.',
         'type' => 'read',
         'capabilities' => 'moodle/backup:backupcourse',
    ),
);
