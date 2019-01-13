<?php

require_once 'traits/WebDriverAssertions.php';
require_once 'traits/WebDriverDevelop.php';

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use tests\automation\web\traits\WebDriverDevelop;
use tests\automation\web\traits\WebDriverAssertions;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

class GitHubTest extends \PHPUnit\Framework\TestCase
{
    use WebDriverAssertions;
    use WebDriverDevelop;

    protected $url = 'http://github.com';
    /**
     * @var \RemoteWebDriver
     */
    protected $webDriver;

	public function setUp()
    {
        $capabilities = array(WebDriverCapabilityType::BROWSER_NAME => 'chrome');
        $this->webDriver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
    }
    public function tearDown()
    {
        $this->webDriver->quit();
    }
    public function testGitHubHome()
    {
        $this->webDriver->get($this->url);
        // checking that page title contains word 'GitHub'
        $this->assertContains('GitHub', $this->webDriver->getTitle());
    }
    public function testSearch()
    {
        $this->webDriver->get($this->url . '/search');
        // find search field by its id
        $search = $this->webDriver->findElement(WebDriverBy::cssSelector('.input-block'));
        $search->click();
        // typing into field
        $this->webDriver->getKeyboard()->sendKeys('php-webdriver');
        // pressing "Enter"
        $this->webDriver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
        $firstResult = $this->webDriver->findElement(
            // select link for php-webdriver
            WebDriverBy::partialLinkText('facebook')
        );
        $firstResult->click();
        // we expect that facebook/php-webdriver was the first result
        $this->assertContains("php-webdriver",$this->webDriver->getTitle());
        $this->assertEquals('https://github.com/facebook/php-webdriver', $this->webDriver->getCurrentURL());
        $this->assertElementNotFound(WebDriverBy::className('name'));
        // $this->waitForUserInput();
    }
}
