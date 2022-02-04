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
 * Course list advanced block.
 *
 * @package    block_course_list_advanced
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @author     Andreas Schenkel - Schulportal Hessen 2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');

use block_course_list_advanced\confighandler;
use block_course_list_advanced\htmlhelper;

class block_course_list_advanced extends block_list
{
    public function init() {
        $this->title = get_string('pluginname', 'block_course_list_advanced');
    }

    public function has_config() {
        return true;
    }

    public function get_content() {
        global $CFG, $USER, $DB, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        $confighandler = new confighandler($CFG);

        // If not BOTH privileges then do not show content for performancereason. must be allowed to see course AND must be trainer.
        $isallowedtoseecontent = false;
        $isallowedtoseecontent = (has_capability('block/course_list_advanced:view', $this->context)
            && has_capability('block/course_list_advanced:viewblockcontent', $this->context));
        if (!$isallowedtoseecontent) {
            $this->title = get_string('blocktitlealt', 'block_course_list_advanced');
            $this->content->footer = get_string('blockfooteralt', 'block_course_list_advanced');
            return $this->content;
        }

        $icon = $OUTPUT->pix_icon('i/course', get_string('course'));
        $icondelete = $OUTPUT->pix_icon('i/delete', get_string('delete'));

        $allcourselink =
            (has_capability('moodle/course:update', context_system::instance())
                || empty($CFG->block_course_list_hideallcourseslink)) &&
            core_course_category::user_top();

        $countcourseseditingteacher = 0;
        $countcourseswithstudent = 0;
        $countcoursesall = 0;
        if (
            empty($CFG->disablemycourses) && isloggedin() && !isguestuser() &&
            !(has_capability('moodle/course:update', context_system::instance()) && $confighandler->getadminseesall())
        ) {
            // Put information into StdClass or array or class (todo).
            $listalltrainercourses = '';
            $listallnoneditingteachercourses = '';
            $listallstudentcourses = '';
            $listallguestcourses = '';
            $listallcourses = '';

            $countcourseseditingteacher = 0;
            $countcoursesnoneditingteacher = 0;
            $countcourseswithstudent = 0;
            $countcourseswithguest = 0;
            $countcoursesall = 0;
            $now = time();

            // older code for documentation 
            if (is_siteadmin()) {
                $courses = $this->getallcoursesbyselect();
            } else {
                $courses = enrol_get_my_courses();
            }
             
            // $courses = enrol_get_my_courses();
            // 1. Alle Rollen ermitteln, die angezeigt werden sollen (default editing teacher, non editing teacher, student, guest)
            // 2. Für jeden Kurs ermitteln, welche Rolle der User in hat und in den entsprechendne Objekte sammeln
            // 3. Anzeige generieren für die entsprechende Rolle

            $showcourseswithguestrole = '';
            $showcourseswithguestrole = $CFG->block_course_list_advanced_showcourseswithguestrole;
            if ($courses) {
                foreach ($courses as $course) {
                    $coursecontext = context_course::instance($course->id);

                    $editingteacher_roleid = '3';
                    $noneditingteacher_roleid = '4';
                    $student_roleid = '5';
                    $guest_roleid = '6';

                    $is_editingteacher = $this->has_user_role_with_roleid_in_context($USER, $editingteacher_roleid, $coursecontext);
                    $is_noneditingteacher = $this->has_user_role_with_roleid_in_context($USER, $noneditingteacher_roleid , $coursecontext);
                    $is_student = $this->has_user_role_with_roleid_in_context($USER, $student_roleid, $coursecontext);
                    $is_guest = $this->has_user_role_with_roleid_in_context($USER, $guest_roleid, $coursecontext);

                    $linkcss = $course->visible ? "" : " class=\"dimmed\" ";
                    $startdate = date('d/m/Y', $course->startdate);

                    // Code: course->enddate is empty if function enrol_get_my_courses() was used.
                    $courserecord = $DB->get_record('course', array('id' => $course->id));
                    if ($courserecord->enddate) {
                        $enddate = date('d/m/Y', $courserecord->enddate);
                    } else {
                        $enddate = get_string('noenddate', 'block_course_list_advanced') . ' ';
                    }

                    // Auslagern: @todo in Funktion.
                    $coursecss = '';
                    // Documentation of code: if ($course->startdate <= $now) {.
                    if ($courserecord->startdate <= $now) {
                        if ($courserecord->enddate > $now || !$courserecord->enddate) {
                            $coursecss = 'class="coursecssactiv"';
                        } else if ($courserecord->enddate < $now) {
                            $coursecss = 'class="coursecssfinished"';
                        }
                    } else {
                        $coursecss = 'class="coursecssfuture"';
                    }

                    $roles = '';
                    if ($is_editingteacher) {
                        $roles = $roles . " " . $this->createroleindicator(
                            get_string('tooltipptexteditingteacher', 'block_course_list_advanced'),
                            get_string('tooltipptexteditingteacherindicator', 'block_course_list_advanced'),
                            'ff0000'
                        );
                    }

                    if ($is_student) {
                        $roles = $roles . " " . $this->createroleindicator(
                            get_string('tooltipptextstudent', 'block_course_list_advanced'),
                            get_string('tooltipptextstudentindicator', 'block_course_list_advanced'),
                            '0000ff'
                        );
                    }

                    if ($is_noneditingteacher) {
                        $roles = $roles
                            . '  <i class="text-info" data-toggle="tooltip" data-placement="right" title="nonediting Teacher (changecategory)" ><font color="green">T</font></i>';
                    }

                    if ($is_guest && $showcourseswithguestrole) {
                        $roles = $roles
                            . '  <i class="text-info" data-toggle="tooltip" data-placement="right" title="Guest" ><font color="#888800">G</font></i>';
                    }

                    $duration = $startdate . ' - ' . $enddate;

                    $htmllinktocourse = "<a $linkcss title=\""
                        . format_string($course->shortname, true, array('context' => $coursecontext)) . " id=$course->id"
                        . "\" "
                        . "href=\"$CFG->wwwroot/course/view.php?id=$course->id\">"
                        . $icon
                        . format_string(get_course_display_name_for_list($course))
                        . "</a>";

                    $htmllinktocoursedeletion = '';
                    // Only if showdeleteicon is true, then we have to check, which courses are deletable and show a delete-icon.
                    if ($confighandler->get_showdeleteicon() && is_enrolled($coursecontext, $USER, 'moodle/course:delete', $onlyactive = false)) {
                        $htmllinktocoursedeletion = "<a $linkcss style=\"color: #921616\" title=\""
                            . format_string($course->shortname, true, array('context' => $coursecontext))
                            . "\" "
                            . "href=\"$CFG->wwwroot/course/delete.php?id=$course->id\">"
                            . $icondelete
                            . "</a>";
                    }

                    $iconorphanedfileslink = $this->createroleindicator(get_string('tooltipptextsphorphanedfiles', 'block_course_list_advanced'), ' <i class="fa fa-server"></i>', '008800');

                    $linkvieworphanedfiles = '';
                    if ($confighandler->get_usesphorphanedfiles()) {
                        $orphanedfileslink = new moodle_url('/report/sphorphanedfiles/index.php', array('id' => $course->id));
                        $linkvieworphanedfiles = '<a href="' . $orphanedfileslink . '"   target="_blank">  ' . $iconorphanedfileslink . '</a>';
                    }

                    if ($is_editingteacher) {
                        $listalltrainercourses = $listalltrainercourses  . '<div ' . $linkcss . '>' . '<div ' . $coursecss . '>' . $htmllinktocourse .  '  ' .  $linkvieworphanedfiles . '  ' . $htmllinktocoursedeletion . ' ' . $roles . '<br>' . $duration . '</div></div>';
                        $countcourseseditingteacher++;
                    }
                    if ($is_student) {
                        $listallstudentcourses = $listallstudentcourses . '<div ' . $linkcss . '>' . '<div ' . $coursecss . '>' . $htmllinktocourse .  '  ' . $roles . '<br>' . $duration . '</div></div>';
                        $countcourseswithstudent++;
                    }
                    if ($is_noneditingteacher) {
                        $listallnoneditingteachercourses = $listallnoneditingteachercourses
                            . '<div ' . $linkcss . '>'
                            . '<div '
                            . $coursecss
                            . '>'
                            . $htmllinktocourse
                            .  '  '
                            . $roles
                            . '<br>'
                            . $duration
                            . '</div></div>';
                        $countcoursesnoneditingteacher++;
                    }

                    if ($is_guest) {
                        $listallguestcourses = $listallguestcourses . '<div ' . $linkcss . '>' . '<div ' . $coursecss . '>' . $htmllinktocourse .  '  ' . $roles . '<br>' . $duration . '</div></div>';
                        $countcourseswithguest++;
                    }

                    if (is_siteadmin() && $countcoursesall <= $confighandler->get_max_for_siteadmin()) {
                        $listallcourses = $listallcourses  . '<div ' . $linkcss . '>' . '<div ' . $coursecss . '>' . $htmllinktocourse .  '  ' .  $linkvieworphanedfiles . '  ' . $htmllinktocoursedeletion . ' ' . $roles . '<br>' . $duration . '</div></div>';
                        $countcoursesall++;
                    }
                }

                $title = '';
                $title = get_string('blocktitle', 'block_course_list_advanced');
                if (is_siteadmin()) {
                    $title = $title . " (Siteadmin)";
                }
                $this->title = $title;
                // If we can update any course of the view all isn't hidden, show the view all courses link.
                if ($allcourselink) {
                    $this->content->footer = "<a href=\"$CFG->wwwroot/course/index.php\">"
                        . get_string("fulllistofcourses")
                        . "</a> ...";
                }
            }

            // TODO: closing the div from class row???
            $blockrow = '<div class="row">';
            if ($countcourseseditingteacher) {
                $blockrow = $blockrow . htmlhelper::generate_role_block($countcourseseditingteacher, "headlineteacher", $listalltrainercourses, "");
            }
            if ($countcourseswithstudent) {
                $blockrow = $blockrow . htmlhelper::generate_role_block($countcourseswithstudent, "headlinestudent", $listallstudentcourses, "");       
            }
            if ($countcoursesnoneditingteacher) {
                $blockrow = $blockrow . htmlhelper::generate_role_block($countcoursesnoneditingteacher, "headlinenoneditingteacher", $listallnoneditingteachercourses, "");       
            }
            if ($countcourseswithguest && $showcourseswithguestrole) {
                $blockrow = $blockrow . htmlhelper::generate_role_block($countcourseswithguest, "headlineguest", $listallguestcourses, "");       
            }
            // siteadmins can view all courses
            if (is_siteadmin() && $countcoursesall) {
                $max = '';
                $max = $confighandler->get_max_for_siteadmin() ;
                $blockrow = $blockrow . htmlhelper::generate_role_block($countcoursesall, "headlinenallcourses", $listallcourses, " (max.  $max )");       
                $this->content->items[] = $blockrow;
            }
            





            $this->get_remote_courses();
            if ($this->content->items) {
                // Make sure we don't return an empty list.
                return $this->content;
            }
        }

        // User is not enrolled in any courses, show list of available categories or courses (if there is only one category).
        $topcategory = core_course_category::top();
        if ($topcategory->is_uservisible() && ($categories = $topcategory->get_children())) { // Check we have categories.
            if (count($categories) > 1 || (count($categories) === 1 && $DB->count_records('course') > 200)) {
                // Just print top level category links.
                foreach ($categories as $category) {
                    $categoryname = $category->get_formatted_name();
                    $linkcss = $category->visible ? "" : " class=\"dimmed\" ";
                    $this->content->items[] = "<a $linkcss href=\"$CFG->wwwroot/course/index.php?categoryid=$category->id\">"
                        . $icon
                        . $categoryname
                        . "</a>";
                }
                // If we can update any course of the view all isn't hidden, show the view all courses link.
                if ($allcourselink) {
                    $this->content->footer .= "<a href=\"$CFG->wwwroot/course/index.php\">" . get_string('fulllistofcourses') . '</a> ...';
                }
                $this->title = get_string('categories');
            } else {
                // Just print course names of single category.
                $category = array_shift($categories);
                $courses = $category->get_courses();

                if ($courses) {
                    foreach ($courses as $course) {
                        // Code: $coursecontext = context_course::instance($course->id);.
                        $linkcss = $course->visible ? "" : " class=\"dimmed\" ";

                        $this->content->items[] = "<a $linkcss title=\""
                            . s($course->get_formatted_shortname()) . "\" " .
                            "href=\"$CFG->wwwroot/course/view.php?id=$course->id\">"
                            . $icon . $course->get_formatted_name() . "</a>";
                    }
                    // If we can update any course of the view all isn't hidden, show the view all courses link.
                    if ($allcourselink) {
                        $this->content->footer .= "<a href=\"$CFG->wwwroot/course/index.php\">" . get_string('fulllistofcourses') . '</a> ...';
                    }
                    $this->get_remote_courses();
                } else {
                    $this->content->icons[] = '';
                    $this->content->items[] = get_string('nocoursesyet');
                    if (has_capability('moodle/course:create', context_coursecat::instance($category->id))) {
                        $this->content->footer = '<a href="' . $CFG->wwwroot . '/course/edit.php?category=' . $category->id . '">' . get_string("addnewcourse") . '</a> ...';
                    }
                    $this->get_remote_courses();
                }
                $this->title = get_string('courses');
            }
        }
        return $this->content;
    }

