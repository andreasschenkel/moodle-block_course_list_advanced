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

namespace block_course_list_advanced;
defined('MOODLE_INTERNAL') || die();
use stdClass;

class htmlhelper
{
   /**
     * @param int $counter          number of courses that where found the user has this role
     * @param string $headline      Title of the block that shows the role
     * @param string $courselist    html-code including the list of courses to be displayed
     * @param string $additional
     * 
     * @return string html-div-code with the heading and a list of courses
    */
    public static function generate_role_block(int $counter, string $headline, string $courselist, string $additional) : string{
         $additionaltext = '';
         if (isset($additional)) {
             $additionaltext = $additional;
         }
         $roleblock = '<div class="roleblock">'
                .   "<div class='$headline'>"
                .       $counter
                .       ' '
                .       get_string("$headline", 'block_course_list_advanced')
                .       $additionaltext
                .   '</div> '  
                . $courselist 
                . "</div>";
        return $roleblock;
    }
}
