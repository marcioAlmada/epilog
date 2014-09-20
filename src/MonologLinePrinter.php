<?php

namespace Epilog;

use Epilog\Interfaces\LineParserInterface;
use Epilog\Interfaces\PrinterInterface;

class MonologLinePrinter implements PrinterInterface
{
    protected $parser;
    protected $invert;
    protected $theme;

    public function __construct(LineParserInterface $parser, $theme, $invert = false)
    {
        $this->parser = $parser;
        $this->invert = $invert;
        $this->theme  =  new Theme($theme);
    }

    public function invert()
    {
        return $this->invert = ! $this->invert;
    }

    public function format($raw)
    {
        $raw = trim($raw);
        if(empty($raw)) return;
        $line = '';
        $data = $this->parser->parse($raw);
        $line = $raw . "\n";
        if ($data) {
            $data = new DataBag($data);
            $data->{'{date}'}    = $data->{'{date}'}->format($this->theme->{'date format'});
            $data->{'{extra}'}   = json_encode($data->{'{extra}'});
            $data->{'{context}'} = json_encode($data->{'{context}'});
            $data->{'{date}'}    = $this->theme->{'date prepend'} . $data->{'{date}'} . $this->theme->{'date append'};
            $data->{'{logger}'}  = $this->theme->{'logger prepend'} . $data->{'{logger}'} . $this->theme->{'logger append'};
            $data->{'{message}'} = $this->theme->{'message prepend'} . $data->{'{message}'} . $this->theme->{'message append'};
            $levelFormat = $this->theme->get('level ' . $data->{'{level}'}, $this->theme->{'level DEFAULT'});
            $data->{'{level}'} =
                $this->theme->{'level prepend'}
                    . $levelFormat['prepend']
                    . $this->pad($data->{'{level}'}, $this->theme->{'level pad'}, constant($this->theme->{'level pad-type'}))
                    . $levelFormat['append']
                    . $this->theme->{'level append'};
            $line = (($this->invert) ? "\e[7m" : '')
                    . $this->theme->{'prepend'}
                    . $this->pad(
                        str_replace(array_keys($data->all()), $data->all(), $this->theme->{'template'}),
                        $this->getScreenWidth()  + $this->theme->{'compensation'})
                    . $this->theme->{'append'};
        }

        return $this->clearLine() . $line;
    }

    public function pad($input, $pad_length, $pad_style = STR_PAD_RIGHT, $encoding = 'UTF-8')
    {
        return str_pad($input, strlen($input) - mb_strlen($input,$encoding) + $pad_length, ' ', $pad_style);
    }

    public function clearLinesUp($lines = 1)
    {
        return "\e[{$lines}A";
    }

    public function clearLine()
    {
        return "\r\e[K";
    }

    public function clearAll()
    {
        return "\e[2J";
    }

    public function unformat()
    {
        return "\e[0m";
    }

    public function getScreenWidth()
    {
        return (int) exec('tput cols');
    }
}
