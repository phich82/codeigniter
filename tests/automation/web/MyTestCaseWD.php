<?php

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;


class MyTestCaseWD extends \PHPUnit\Framework\TestCase
{
    protected $protocol = 'http';
    protected $host = 'localhost';
    protected $port = 8888;

    protected $protocolRemote = 'http';
    protected $hostRemote = 'localhost';
    protected $portRemote = 4444;

    protected $url;
    protected $browser = 'chrome';

    /**
     * @var \RemoteWebDriver
     */
    protected $driver;

    public function setUp()
    {
        if (empty($this->url)) {
            $this->url = $this->protocol.'://'.$this->host.':'.$this->port;
        }
        $urlRemote = $this->protocolRemote.'://'.$this->hostRemote.':'.$this->portRemote.'/wd/hub';

        $capabilities = [WebDriverCapabilityType::BROWSER_NAME => $this->browser];
        $this->driver = RemoteWebDriver::create($urlRemote, $capabilities);
    }

    public function tearDown()
    {
        $this->driver->quit();
    }

    public function open($url = null)
    {
        $this->driver->get($url ?: $this->url);
    }
}
