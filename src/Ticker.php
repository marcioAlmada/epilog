<?php

namespace Epilog;

class Ticker
{
    protected $states;

    public function __construct(array $states = ['•••<', '•<<<', '<<<<', '<<<•', '<•••', '••••'])
    {
        end($states);
        $this->states = $states;
    }

    public function __toString()
    {
        return next($this->states) ?: reset($this->states);
    }
}
