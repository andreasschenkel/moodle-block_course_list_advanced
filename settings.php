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
 * Course list block settings
 *
 * @package    block_course_list
 * @copyright  2007 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $options = array('all' => get_string('allcourses', 'block_course_list_advanced'),
        'own' => get_string('owncourses', 'block_course_list_advanced'));

    $settings->add(new admin_setting_configselect(
        'block_course_list_advanced_adminview',
        get_string('adminview', 'block_course_list_advanced'),
        get_string('configadminview', 'block_course_list_advanced'),
        'all',
        $options
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_course_list_advanced_hideallcourseslink',
        get_string('hideallcourseslink', 'block_course_list_advanced'),
        get_string('confighideallcourseslink', 'block_course_list_advanced'),
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_course_list_advanced_showdeleteicon',
        get_string('showdeleteicon', 'block_course_list_advanced'),
        get_string('configshowdeleteicon', 'block_course_list_advanced'),
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_course_list_advanced_usesphorphanedfiles',
        get_string('usesphorphanedfiles', 'block_course_list_advanced'),
        get_string('configusesphorphanedfiles', 'block_course_list_advanced'),
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_course_list_advanced_isallowedonfrontpage',
        get_string('isallowedonfrontpage', 'block_course_list_advanced'),
        get_string('configisallowedonfrontpage', 'block_course_list_advanced'),
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_course_list_advanced_isallowedonmypage',
        get_string('isallowedonmypage', 'block_course_list_advanced'),
        get_string('configisallowedonmypage', 'block_course_list_advanced'),
        0
    ));

    $options = array(5 => '5', 10 => '10', 20 => '20', 30 => '30', 40 => '40', 50 => '50', 100 => '100', 10000 => '10000');
    $settings->add(new admin_setting_configselect(
        'block_course_list_advanced_maxforsiteadmin',
        get_string('maxforsiteadmin', 'block_course_list_advanced'),
        get_string('configmaxforsiteadmin', 'block_course_list_advanced'),
        '30',
        $options
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_course_list_advanced_showcourseswithguestrole',
        get_string('showcourseswithguestrole', 'block_course_list_advanced'),
        get_string('configshowcourseswithguestrole', 'block_course_list_advanced'),
        0
    ));

}
