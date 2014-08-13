<?php

namespace Epilog;

/**
 * FileMonitorTest
 *
 * @group support
 */
class FileMonitorTest extends \PHPUnit_Framework_TestCase
{

    protected $fixture;

    public function setUp()
    {
        $this->fixture = tempnam(sys_get_temp_dir(), 'epilog-');
    }

    /**
     * @requires extension inotify
     */
    public function testWatchReadUnwatch()
    {
        $monitor = new FileMonitor();
        $descriptor = $monitor->watch($this->fixture);
        $this->assertNull($monitor->read());
        file_put_contents($this->fixture, "bump\n", FILE_APPEND);
        $this->assertEquals(IN_CLOSE_WRITE, $monitor->read()[0]['mask']);
        $this->assertNull($monitor->read());
        $monitor->unwatch($descriptor);
        $this->assertEquals(IN_IGNORED, $monitor->read()[0]['mask']);
        file_put_contents($this->fixture, "bump\n", FILE_APPEND);
        $this->assertNull($monitor->read());
    }
}
