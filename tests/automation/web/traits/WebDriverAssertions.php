<?php
namespace Tests\Automation\Web\Traits;

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
            $element = $this->driver->wait(10)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated($by)
            );
        } catch (Exception $e) {
            $element = null;
        }

        $this->assertInstanceOf('WebDriverElement', $element, $message);
    }

    protected function assertElementNotFound($by)
    {
        $elements = $this->driver->findElements($by);
        if (count($elements)) {
            $this->fail("Unexpectedly element was found");
        }
        // increment assertion counter
        $this->assertTrue(true);
    }
}
