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

// Variante 1 OHNE use.
// Variante 2 MIT use course_list_advanced.

namespace block_course_list_advanced\core_confighandler_testcase;
defined('MOODLE_INTERNAL') || die();
// Variante 3.

use block_course_list_advanced\confighandler;

class core_confighandler_testcase extends \advanced_testcase
{

    public function test_adminview_default_false() {
        $config = new stdClass;
        $config->block_course_list_advanced_adminview = 'own';
        $config->block_course_list_advanced_hideallcourseslink = false;

        // Variante 1 $confighandler = new \course_list_advanced\confighandler($config).
        // Variante 2 $confighandler = new course_list_advanced\confighandler($config).
        // Variante 3.
        $confighandler = new confighandler($config);

        $this->assertEquals(false, $confighandler->getadminseesall());
        $this->assertEquals(false, $confighandler->gethideallcourseslink());
    }

    public function test_adminview_default_true() {
        $config = new stdClass;
        $config->block_course_list_advanced_adminview = 'wennHierNicht_own_steht';
        $config->block_course_list_advanced_hideallcourseslink = true;

        $confighandler = new confighandler($config);

        $this->assertEquals(true, $confighandler->getadminseesall());
        $this->assertEquals(true, $confighandler->gethideallcourseslink());
    }

    public function test_adminview_irgendwas() {
        $config = new stdClass;
        $config->block_course_list_advanced_adminview = 'irgendwas';

        $confighandler = new confighandler($config);

        $this->assertEquals(true, $confighandler->getadminseesall());
    }

    public function test_adminview_empty() {
        $config = new stdClass;
        $config->block_course_list_advanced_adminview = '';

        $confighandler = new confighandler($config);

        $this->assertEquals(true, $confighandler->getadminseesall());
    }

    public function test_adminview_unset() {
        $config = new stdClass;

        $confighandler = new confighandler($config);

        $this->assertEquals(true, $confighandler->getadminseesall());
    }
}
