<?php

require_once 'traits/WebDriverAssertions.php';
require_once 'traits/WebDriverDevelop.php';
require_once 'MyTestCaseWD.php';

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use tests\automation\web\traits\WebDriverDevelop;
use tests\automation\web\traits\WebDriverAssertions;

class GitHubNoNSTest extends MyTestCaseWD
{
    use WebDriverAssertions;
    use WebDriverDevelop;

    protected $url = 'http://github.com';

    public function testGitHubHome()
    {
        $this->open();
        // checking that page title contains word 'GitHub'
        $this->assertContains('GitHub', $this->driver->getTitle());
    }
    public function testSearch()
    {
        $this->open($this->url.'/search');

        // find search field by its class & click on it
        $this->driver->findElement(WebDriverBy::cssSelector('.input-block'))->click();
        // typing into this field
        $this->driver->getKeyboard()->sendKeys('php-webdriver');
        // pressing "Enter"
        $this->driver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
        // select link that it contains a 'facebook' text & click on it (get the first searched result)
        $this->driver->findElement(WebDriverBy::partialLinkText('facebook'))->click();

        // we expect that facebook/php-webdriver was the first result
        $this->assertContains("php-webdriver", $this->driver->getTitle());
        $this->assertEquals('https://github.com/facebook/php-webdriver', $this->driver->getCurrentURL());
        $this->assertElementNotFound(WebDriverBy::className('name'));
    }
}
