<?php

namespace Epilog;

/**
 * StreamReaderTest
 *
 * @group support
 */
class InputReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testReadBlockAndUnblock()
    {
        $stdinReader = new InputReader(
            "data://,BLOCKING\nA\nUNBLOCKING\nB\nBLOCKING\nC");
        $this->assertEquals('BLOCKING', $stdinReader->readLine());
        $this->assertEquals('A', $stdinReader->readChar());
        $this->assertEquals(PHP_EOL, $stdinReader->readChar());
        $stdinReader->block(false);
        $this->assertEquals('UNBLOCKING', $stdinReader->readLine());
        $this->assertEquals('B', $stdinReader->readChar());
        $this->assertEquals(PHP_EOL, $stdinReader->readChar());
        $stdinReader->block(true);
        $this->assertEquals('BLOCKING', $stdinReader->readLine());
        $this->assertEquals('C', $stdinReader->readChar());
        $this->assertFalse($stdinReader->readChar());
        $this->assertFalse($stdinReader->readLine());
    }

}
