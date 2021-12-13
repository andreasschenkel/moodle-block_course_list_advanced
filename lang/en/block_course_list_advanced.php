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
 * Strings for component 'block_course_list_advanced', language 'en', branch 'MOODLE_39_STABLE'
 *
 * @package    block_course_list_advanced
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @author     Andreas Schenkel - Schulportal Hessen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allcourses'] = 'Admin user sees all courses';
$string['blocktitle'] = 'Courses advanced';
$string['blocktitlealt'] = 'not supported';
$string['blockfooteralt'] = 'Content only supported in a course where user is trainer';

$string['adminview'] = 'Admin view';
$string['configadminview'] = 'Whether to display all courses in the Courses advanced block, or only courses that the admin is enrolled in.';

$string['hideallcourseslink'] = 'Hide \'All courses\' link';
$string['confighideallcourseslink'] = 'Remove the \'All courses\' link under the list of courses. (This setting does not affect the admin view.)';

$string['showdeleteicon'] = 'Show a delete-icon to delete directly from the block.';
$string['configshowdeleteicon'] = 'If set to true a delete-icon is shown near the coursename in order to be able to delete the course directly from the block.';

$string['usesphorphanedfiles'] = 'Use Plugin orphanedfiles (Plugin must be installed!)';
$string['configusesphorphanedfiles'] = 'When activated a ? as a link is shown to jump directly to the List of orphaned files in this course.';

$string['isallowedonfrontpage'] = 'Allow to add block on the frontpage';
$string['configisallowedonfrontpage'] = 'When activated it is allowed to add block to frontpage.';

$string['isallowedonmypage'] = 'Allow to add block on the mypage';
$string['configisallowedonmypage'] = 'When activated it is allowed to add block to mypage.';

$string['max_for_siteadmin'] = 'Maximum number of courses in section all courses for siteadmins';
$string['configmax_for_siteadmin'] = 'Maximum number of courses in section all courses for siteadmins';

$string['course_list_advanced:view'] = 'Show block';
$string['course_list_advanced:addinstance'] = 'Add a new courses block';
$string['course_list_advanced:myaddinstance'] = 'Add a new courses block to Dashboard';

$string['owncourses'] = 'Admin user sees own courses';
$string['pluginname'] = 'Courses advanced';
$string['privacy:metadata'] = 'The Courses block only shows data about courses and does not store any data itself.';
$string['headlineteacher'] = '<b>Course(s) - trainer</b>';
$string['headlinestudent'] = '<b>Course(s) - student</b>';
$string['headlinenoneditingteacher'] = '<b>Course(s) - trainer nonediting</b>';
$string['headlinenallcourses'] = '<b>Course(s) - all</b>';

$string['noenddate'] = 'open';

$string['tooltipptextstudent'] = 'student (reviewmyattempts)';
$string['tooltipptextstudentindicator'] = 'S';
$string['tooltipptexteditingteacher'] = 'editingteacher: capability moodle/course:manageactivities';
$string['tooltipptexteditingteacherindicator'] = 'T';
$string['tooltipptextsphorphanedfiles'] = 'Report orphened files';
