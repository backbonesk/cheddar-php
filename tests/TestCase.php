<?php

namespace Cheddar;

/**
 * Base class for Cheddar test cases.
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::__construct();
    }
}
