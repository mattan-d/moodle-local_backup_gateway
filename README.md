Moodle Remote Backup Provider
=============================

[![Build Status](https://travis-ci.org/LafColITS/moodle-local_backup_gateway.svg?branch=master)](https://travis-ci.org/LafColITS/moodle-local_backup_gateway)

[SEEKING MAINTAINER] This plugin is no longer maintained and there will be no further releases.

This local module allows you to restore a course from a remote Moodle instance into your current instance via a REST web service. The intended use case is quick restores from an archival Moodle instance into the current yearly instance.

It is limited to administrators but could in the future be extended for teacher use. Currently it is limited to one pair of source and target instances.

Configuration
-------------
You will need to install this plugin on both the source and target instances. On the source Moodle instance you'll need to create the following:

1. An external web service.
2. A user with sufficient permissions to use said web service.
3. A token for that user. For additional security it should be restricted to the target server's IP address.

See [Using web services](https://docs.moodle.org/29/en/Using_web_services) in the Moodle documentation for information about creating and enabling web services. The user will need the following capabilities in addition to whichever protocol you enable:

- `moodle/course:view`
- `moodle/course:viewhiddencourses`
- `moodle/backup:backupcourse`

The web service will need the following functions:

- `local_backup_gateway_find_courses`
- `local_backup_gateway_get_course_backup_by_id`

On the target Moodle instance you will need to configure the token and source Moodle URL in the System Administration block under Local Plugins > Remote Backup Provider.

Usage
-----
On the target instance you will have a new link in the Course Administration block called "Import from remote". This will bring up a page with a search interface which queries the source instance for a list of matching courses. Clicking the link begins the following process, all of which happens automatically:

1. A backup will be created on the source instance.
2. The backup will be copied into the target instance.
3. You will be redirected to the course restore dialog with the backup preloaded.

At this point follow the course restore process. The backup is created according to the general defaults on the source instance. Cron is configured to delete the backup files on *both* environments after 24 hours.

Requirements
------------
- Moodle 3.6 (build 2018120300 or later)

Installation
------------
Copy the backup_gateway folder into your /local directory and visit your Admin Notification page to complete the installation.

Author
------
Charles Fulton (fultonc@lafayette.edu)
