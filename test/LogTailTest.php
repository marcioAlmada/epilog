<?php

namespace Epilog;

/**
 * LogTailTest
 *
 * @group support
 */
class LogTailTest extends \PHPUnit_Framework_TestCase
{

    protected $fixture;

    public function setUp()
    {
        $this->fixture = tempnam(sys_get_temp_dir(), 'epilog-');
    }

    public function testLogTailWithEmptyFile()
    {
        $log = new LogTail($this->fixture, 'r');
        $this->assertEquals(0, $log->key());
        $log->seekLastLineRead();
        $this->assertEquals(0, $log->key());
        $this->assertEquals('', $log->fgets());
        $this->assertEquals(0, $log->key());
        $this->assertTrue($log->eof());
    }

    public function testLogTailWithShortFile()
    {
        $this->growLog("line {i}", 3);
        $log = new LogTail($this->fixture, 'r');
        $log->seekLastLineRead();
        $this->assertEquals(3, $log->key());
        $this->assertTrue($log->eof());
    }

    public function testLogTailWithLongerFile()
    {
        $this->growLog("line {i}", 11);
        $log = new LogTail($this->fixture, 'r');
        $log->seekLastLineRead();
        $this->assertEquals(1, $log->key());
        $log->fgets();
        $log->fgets();
        $log->fgets();
        $log->fgets();
        $log->fgets();
        $log->fgets();
        $log->fgets();
        $log->fgets();
        $this->assertEquals("line 10\n", $log->fgets());
        $this->assertEquals('', $log->fgets());
        $this->assertTrue($log->eof());
        
        $this->growLog('bump!');
        $log->seekLastLineRead();
        $this->assertFalse($log->eof());
        $this->assertEquals("bump!\n", $log->fgets());
        $this->assertEquals('', $log->fgets());
        $this->assertTrue($log->eof());
    }

    public function testLogTailWhenFileIsTruncated()
    {
        $this->growLog("line {i}", 100);
        $log = new LogTail($this->fixture, 'r');
        $log->seekLastLineRead();
        $this->assertEquals(90, $log->key());
        $this->truncateLog();
        $log->seekLastLineRead();
        $this->assertEquals(1, $log->key());
    }

    protected function growLog($text, $times = 1)
    {
        for ($i=0; $i < $times; $i++)
            file_put_contents(
                $this->fixture, str_replace('{i}', $i, $text) . "\n", FILE_APPEND);
    }

    protected function truncateLog()
    {
        file_put_contents($this->fixture, "\n");
    }
}
