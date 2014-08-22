<?php

namespace Epilog;

use Epilog\Interfaces\MonitorInterface;

class FakeMonitor implements MonitorInterface
{
    public function read()
    {
        return true;
    }

    public function watch($path, $mask) {}

    public function unwatch($descriptor) {}
}