    private function get_remote_courses() {
        global $CFG, $USER, $OUTPUT;

        if (!is_enabled_auth('mnet')) {
            // No need to query anything remote related.
            return;
        }

        $icon = $OUTPUT->pix_icon('i/mnethost', get_string('host', 'mnet'));

        // Shortcut - the rest is only for logged in users!
        if (!isloggedin() || isguestuser()) {
            return false;
        }

        if ($courses = get_my_remotecourses()) {
            $this->content->items[] = get_string('remotecourses', 'mnet');
            $this->content->icons[] = '';
            foreach ($courses as $course) {
                $this->content->items[] = "<a title=\""
                    . format_string($course->shortname, true)
                    . "\" "
                    . "href=\"{$CFG->wwwroot}/auth/mnet/jump.php?hostid={$course->hostid}&amp;wantsurl=/course/view.php?id={$course->remoteid}\">"
                    . $icon
                    . format_string(get_course_display_name_for_list($course)) . "</a>";
            }
            // If we listed courses, we are done.
            return true;
        }

        if ($hosts = get_my_remotehosts()) {
            $this->content->items[] = get_string('remotehosts', 'mnet');
            $this->content->icons[] = '';
            foreach ($USER->mnet_foreign_host_array as $somehost) {
                $this->content->items[] = $somehost['count'] . get_string('courseson', 'mnet')
                    . '<a title="' . $somehost['name'] . '" href="' . $somehost['url'] . '">' . $icon . $somehost['name'] . '</a>';
            }
            // If we listed hosts, done.
            return true;
        }

        return false;
    }

