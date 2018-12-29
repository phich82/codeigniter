<?php

require_once 'MyTestCase.php';
require_once 'pages/StartPage.php';

class SeleniumTest extends MyTestCase
{
    public function testTitle()
    {
        $startPage = new StartPage($this);
        $startPage->open();

        $this->assertEquals('Welcome to CodeIgniter', $this->title());
    }
}
