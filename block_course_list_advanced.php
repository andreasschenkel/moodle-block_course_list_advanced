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

include_once($CFG->dirroot . '/course/lib.php');
use block_course_list_advanced\config_handler;

class block_course_list_advanced extends block_list
{
    public function init()
    {
        $this->title = get_string('pluginname', 'block_course_list_advanced');
    }

    public function has_config()
    {
        return true;
    }

    public function get_content()
    {
        global $CFG, $USER, $DB, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        $configHandler = new config_handler($CFG);

        // if not BOTH privileges then do not show content for performancereason. must be allowed to see course AND must be trainer 
        $isAllowedToSeeContent = false;
        $isAllowedToSeeContent = (has_capability('block/course_list_advanced:view', $this->context)
            && has_capability('block/course_list_advanced:viewContent', $this->context));
        if (!$isAllowedToSeeContent) {
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

        $countCoursesEditingTeacher = 0;
        $countCoursesWithStudent = 0;
        $countCoursesAll = 0;
        if (
            empty($CFG->disablemycourses) && isloggedin() && !isguestuser() &&
            !(has_capability('moodle/course:update', context_system::instance()) && $configHandler->getAdminseesall())
        ) {
            /**
             * @todo put information into StdClass or array or class
             */
            $listAllTrainerCourses = '';
            $listAllStudentCourses = '';
            $listAllNoneditingTeacherCourses = '';
            $listAllCourses = '';

            $countCoursesEditingTeacher = 0;
            $countCoursesWithStudent = 0;
            $countCoursesNoneditingTeacher = 0;
            $countCoursesAll = 0;
            $now = time();

            if (is_siteadmin()) {
                $courses = $this->getAllCoursesBySelect();
            } else {
                $courses = enrol_get_my_courses();
            }

            if ($courses) {
                foreach ($courses as $course) {
                    $coursecontext = context_course::instance($course->id);
                    $linkcss = $course->visible ? "" : " class=\"dimmed\" ";
                    $startDate = date('d/m/Y', $course->startdate);

                    // course->enddate is empty if function enrol_get_my_courses() was used;
                    $courserecord = $DB->get_record('course', array('id' => $course->id));
                    if ($courserecord->enddate) {
                        $endDate = date('d/m/Y', $courserecord->enddate);
                    } else {
                        $endDate = get_string('noenddate', 'block_course_list_advanced') . ' ';
                    }

                    /**
                     * @todo auslagern in funktion
                     */
                    $coursecss = '';
                    //if ($course->startdate <= $now) {
                    if ($courserecord->startdate <= $now) {
                        if ($courserecord->enddate > $now || !$courserecord->enddate) {
                            $coursecss = 'class="coursecssactiv"';
                        } else if ($courserecord->enddate < $now) {
                            $coursecss = 'class="coursecssfinished"';
                        }
                    } else {
                        $coursecss = 'class="coursecssfuture"';
                    }

                    /**
                     * getting all users with moodle/course:manageactivities.
                     * This should be all user with role teacher (without noneditingteacher)
                     * @todo implement better way to find role of the user in the course
                     * @todo see at https://docs.moodle.org/dev/NEWMODULE_Adding_capabilities
                     *                   // $roles = get_user_roles($coursecontext, $USER->id, false);
                     * @todo check if this could be used for a better implementation
                     * echo "<br>Kursid " . $course->id ;
                     * var_dump($roles);
                     * foreach ($roles as $dummy) {
                     *    $role = key($roles);
                     *    $rolename = $roles[$role]->shortname;
                     *    echo "  " . $rolename . "; ";
                     */
                    $editingteachers = get_users_by_capability($coursecontext, 'moodle/course:manageactivities');
                    $isEditingTeacher = false;
                    $roles = '';
                    foreach ($editingteachers as $teacher) {
                        if ($USER->username === $teacher->username) {
                            $isEditingTeacher = true;
                            $roles = $roles . " " . $this->createRoleIndicator(
                                get_string('tooltipptexteditingteacher', 'block_course_list_advanced'),
                                get_string('tooltipptexteditingteacherindicator', 'block_course_list_advanced'),
                                'ff0000'
                            );
                            break;
                        }
                    }

                    $isStudent = is_enrolled($coursecontext, $USER, 'mod/quiz:reviewmyattempts', $onlyactive = false) ? true : false;
                    if ($isStudent) {
                        $roles = $roles . " " . $this->createRoleIndicator(
                            get_string('tooltipptextstudent', 'block_course_list_advanced'),
                            get_string('tooltipptextstudentindicator', 'block_course_list_advanced'),
                            '0000ff'
                        );
                    }

                    $isNoneditingTeacher = !is_enrolled($coursecontext, $USER, 'moodle/course:changecategory', $onlyactive = false)
                        &&  is_enrolled($coursecontext, $USER, 'moodle/course:markcomplete', $onlyactive = false) ? true : false;

                    if ($isNoneditingTeacher) {
                        $roles = $roles
                            . '  <i class="text-info" data-toggle="tooltip" data-placement="right" title="nonediting Teacher (changecategory)" ><font color="green">T</font></i>';
                    }

                    $duration =  $startDate . ' - ' . $endDate;

                    $htmllinktocourse = "<a $linkcss title=\""
                        . format_string($course->shortname, true, array('context' => $coursecontext))
                        . "\" "
                        . "href=\"$CFG->wwwroot/course/view.php?id=$course->id\">"
                        . $icon
                        . format_string(get_course_display_name_for_list($course))
                        . "</a>";

                    // only if showdeleteicon is true, then we have to check, which courses are deletable and show a delete-icon
                    if ($configHandler->getShowdeleteicon() && is_enrolled($coursecontext, $USER, 'moodle/course:delete', $onlyactive = false)) {
                        $htmllinktocoursedeletion = "<a $linkcss style=\"color: #921616\" title=\""
                            . format_string($course->shortname, true, array('context' => $coursecontext))
                            . "\" "
                            . "href=\"$CFG->wwwroot/course/delete.php?id=$course->id\">"
                            . $icondelete
                            . "</a>";
                    }

                    $iconOrphanedFilesLink = $this->createRoleIndicator(get_string('tooltipptextsphorphanedfiles', 'block_course_list_advanced'), ' <i class="fa fa-server"></i>', '008800');

                    $linkViewOrphanedFiles = '';
                    if ($configHandler->getUsesphorphanedfiles()) {
                        $orphanedFilesLink = new moodle_url('/report/sphorphanedfiles/index.php', array('id' => $course->id));
                        $linkViewOrphanedFiles = '<a href="' . $orphanedFilesLink . '">  ' . $iconOrphanedFilesLink . '</a>';
                    }

                    if ($isEditingTeacher) {
                        $listAllTrainerCourses = $listAllTrainerCourses  . '<div ' . $linkcss . '>' . '<div ' . $coursecss . '>' . $htmllinktocourse .  '  ' .  $linkViewOrphanedFiles . '  ' . $htmllinktocoursedeletion . ' ' . $roles . '<br>' . $duration . '</div></div>';
                        $countCoursesEditingTeacher++;
                    }
                    if ($isStudent) {
                        $listAllStudentCourses = $listAllStudentCourses . '<div ' . $linkcss . '>' . '<div ' . $coursecss . '>' . $htmllinktocourse .  '  ' . $roles . '<br>' . $duration . '</div></div>';
                        $countCoursesWithStudent++;
                    }
                    if ($isNoneditingTeacher) {
                        $listAllNoneditingTeacherCourses = $listAllNoneditingTeacherCourses
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
                        $countCoursesNoneditingTeacher++;
                    }

                    if (is_siteadmin() && $countCoursesAll <= $configHandler->getMax_for_siteadmin()) {
                        $listAllCourses = $listAllCourses  . '<div ' . $linkcss . '>' . '<div ' . $coursecss . '>' . $htmllinktocourse .  '  ' .  $linkViewOrphanedFiles . '  ' . $htmllinktocoursedeletion . ' ' . $roles . '<br>' . $duration . '</div></div>';
                        $countCoursesAll++;
                    }
                }

                $title = '';
                $title = get_string('blocktitle', 'block_course_list_advanced');
                if (is_siteadmin()) {
                    $title = 'Adminmodus';
                }
                $this->title = $title;
                // If we can update any course of the view all isn't hidden, show the view all courses link
                if ($allcourselink) {
                    $this->content->footer = "<a href=\"$CFG->wwwroot/course/index.php\">"
                        . get_string("fulllistofcourses")
                        . "</a> ...";
                }
            }
            if ($countCoursesEditingTeacher) {
                $this->content->items[] = '<div class="course_list_advanced">'
                    . $countCoursesEditingTeacher
                    . ' '
                    . get_string('headlineteacher', 'block_course_list_advanced')
                    . '</div>';
                $this->content->items[] = $listAllTrainerCourses . ' <br />';
            }
            if ($countCoursesWithStudent) {
                $this->content->items[] = '<div class="course_list_advanced">'
                    .  $countCoursesWithStudent
                    . ' '
                    . get_string('headlinestudent', 'block_course_list_advanced')
                    . '</div>';
                $this->content->items[] = $listAllStudentCourses . '<br />';
            }
            if ($countCoursesNoneditingTeacher) {
                $this->content->items[] = '<div class="course_list_advanced">' .  $countCoursesNoneditingTeacher . ' ' . get_string('headlinenoneditingteacher', 'block_course_list_advanced') . '</div>';
                $this->content->items[] = $listAllNoneditingTeacherCourses . '<br />';
            }

            if ($countCoursesAll) {
                $this->content->items[] = '<div class="course_list_advanced">'
                    .  $countCoursesAll . ' '
                    . get_string('headlinenallcourses', 'block_course_list_advanced')
                    . ' (max. '
                    . $configHandler->getMax_for_siteadmin()
                    . ')</div>';
                $this->content->items[] = $listAllCourses . '<br />';
            }

            $this->get_remote_courses();
            if ($this->content->items) { // make sure we don't return an empty list
                return $this->content;
            }
        }

        // User is not enrolled in any courses, show list of available categories or courses (if there is only one category).
        $topcategory = core_course_category::top();
        if ($topcategory->is_uservisible() && ($categories = $topcategory->get_children())) { // Check we have categories.
            if (count($categories) > 1 || (count($categories) === 1 && $DB->count_records('course') > 200)) {
                // Just print top level category links
                foreach ($categories as $category) {
                    $categoryname = $category->get_formatted_name();
                    $linkcss = $category->visible ? "" : " class=\"dimmed\" ";
                    $this->content->items[] = "<a $linkcss href=\"$CFG->wwwroot/course/index.php?categoryid=$category->id\">"
                        . $icon
                        . $categoryname
                        . "</a>";
                }
                // If we can update any course of the view all isn't hidden, show the view all courses link
                if ($allcourselink) {
                    $this->content->footer .= "<a href=\"$CFG->wwwroot/course/index.php\">" . get_string('fulllistofcourses') . '</a> ...';
                }
                $this->title = get_string('categories');
            } else {                          // Just print course names of single category
                $category = array_shift($categories);
                $courses = $category->get_courses();

                if ($courses) {
                    foreach ($courses as $course) {
                        // $coursecontext = context_course::instance($course->id);
                        $linkcss = $course->visible ? "" : " class=\"dimmed\" ";

                        $this->content->items[] = "<a $linkcss title=\""
                            . s($course->get_formatted_shortname()) . "\" " .
                            "href=\"$CFG->wwwroot/course/view.php?id=$course->id\">"
                            . $icon . $course->get_formatted_name() . "</a>";
                    }
                    // If we can update any course of the view all isn't hidden, show the view all courses link
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
                $this->content->items[] = "<a title=\""
                    . format_string($course->shortname, true)
                    . "\" "
                    . "href=\"{$CFG->wwwroot}/auth/mnet/jump.php?hostid={$course->hostid}&amp;wantsurl=/course/view.php?id={$course->remoteid}\">"
                    . $icon
                    . format_string(get_course_display_name_for_list($course)) . "</a>";
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
    public function applicable_formats()
    {
        global $CFG;
        $configHandler = new config_handler($CFG);
        return array(
            'site-index' => $configHandler->getIsallowedonfrontpage(),
            'my' => $configHandler->getIsallowedonmypage(),
            'course-view' => true
        );
    }

    /**
     * @return array returns all courses in this moodle
     */
    public function getAllCoursesBySelect(): array
    {
        global $DB;
        $query = "SELECT id, fullname, shortname, startdate, enddate, visible from {course}";
        $courselist = $DB->get_records_sql($query);
        return $courselist;
    }

    /**
     * @var $color string like #ff0000
     * @return string indicator for the role as html-code
     */
    public function createRoleIndicator($title, $shortcut, $color): string
    {
        return "<i class='text-info' data-toggle='tooltip' data-placement='bottom' title='$title' > <font color='$color'>$shortcut</font></i>";
    }
}
