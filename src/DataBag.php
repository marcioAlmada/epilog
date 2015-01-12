<?php

namespace Epilog;

class DataBag
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }

    public function get($key, $default = null)
    {
        return \igorw\get_in($this->data, $this->makePath($key), $default);
    }

    public function set($key, $value)
    {
        return $this->data = \igorw\assoc_in($this->data, $this->makePath($key), $value);
    }

    public function all()
    {
        return $this->data;
    }

    public function keys()
    {
        return array_keys($this->data);
    }

    public function values()
    {
        return array_values($this->data);
    }

    protected function makePath($key)
    {
        return explode('.', str_replace(' ', '.', $key));
    }
}
