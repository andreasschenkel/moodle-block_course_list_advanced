<?php
// Variante 1 OHNE use
// Variante 2 MIT use course_list_advanced;
use block_course_list_advanced\config_handler; // Variante 3

class core_config_handler_testcase extends \advanced_testcase
{
    
    public function test_adminview_default_false(){
        $config = new stdClass;
        $config->block_course_list_advanced_adminview = 'own';
        $config->block_course_list_advanced_hideallcourseslink = false;
        
        //Variante 1 $config_handler = new \course_list_advanced\config_handler($config);
        //Variante 2 $config_handler = new course_list_advanced\config_handler($config);
        $config_handler = new config_handler($config); // Variante 3

        $this->assertEquals(false, $config_handler->getAdminseesall());
        $this->assertEquals(false, $config_handler->getHideallcourseslink());
    }

    public function test_adminview_default_true(){
        $config = new stdClass;
        $config->block_course_list_advanced_adminview = 'wennHierNicht_own_steht';
        $config->block_course_list_advanced_hideallcourseslink = true;
        
        $config_handler = new config_handler($config);

        $this->assertEquals(true, $config_handler->getAdminseesall());
        $this->assertEquals(true, $config_handler->getHideallcourseslink());
    }

    public function test_adminview_irgendwas(){
        $config = new stdClass;
        $config->block_course_list_advanced_adminview = 'irgendwas'; 

        $config_handler = new config_handler($config);

        $this->assertEquals(true, $config_handler->getAdminseesall());
    }

    public function test_adminview_empty(){
        $config = new stdClass;
        $config->block_course_list_advanced_adminview = ''; 

        $config_handler = new config_handler($config);
        
        $this->assertEquals(true, $config_handler->getAdminseesall());
    }

    public function test_adminview_unset(){
        $config = new stdClass;

        $config_handler = new config_handler($config);
        
        $this->assertEquals(true, $config_handler->getAdminseesall());
    }


}




