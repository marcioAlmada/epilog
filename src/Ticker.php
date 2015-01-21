<?php

namespace Epilog;

use InfiniteIterator, ArrayIterator;

class Ticker extends InfiniteIterator
{
    public function __construct(array $states = ['•••<', '•<<<', '<<<<', '<<<•', '<•••', '••••'])
    {
        parent::__construct(new ArrayIterator($states));
        $this->rewind();
    }

    public function __toString()
    {   
        return $this->current() . $this->next();
    }
}
