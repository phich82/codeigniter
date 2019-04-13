<?php

require_once 'MyTestCaseSelenium.php';
require_once 'pages/StartPage.php';

class SeleniumTest extends MyTestCaseSelenium
{
    public function testTitle()
    {
        $startPage = new StartPage($this);
        $startPage->open();

        $this->assertEquals('Welcome to CodeIgniter', $this->title());
    }
}
