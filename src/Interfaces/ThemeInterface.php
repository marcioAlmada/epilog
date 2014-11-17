<?php

namespace Epilog\Interfaces;

interface ThemeInterface
{
    /**
     * @param string $key
     */
    public function get($key, $default = null);
    public function set($key, $value);
    public function all();
}
