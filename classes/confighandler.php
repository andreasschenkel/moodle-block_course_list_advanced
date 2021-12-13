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

class confighandler
{
    /**
     * @var bool
     */
    private $adminseesall = true;

    /**
     * @var bool
     */
    private $hideallcourseslink = false;

    /**
     * @var bool
     */
    private $showdeleteicon = false;

    /**
     * @var bool
     */
    private $isallowedonfrontpage = false;

    /**
     * @var bool
     */
    private $isallowedonmypage = false;

    /**
     * @var bool
     */
    private $usesphorphanedfiles = false;

    /**
     * @var int
     */
    private $maxforsiteadmin = 22;

    public function __construct(stdClass $config) {
        if (isset($config->block_course_list_advanced_adminview)) {
            $this->adminseesall = $config->block_course_list_advanced_adminview == 'own' ? false : true;
        }

        if (isset($config->block_course_list_advanced_hideallcourseslink)) {
            $this->hideallcourseslink = $config->block_course_list_advanced_hideallcourseslink ? true : false;
        }

        if (isset($config->block_course_list_advanced_showdeleteicon)) {
            $this->showdeleteicon = $config->block_course_list_advanced_showdeleteicon ? true : false;
        }

        if (isset($config->block_course_list_advanced_isallowedonfrontpage)) {
            $this->isallowedonfrontpage = $config->block_course_list_advanced_isallowedonfrontpage ? true : false;
        }

        if (isset($config->block_course_list_advanced_isallowedonmypage)) {
            $this->isallowedonmypage = $config->block_course_list_advanced_isallowedonmypage ? true : false;
        }

        if (isset($config->block_course_list_advanced_usesphorphanedfiles)) {
            $this->usesphorphanedfiles = $config->block_course_list_advanced_usesphorphanedfiles ? true : false;
        }

        if (isset($config->block_course_list_advanced_maxforsiteadmin)) {
            $this->maxforsiteadmin = $config->block_course_list_advanced_maxforsiteadmin;
        }

    }

    /**
     * Get the value of adminview
     */
    public function getadminseesall(): bool {
        return $this->adminseesall;
    }

    /**
     * Get the value of hideallcourseslink
     */
    public function gethideallcourseslink(): bool {
        return $this->hideallcourseslink;
    }

    /**
     * Get the value of showdeleteicon
     */
    public function get_showdeleteicon(): bool {
        return $this->showdeleteicon;
    }

    /**
     * Get the value of isallowedonfrontpage
     */
    public function get_isallowedonfrontpage(): bool {
        return $this->isallowedonfrontpage;
    }

    /**
     * Get the value of isallowedonmypage
     */
    public function get_isallowedonmypage(): bool {
        return $this->isallowedonmypage;
    }

    /**
     * Get the value of usesphorphanedfiles
     * @return  bool
     */
    public function get_usesphorphanedfiles(): bool {
        return $this->usesphorphanedfiles;
    }

    /**
     * Get the value of maxforsiteadmin
     *
     * @return  int
     */
    public function get_max_for_siteadmin(): int {
        return $this->maxforsiteadmin;
    }
}
