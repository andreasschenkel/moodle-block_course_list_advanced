<?php
use PHPUnit\Framework\TestCase;
use course_list_advanced\classes;

class config_handler_testcase extends TestCase
{
    
    public function test_adminview_default_false(){
        $config = new stdClass;
        $config->block_course_list_advanced_adminview = 'own';
        $config->block_course_list_advanced_hideallcourseslink = false;
        
        $config_handler = new config_handler($config);

        //$this->assertFalse($config_handler->getAdminseesall());
        $this->assertEquals(false, $config_handler->getAdminseesall());
        $this->assertEquals(false, $config_handler->getHideallcourseslink());
    }

    public function test_adminview_default_true(){
        $config = new stdClass;
        $config->block_course_list_advanced_adminview = 'sdfsdfsdfsdf';
        $config->block_course_list_advanced_hideallcourseslink = true;
        
        $config_handler = new config_handler($config);

        //$this->assertFalse($config_handler->getAdminseesall());
        $this->assertEquals(true, $config_handler->getAdminseesall());
        $this->assertEquals(true, $config_handler->getHideallcourseslink());
    }

    public function test_adminview_hurzRumpelpumpel(){
        $config = new stdClass;
        $config->block_course_list_advanced_adminview = 'hurzRumpelpumpel'; 
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




