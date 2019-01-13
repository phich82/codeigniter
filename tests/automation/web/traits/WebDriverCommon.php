<?php
namespace tests\automation\web\traits;

use Facebook\WebDriver\WebDriverBy;
/**
 * WebDriverCommon
 */
trait WebDriverCommon
{
    protected function login($username, $password)
    {
        $this->webDriver->findElement(\WebDriverBy::cssSelector('a.user-enter'))->click();
        $this->assertElementAppeared(
            \WebDriverBy::id('modalLogin'),
            'Убедиться, что открылась форма входа'
        );
        $this->webDriver->
                findElement(\WebDriverBy::id('loginform-email'))->
                sendKeys($username);
        $this->webDriver->
                findElement(\WebDriverBy::id('loginform-password'))->
                sendKeys($password);
        $this->webDriver->
                findElement(\WebDriverBy::cssSelector('#login-form button[type=submit]'))->
                click();
        $this->assertElementAppeared(
            \WebDriverBy::cssSelector('span.user-welcome'),
            'Убедиться, что юзер залогинился'
        );
    }
}
