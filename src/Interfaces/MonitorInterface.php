<?php

namespace Epilog\Interfaces;

interface MonitorInterface
{
    public function read();
    public function watch($path, $mask);
    public function unwatch($descriptor);
}
