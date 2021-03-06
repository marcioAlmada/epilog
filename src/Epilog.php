<?php

namespace Epilog;

use Epilog\Interfaces\TailInterface;
use Epilog\Interfaces\InputReaderInterface;
use RegexGuard\Factory as RegexGuard;
use Docopt\Response;

class Epilog
{
    const VERSION  = '1.0.1';

    /**
     * Theme tables
     *
     * @var array
     */
    static $themes = [
        1 => 'default',
        2 => 'chaplin',
        3 => 'forest',
        4 => 'scrapbook',
        5 => 'punchcard',
        6 => 'sunset',
        7 => 'sunrise',
        8 => 'traffic',
    ];

    /**
     * table of callable subcommands
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Epilog command line args container
     *
     * @var \Docopt\Response
     */
    protected $args;

    /**
     * Input reader
     *
     * @var \Epilog\Interfaces\InputReaderInterface
     */
    protected $stdin;

    /**
     * Line printer
     *
     * @var \Epilog\Interfaces\LinePrinterInterface
     */
    protected $printer;

    /**
     * @var \Epilog\Ticker
     */
    protected $ticker;

    /**
     * Regex validator
     *
     * @var \RegexGuard\RegexGuard
     */
    protected $regexGuard;

    public function __construct(Response $args)
    {
        $this->args = $args;
        $args['--sleep-interval']  = (float) $args['--sleep-interval'];
        $this->ticker = new Ticker;
        $this->printer = $this->loadPrinter();
        $this->regexGuard = RegexGuard::getGuard();
        $this->commands['']  = function () {};
        $this->commands['q'] = $this->commands[false] = [$this, 'quit'];
        $this->commands['r'] = [$this, 'loadRandomTheme'];
        $this->commands['c'] = [$this, 'clear'];
        $this->commands['i'] = [$this, 'invertTheme'];
        $this->commands['-'] = [$this, 'clearFilter'];
        $this->commands['default'] = function ($command) {
            if ($this->regexGuard->isRegexValid($command)) {
                $this->args['--filter'] = $command;
            } elseif (isset(self::$themes[$command])) {
                $this->args['--theme'] = self::$themes[$command];
                $this->printer = $this->loadPrinter();
            } else {
                $this->output(" Invalid option \"{$command}\" given.\n");
            }
        };
    }

    public function run(TailInterface $log, InputReaderInterface $stdin = null)
    {
        $this->stdin = $stdin ?: new InputReader;
        $this->stdin->block(false);
        while (true) {
            $log->seekLastLineRead();
            while (! $log->eof()) {
                $line = $log->fgets();
                $filter = $this->args['--filter'];
                if (! empty($filter))
                    if(! $this->regexGuard->match($filter, $line)) continue;
                $this->output($this->printer->format($line));
            }
            if($this->args['--no-follow']) $this->quit();
            $this->output($this->status($log->getRealPath()));
            $this->sleep(); // wait before trigger new iteration
            if($this->args['--debug']) $this->printer = $this->loadPrinter();
            $this->handleInteraction();
        }
    }

    public function args()
    {
        return $this->args;
    }

    protected function handleInteraction()
    {
        if (false !== $this->stdin->readChar()) {
            $this->output(
                "\n Woot! Epilog here. Please type a theme number,"
                . " a valid regexp to filter messages or a valid flag: \n"
                . "\n [#] load another theme:\n"
            );
            foreach (self::$themes as $key => $theme) {
                $this->output((! ($key & 1) ? "    \t" : "\n    ")  . "{$key}:$theme");
            }
            $this->output(
                "\n\n [ r ] load random theme from list above."
                . "\n [ i ] toggle invert theme."
                . "\n [ c ] clear screen."
                . "\n [ - ] reset regexp filter."
                . "\n [ q ] quit."
                . "\n\n [ ⏎ ] "
            );

            $command = $input = $this->stdin->block()->readLine();
            if(! isset($this->commands[$command])) $command = 'default';
            call_user_func_array($this->commands[$command], [$input]);
            $this->output($this->printer->unformat());
            $this->stdin->block(false);
        }
    }

    protected function output($string)
    {
        if (! isset($this->args['--silent'])) echo $string;
    }

    /**
     * Renders ANSI escaped status line
     *
     * @param  string $message status line message
     * @return string ANSI escaped status line
     */
    protected function status($message)
    {
        $clear = $this->printer->clearLinesUp(1);
        $statusLine =  "{$clear}\n[{$this->ticker}] {$message} [ ⏎ ]";
        $screenWidth = $this->printer->getScreenWidth() + 5;

        return $this->printer->pad($statusLine, $screenWidth) . $this->printer->unformat();
    }

    protected function quit()
    {
        throw new FlowException('Bye!', 0);
    }

    protected function sleep()
    {
        usleep($this->args['--sleep-interval'] * 1000000);
    }

    protected function loadPrinter()
    {
        $theme = __DIR__ . '/../themes/'. $this->args['--theme'] . '.yml';

        return new MonologLinePrinter(
            new MonologLineParser, new Theme($theme), $this->args['--theme-invert']);
    }

    protected function loadRandomTheme()
    {
        $this->args['--theme'] = self::$themes[array_rand(self::$themes)];
        $this->printer = $this->loadPrinter();
    }

    protected function clear()
    {
        $this->output($this->printer->clearAll());
    }

    protected function invertTheme()
    {
        $this->args['--theme-invert'] = $this->printer->invert();
    }

    protected function clearFilter()
    {
        $this->args['--filter'] = null;
    }
}
