<?php

namespace Epilog;

use Epilog\Interfaces\TailInterface;
use SplFileObject;

class LogTail extends SplFileObject implements TailInterface
{
    protected $index;

    public function __construct($filename)
    {
        parent::__construct($filename, 'r');
        $this->setFlags(self::SKIP_EMPTY);
        $lineCount = $this->getLineCount();
        $this->index = ($lineCount > 10) ? ($lineCount - 10) : $lineCount;
    }

    public function seekLastLineRead()
    {
        return parent::seek($this->index);
    }

    protected function getLineCount()
    {
        $this->fastForward();

        return $this->key();
    }

    protected function fastForward()
    {
        while(! $this->eof()) $this->fgets();
    }

    public function eof()
    {
        if($eof = parent::eof())
            $this->index = ($this->key() > 1) ? ($this->key() - 1) : $this->key();

        return $eof;
    }
}
