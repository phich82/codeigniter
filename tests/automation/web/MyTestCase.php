<?php


class MyTestCase extends PHPUnit_Extensions_Selenium2TestCase
{
    protected $host = 'localhost';
    protected $port = 8888;
    protected $hostSelenium = '';
    protected $portSelenium = 4444;
    public $browser = 'chrome';
    public $urlRoot;

    public function setUp()
    {
        $this->urlRoot = 'http://'.$this->host.':'.$this->port;

        $this->setHost($this->hostSelenium);
        $this->setPort($this->portSelenium);
        $this->setBrowserUrl($this->urlRoot);
        $this->setBrowser($this->browser);
    }

    public function tearDown()
    {
        $this->stop();
    }
}
