<?php
namespace Tests\Automation\Web\Traits;

use Facebook\WebDriver\WebDriverBy;
/**
 * WebDriverCommon
 */
trait WebDriverCommon
{
    protected function login($username, $password)
    {
        $this->driver->findElement(WebDriverBy::cssSelector('a.user-enter'))->click();

        $this->assertElementAppeared(
            WebDriverBy::id('modalLogin'),
            'Убедиться, что открылась форма входа'
        );

        $this->driver->findElement(WebDriverBy::id('loginform-email'))->sendKeys($username);
        $this->driver->findElement(WebDriverBy::id('loginform-password'))->sendKeys($password);
        $this->driver->findElement(WebDriverBy::cssSelector('#login-form button[type=submit]'))->click();

        $this->assertElementAppeared(
            WebDriverBy::cssSelector('span.user-welcome'),
            'Убедиться, что юзер залогинился'
        );
    }
}
