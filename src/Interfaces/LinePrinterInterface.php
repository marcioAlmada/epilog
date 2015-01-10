<?php

namespace Epilog\Interfaces;

interface LinePrinterInterface
{
    public function __construct(LineParserInterface $parser, ThemeInterface $theme, $invert = false);

    /**
     * @return boolean
     */
    public function invert();

    /**
     * @return null|string
     */
    public function format($raw);

    /**
     * @param integer $pad_length
     *
     * @return string
     */
    public function pad($input, $pad_length, $pad_style = STR_PAD_RIGHT, $encoding = 'UTF-8');

    public function clearLinesUp($lines = 1);

    /**
     * @return string
     */
    public function clearLine();

    /**
     * @return string
     */
    public function clearAll();

    /**
     * @return string
     */
    public function unformat();

    /**
     * @return integer
     */
    public function getScreenWidth();
}
