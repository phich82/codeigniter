<?php

abstract class Page
{
    /**
     * @var RemoteWebDriver
     */
    protected $driver;

    /**
     * Constructor
     */
    public function __construct($driver)
    {
        $this->driver = $driver;
    }
}
