<?php

namespace Tests;

use PHPUnit_Framework_TestCase;

/**
 * Class ItemsControllerTest
 *
 * @package Tests
 */
class TestCase extends PHPUnit_Framework_TestCase
{
    /** @var MockContainer */
    private $mock;

    public function mock()
    {
        if ($this->mock === null) {
            $this->mock = new MockContainer();
            $this->mock->setTestCase($this);
        }

        return $this->mock;
    }

    public function tearDown()
    {
        $this->mock = null;
    }
}