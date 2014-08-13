<?php

namespace Epilog;

/**
 * TickerTest
 *
 * @group support
 */
class TickerTest extends \PHPUnit_Framework_TestCase
{
    public function testSequence()
    {
        $ticker = new Ticker(['0', '1', '2', '3']);
        $this->assertEquals('0', $ticker->__toString());
        $this->assertEquals('1', $ticker->__toString());
        $this->assertEquals('2', $ticker->__toString());
        $this->assertEquals('3', $ticker->__toString());
        $this->assertEquals('0', $ticker->__toString());
    }
}