    /**
     * Returns the role that best describes the course list block.
     *
     * @return string
     */
    public function get_aria_role() {
        return 'navigation';
    }

    /**
     * Return the plugin config settings for external functions.
     *
     * @return stdClass the configs for both the block instance and plugin
     * @since Moodle 3.8
     */
    public function get_config_for_external() {
        global $CFG;

        // Return all settings for all users since it is safe (no private keys, etc..).
        $configs = (object) [
            'adminview' => $CFG->block_course_list_advanced_adminview,
            'hideallcourseslink' => $CFG->block_course_list_advanced_hideallcourseslink,
            'showdeleteicon' => $CFG->block_course_list_advanced_showdeleteicon,
            'isallowedonfrontpage' => $CFG->block_course_list_advanced_isallowedonfrontpage,
            'isallowedonmypage' => $CFG->block_course_list_advanced_isallowedonmypage
        ];

        return (object) [
            'instance' => new stdClass(),
            'plugin' => $configs,
        ];
    }

    /**
     * only show block in a course in order to prevent that the course is placed at frontpage or dashboard
     * frontpage and dashboard are shown many times and the code is not jet optimized for large instances of moodle with many users
     * @todo optimize
     */
    public function applicable_formats() {
        global $CFG;
        $confighandler = new confighandler($CFG);
        return array(
            'site-index' => $confighandler->get_isallowedonfrontpage(),
            'my' => $confighandler->get_isallowedonmypage(),
            'course-view' => true
        );
    }

    /**
     * @return array returns all courses in this moodle
     */
    public function getallcoursesbyselect(): array {
        global $DB;
        $query = "SELECT id, fullname, shortname, startdate, enddate, visible from {course}";
        $courselist = $DB->get_records_sql($query);
        return $courselist;
    }

    /**
     * @param string $color like #ff0000
     * @return string indicator for the role as html-code
     */
    public function createroleindicator($title, $shortcut, $color): string {
        $roleindicatorstring = "<i class='text-info' "
            . "data-toggle='tooltip' "
            . "data-placement='bottom' "
            . "title='$title' > <font color='$color'>$shortcut</font></i>";
        return $roleindicatorstring;
    }

    /**
     * @param string $USER  
     * @param string $roleid  
     * @param string $context  
     * @return bool true, if $user is has role with $roleid in $context
     */
    public function has_user_role_with_roleid_in_context($USER, $roleid, $context): bool {
        $roles = get_user_roles($context, $USER->id, true);
        foreach ($roles as $role) {
            if ($role->roleid === $roleid ) {
                return true;
            }
        }
        return false;
    }


}
