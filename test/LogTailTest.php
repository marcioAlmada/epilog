<?php

namespace Epilog;

use org\bovigo\vfs\vfsStream as Vfs;

/**
 * LogTailTest
 *
 * @group support
 */
class LogTailTest extends \PHPUnit_Framework_TestCase
{

    protected $tail;

    protected $log;

    public function setUp()
    {
        $dir = Vfs::setup('logs');
        $this->log = Vfs::newFile('fake.log')->at($dir)->withContent(
            "line 1\nline 2\n"
        );
        $this->tail = new LogTail($this->log->url());
    }

    public function testLogTail()
    {
        $this->tail->seekLastLineRead();
        $this->assertTrue($this->tail->eof());
        file_put_contents($this->log->url(), "line 3\n", FILE_APPEND);
        $this->assertTrue($this->tail->eof());
    }
}
