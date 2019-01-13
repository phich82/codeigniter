<?php
namespace tests\automation\web\traits;

use Exception;
use Facebook\WebDriver\WebDriverExpectedCondition;

/**
 * WebDriverAssertions
 */
trait WebDriverAssertions
{
    protected function assertElementAppeared($by, $message = '')
    {
        try {
            $element = $this->webDriver->wait(10)->until(
                \WebDriverExpectedCondition::visibilityOfElementLocated($by)
            );
        } catch (\Exception $e) {
            $element = null;
        }

        $this->assertInstanceOf('WebDriverElement', $element, $message);
    }

    protected function assertElementNotFound($by)
    {
        $els = $this->webDriver->findElements($by);
        if (count($els)) {
            $this->fail("Unexpectedly element was found");
        }
        // increment assertion counter
        $this->assertTrue(true);
    }
}
