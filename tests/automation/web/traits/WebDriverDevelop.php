<?php
namespace tests\automation\web\traits;

trait WebDriverDevelop {
    protected function waitForUserInput()
    {
        if (trim(fgets(fopen("php://stdin","r"))) != chr(13)) {
            return;
        }
    }
}
