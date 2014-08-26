<?php

namespace Epilog;

use Symfony\Component\Yaml\Yaml;
use InvalidArgumentException;

class Theme extends DataBag
{
    public function __construct($themeFile)
    {
        if(! is_readable($themeFile))
            throw new InvalidArgumentException('Invalid theme given.');

        $themes = [];
        $themes[] = $theme = Yaml::parse($themeFile);
        $themeDir = dirname($themeFile);

        while ($theme = \igorw\get_in($theme, ['theme', 'extends'], false))
            $themes[] = $theme = Yaml::parse($themeDir . '/' . $theme . '.yml');

        $this->data = call_user_func_array(
            'array_replace_recursive', array_reverse($themes))['theme'];
    }
}
