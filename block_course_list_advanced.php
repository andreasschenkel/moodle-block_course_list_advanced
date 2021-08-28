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
 * @author     Andreas Schenkel - Schulportal Hessen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

include_once($CFG->dirroot . '/course/lib.php');

class block_course_list_advanced extends block_list
{
    function init()
    {
        $this->title = get_string('pluginname', 'block_course_list_advanced');
    }

    function has_config()
    {
        return true;
    }

    function get_content()
    {
        global $CFG, $USER, $DB, $OUTPUT;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        $icon = $OUTPUT->pix_icon('i/course', get_string('course'));

        $adminseesall = true;
        if (isset($CFG->block_course_list_advanced_adminview)) {
            if ($CFG->block_course_list_advanced_adminview == 'own') {
                $adminseesall = false;
            }
        }

        $allcourselink =
            (has_capability('moodle/course:update', context_system::instance())
                || empty($CFG->block_course_list_hideallcourseslink)) &&
            core_course_category::user_top();

        $countCoursesWithTrainer = 0;
        $countCoursesWithStudent = 0;
        if (
            empty($CFG->disablemycourses) and isloggedin() and !isguestuser() and
            !(has_capability('moodle/course:update', context_system::instance()) and $adminseesall)
        ) {
            // Just print My Courses
            if ($courses = enrol_get_my_courses()) {
                foreach ($courses as $course) {
                    $coursecontext = context_course::instance($course->id);
                    $linkcss = $course->visible ? "" : " class=\"dimmed\" ";

                    /**
                     * getting all users with moodle/course:manageactivities. This should be all user with role teacher (without noneditingteacher)
                     * @todo implement better way to find role of the user in the course
                     */
                    $editingteachers = get_users_by_capability($coursecontext, 'moodle/course:manageactivities');
                    $isEditingTeacher = false;
                    $roles = '';
                    foreach ($editingteachers as $teacher) {
                        if ($USER->username === $teacher->username) {
                            $isEditingTeacher = true;
                            $roles = $roles 
                            . ' <i class="text-info" 
                                data-toggle="tooltip" 
                                data-placement="right" 
                                title="Trainer: capability moodle/course:manageactivities" >
                                <font color="red">T</font></i>';
                            break;
                        }
                    }

                    /**
                     * now proof if user is student
                     */
                    $isStudent = false;
                    if (is_enrolled($coursecontext, $USER, 'mod/quiz:reviewmyattempts', $onlyactive = false)) {
                        $isStudent = true;
                        $roles = $roles . ' <i class="text-info" data-toggle="tooltip" data-placement="right" title="Schüler:in (reviewmyattempts)" ><font color="blue">S</font></i>';
                    }

                    /**
                     * now proof if user is isNoneditingTeacher
                     */
                    $isNoneditingTeacher = false;
                    if (!is_enrolled($coursecontext, $USER, 'moodle/course:changecategory', $onlyactive = false) &&  is_enrolled($coursecontext, $USER, 'moodle/course:markcomplete', $onlyactive = false)) {
                        $isNoneditingTeacher = true;
                        $roles = $roles . '  <i class="text-info" data-toggle="tooltip" data-placement="right" title="nonediting Teacher (changecategory)" ><font color="green">T</font></i>';
                    }


                    $htmllinktocourse = "<a $linkcss title=\"" . format_string($course->shortname, true, array('context' => $coursecontext)) . "\" " .
                        "href=\"$CFG->wwwroot/course/view.php?id=$course->id\">" . $icon . format_string(get_course_display_name_for_list($course)) . $dummy . "</a> ";

                    if ($isEditingTeacher) {
                        $listAllTrainerCourses = $listAllTrainerCourses . $htmllinktocourse .  '  ' . $roles . '<br /> ';
                        $countCoursesWithTrainer++;
                    }
                    if ($isStudent) {
                        $listAllStudentCourses = $listAllStudentCourses . $htmllinktocourse .  '  ' . $roles . '<br /> ';
                        $countCoursesWithStudent++;
                    }
                    if ($isNoneditingTeacher) {
                        $listAllNoneditingTeacherCourses = $listAllNoneditingTeacherCourses . $htmllinktocourse .  '  ' . $roles . '<br /> ';
                        $countCoursesWithNoneditingTeacher++;
                    }

                    //}
                    ////$this->content->items[ ]=  $htmllinktocourse . ' '  . $additionalInformation;

                }
                $this->title = get_string('mycourses');
                /// If we can update any course of the view all isn't hidden, show the view all courses link
                if ($allcourselink) {
                    $this->content->footer = "<a href=\"$CFG->wwwroot/course/index.php\">" . get_string("fulllistofcourses") . "</a> ...";
                }
            }
            if ($countCoursesWithTrainer) {
                $this->content->items[] = '<div class="course_list_advanced">' 
                    . $countCoursesWithTrainer
                    . ' '
                    . get_string('headlineteacher', 'block_course_list_advanced')
                    . '</div>';
                    $this->content->items[] = $listAllTrainerCourses . ' <br />';
            }
            if ($countCoursesWithStudent) {
                $this->content->items[] = '<div class="course_list_advanced">' .  $countCoursesWithStudent . ' ' . get_string('headlinestudent', 'block_course_list_advanced') . '</div>';
                $this->content->items[] = $listAllStudentCourses . '<br />';
            }
            if ($countCoursesWithNoneditingTeacher) {
                $this->content->items[] = '<div class="course_list_advanced">' .  $countCoursesWithNoneditingTeacher . ' ' . get_string('headlinenoneditingteacher', 'block_course_list_advanced') . '</div>';
                $this->content->items[] = $listAllNoneditingTeacherCourses . '<br />';
            }



            $this->get_remote_courses();
            if ($this->content->items) { // make sure we don't return an empty list
                return $this->content;
            }
        }

        // User is not enrolled in any courses, show list of available categories or courses (if there is only one category).
        $topcategory = core_course_category::top();
        if ($topcategory->is_uservisible() && ($categories = $topcategory->get_children())) { // Check we have categories.
            if (count($categories) > 1 || (count($categories) == 1 && $DB->count_records('course') > 200)) {     // Just print top level category links
                foreach ($categories as $category) {
                    $categoryname = $category->get_formatted_name();
                    $linkcss = $category->visible ? "" : " class=\"dimmed\" ";
                    $this->content->items[] = "<a $linkcss href=\"$CFG->wwwroot/course/index.php?categoryid=$category->id\">" . $icon . $categoryname . "</a>";
                }
                /// If we can update any course of the view all isn't hidden, show the view all courses link
                if ($allcourselink) {
                    $this->content->footer .= "<a href=\"$CFG->wwwroot/course/index.php\">" . get_string('fulllistofcourses') . '</a> ...';
                }
                $this->title = get_string('categories');
            } else {                          // Just print course names of single category
                $category = array_shift($categories);
                $courses = $category->get_courses();

                if ($courses) {
                    foreach ($courses as $course) {
                        $coursecontext = context_course::instance($course->id);
                        $linkcss = $course->visible ? "" : " class=\"dimmed\" ";

                        $this->content->items[] = "<a $linkcss title=\""
                            . s($course->get_formatted_shortname()) . "\" " .
                            "href=\"$CFG->wwwroot/course/view.php?id=$course->id\">"
                            . $icon . $course->get_formatted_name() . "</a>";
                    }
                    /// If we can update any course of the view all isn't hidden, show the view all courses link
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

    function get_remote_courses()
    {
        global $CFG, $USER, $OUTPUT;

        if (!is_enabled_auth('mnet')) {
            // no need to query anything remote related
            return;
        }

        $icon = $OUTPUT->pix_icon('i/mnethost', get_string('host', 'mnet'));

        // shortcut - the rest is only for logged in users!
        if (!isloggedin() || isguestuser()) {
            return false;
        }

        if ($courses = get_my_remotecourses()) {
            $this->content->items[] = get_string('remotecourses', 'mnet');
            $this->content->icons[] = '';
            foreach ($courses as $course) {
                $this->content->items[] = "<a title=\"" . format_string($course->shortname, true) . "\" " .
                    "href=\"{$CFG->wwwroot}/auth/mnet/jump.php?hostid={$course->hostid}&amp;wantsurl=/course/view.php?id={$course->remoteid}\">"
                    . $icon . format_string(get_course_display_name_for_list($course)) . "</a>";
            }
            // if we listed courses, we are done
            return true;
        }

        if ($hosts = get_my_remotehosts()) {
            $this->content->items[] = get_string('remotehosts', 'mnet');
            $this->content->icons[] = '';
            foreach ($USER->mnet_foreign_host_array as $somehost) {
                $this->content->items[] = $somehost['count'] . get_string('courseson', 'mnet') . '<a title="' . $somehost['name'] . '" href="' . $somehost['url'] . '">' . $icon . $somehost['name'] . '</a>';
            }
            // if we listed hosts, done
            return true;
        }

        return false;
    }

    /**
     * Returns the role that best describes the course list block.
     *
     * @return string
     */
    public function get_aria_role()
    {
        return 'navigation';
    }

    /**
     * Return the plugin config settings for external functions.
     *
     * @return stdClass the configs for both the block instance and plugin
     * @since Moodle 3.8
     */
    public function get_config_for_external()
    {
        global $CFG;

        // Return all settings for all users since it is safe (no private keys, etc..).
        $configs = (object) [
            'adminview' => $CFG->block_course_list_advanced_adminview,
            'hideallcourseslink' => $CFG->block_course_list_advanced_hideallcourseslink
        ];

        return (object) [
            'instance' => new stdClass(),
            'plugin' => $configs,
        ];
    }
}
