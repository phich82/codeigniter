<?php

namespace Tests\Automation\Web;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;


class MyTestCaseWeb extends \PHPUnit\Framework\TestCase
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

    /**
     * setUp
     *
     * @return void
     */
    public function setUp()
    {
        if (empty($this->url)) {
            $this->url = $this->protocol.'://'.$this->host.':'.$this->port;
        }
        $urlRemote = $this->protocolRemote.'://'.$this->hostRemote.':'.$this->portRemote.'/wd/hub';

        $capabilities = [WebDriverCapabilityType::BROWSER_NAME => $this->browser];
        $this->driver = RemoteWebDriver::create($urlRemote, $capabilities);
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown()
    {
        $this->driver->quit();
    }

    /**
     * Open page with the given url
     *
     * @param  string $path
     *
     * @return void
     */
    public function open($path = null)
    {
        $this->driver->get($this->route($path));
    }

    /**
     * Create the url by the given path
     *
     * @param  string $path
     *
     * @return string
     */
    public function route($path = null)
    {
        if (is_string($path) && strtolower(substr($path, 0, 4)) == 'http') {
            return $path;
        }
        return !empty($path) ? rtrim($this->url, '/').'/'.ltrim($path, '/') : $this->url;
    }
}
