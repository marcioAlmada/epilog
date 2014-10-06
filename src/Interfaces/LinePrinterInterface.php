<?php

namespace Epilog\Interfaces;

interface LinePrinterInterface
{
    public function __construct(LineParserInterface $parser, ThemeInterface $theme, $invert = false);

    public function invert();

    public function format($raw);

    public function pad($input, $pad_length, $pad_style = STR_PAD_RIGHT, $encoding = 'UTF-8');

    public function clearLinesUp($lines = 1);

    public function clearLine();

    public function clearAll();

    public function unformat();

    public function getScreenWidth();
}
