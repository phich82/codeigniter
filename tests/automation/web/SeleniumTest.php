<?php

require_once 'MyTestCaseWeb.php';
require_once 'pages/StartPage.php';

class SeleniumTest extends MyTestCaseWeb
{
    public function testTitle()
    {
        $startPage = new StartPage($this);
        $startPage->open();

        $this->assertEquals('Welcome to CodeIgniter', $this->title());
    }
}
