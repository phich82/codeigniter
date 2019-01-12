<?php

require_once 'Page.php';

class StartPage extends Page
{
    /**
     * Access by url
     */
    public function open($url = null)
    {
        $this->driver->url($url ?: $this->driver->urlRoot);
    }
}
