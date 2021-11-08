<?php

/**
 * Course list advanced block.
 *
 * @package    
 * @copyright  
 * @author     Andreas Schenkel - Schulportal Hessen 2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_course_list_advanced;
use stdClass;

class config_handler
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

    public function __construct(stdClass $config)
    {

        if (isset($config->block_course_list_advanced_adminview)) {
            $this->adminseesall = $config->block_course_list_advanced_adminview == 'own' ? false : true;
        }

        if (isset($config->block_course_list_advanced_hideallcourseslink)) {
            $this->hideallcourseslink = $config->block_course_list_advanced_hideallcourseslink == true ? true : false;
        }

        if (isset($config->block_course_list_advanced_showdeleteicon)) {
            $this->showdeleteicon = $config->block_course_list_advanced_showdeleteicon == true ? true : false;
        }

        if (isset($config->block_course_list_advanced_isallowedonfrontpage)) {
            $this->isallowedonfrontpage = $config->block_course_list_advanced_isallowedonfrontpage == true ? true : false;
        }

        if (isset($config->block_course_list_advanced_isallowedonmypage)) {
            $this->isallowedonmypage = $config->block_course_list_advanced_isallowedonmypage == true ? true : false;
        }

        if (isset($config->block_course_list_advanced_usesphorphanedfiles)) {
            $this->usesphorphanedfiles = $config->block_course_list_advanced_usesphorphanedfiles == true ? true : false;
        }

    }



    /**
     * Get the value of adminview
     */
    public function getAdminseesall(): bool
    {
        return $this->adminseesall;
    }

    /**
     * Get the value of hideallcourseslink
     */
    public function getHideallcourseslink(): bool
    {
        return $this->hideallcourseslink;
    }


    /**
     * Get the value of showdeleteicon
     */
    public function getShowdeleteicon(): bool
    {
        return $this->showdeleteicon;
    }

    /**
     * Get the value of isallowedonfrontpage
     */
    public function getIsallowedonfrontpage(): bool
    {
        return $this->isallowedonfrontpage;
    }


    /**
     * Get the value of isallowedonmypage
     */
    public function getIsallowedonmypage(): bool
    {
        return $this->isallowedonmypage;
    }

    


    /**
     * Get the value of usesphorphanedfiles
     *
     * @return  bool
     */ 
    public function getUsesphorphanedfiles()
    {
        return $this->usesphorphanedfiles;
    }
}
