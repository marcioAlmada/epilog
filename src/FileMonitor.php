<?php

namespace Epilog;

use Epilog\Interfaces\MonitorInterface;
use InvalidArgumentException;

class FileMonitor implements MonitorInterface
{
    protected $handler;

    const ACCESS = IN_ACCESS;

    const MODIFY = IN_MODIFY;

    const ATTRIB = IN_ATTRIB;

    const CLOSE_WRITE = IN_CLOSE_WRITE;

    const CLOSE_NOWRITE = IN_CLOSE_NOWRITE;

    const OPEN = IN_OPEN;

    const MOVED_TO = IN_MOVED_TO;

    const MOVED_FROM = IN_MOVED_FROM;

    const CREATE = IN_CREATE;

    const DELETE = IN_DELETE;

    const DELETE_SELF = IN_DELETE_SELF;

    const MOVE_SELF = IN_MOVE_SELF;

    const CLOSE = IN_CLOSE;

    const MOVE = IN_MOVE;

    const ALL_EVENTS = IN_ALL_EVENTS;

    const UNMOUNT = IN_UNMOUNT;

    const Q_OVERFLOW = IN_Q_OVERFLOW;

    const IGNORED = IN_IGNORED;

    const ISDIR = IN_ISDIR;

    const ONLYDIR = IN_ONLYDIR;

    const DONT_FOLLOW = IN_DONT_FOLLOW;

    const MASK_ADD = IN_MASK_ADD;

    const ONESHOT = IN_ONESHOT;

    public function __construct()
    {
        $this->handler = inotify_init();
    }

    public function watch($path, $mask = self::CLOSE_WRITE)
    {
        if (! is_readable($path))
            throw new InvalidArgumentException("Path '{$path}' not found.");

        return inotify_add_watch($this->handler, $path, $mask);
    }

    public function read()
    {
        if($this->getQueue())

            return inotify_read($this->handler);
    }

    public function unwatch($descriptor)
    {
        return inotify_rm_watch($this->handler, $descriptor);
    }

    protected function getQueue()
    {
        return inotify_queue_len($this->handler);
    }
}
