<?php

namespace Epilog;

use Epilog\Interfaces\TailInterface;
use Epilog\Interfaces\MonitorInterface;
use Epilog\Interfaces\StreamReaderInterface;
use RegexGuard\Factory as RegexGuard;
use Docopt\Response;
use ErrorException;

class Epilog
{
    static $themes = [
        1 => 'chaplin',
        2 => 'forest',
        3 => 'scrapbook',
        4 => 'punchcard',
        5 => 'sunset',
        6 => 'sunrise',
        7 => 'traffic',
    ];

    /**
     * Epilog command line args container
     *
     * @var \Docopt\Response
     */
    protected $args;

    /**
     * Input reader
     *
     * @var \Epilog\Interfaces\StreamReaderInterface
     */
    protected $stdin;

    protected $printer;

    protected $ticker;

    /**
     * Screen update interval in useconds
     *
     * @var double
     */
    protected $sleep;

    protected $regexGuard;

    protected $loop = 0;

    public function __construct(Response $args)
    {
        static::checkRequirements();

        $this->args = $args;
        $this->sleep  = (float) $args['--sleep-interval'];
        $this->ticker = new Ticker;
        $this->printer = $this->loadPrinter();
        $this->regexGuard = RegexGuard::getGuard();
    }

    public function run(TailInterface $log, MonitorInterface $logMonitor, StreamReaderInterface $stdin = null)
    {
        $this->stdin = $stdin ?: new InputReader;
        $this->stdin->block(false);
        while (true) {
            if (! $this->loop++ || $logMonitor->read()) {
                $log->seekLastLineRead();
                while (! $log->eof()) {
                    $line = $log->fgets();
                    $filter = $this->args['--filter'];
                    if (! empty($filter))
                        if(! $this->regexGuard->match($filter, $line)) continue;
                    $this->output($this->printer->format($line));
                }
            }
            if($this->args['--no-follow']) $this->quit();
            $this->output($this->status($log->getRealPath()));
            $this->sleep(); // wait before trigger new iteration
            if($this->args['--debug']) $this->printer = $this->loadPrinter();
            $this->handleInteraction();
    }}

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
            foreach (self::$themes as $key => $theme)
                $this->output((! ($key & 1) ? "    \t" : "\n    ")  . "{$key}:$theme");
            $this->output(
                "\n\n [ r ] load random theme from list above."
                . "\n [ i ] toggle invert theme."
                . "\n [ d ] toggle debug mode."
                . "\n [ c ] clear screen."
                . "\n [ - ] reset regexp filter."
                . "\n [ q ] quit."
                . "\n\n [ ⏎ ] "
            );

            $command = $this->stdin->block()->readLine();

            switch ($command) {
                case "":
                    break;
                case 'q':
                    $this->quit();
                case 'r':
                    $this->loadRandomTheme();
                    break;
                case 'c':
                    $this->output($this->printer->clearAll());
                    break;
                case 'i':
                    $this->args['--theme-invert'] = $this->printer->invert();
                    break;
                case 'd':
                    $this->args['--debug'] = ! $this->args['--debug'];
                    break;
                case '-':
                    $this->args['--filter'] = null;
                    break;
                default:
                    if($this->regexGuard->isRegexValid($command))
                        $this->args['--filter'] = $command;
                    elseif (array_key_exists($command, self::$themes)) {
                        $this->args['--theme'] = self::$themes[$command];
                        $this->printer = $this->loadPrinter();
                    } else
                        $this->output(" Invalid option \"{$command}\" given.\n");
                    break;
            }
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

    protected function quit($message = 'Bye!', $code = 0)
    {
        throw new FlowException($message, $code);
    }

    protected function sleep()
    {
        usleep($this->sleep * 1000000);
    }

    protected function loadPrinter()
    {
        $themeFile = __DIR__ . '/../themes/'. $this->args['--theme'] . '.yml';

        return new MonologLinePrinter(
            new MonologLineParser, $themeFile, $this->args['--theme-invert']);
    }

    protected function loadRandomTheme()
    {
        $this->args['--theme'] = self::$themes[array_rand(self::$themes)];
        $this->printer = $this->loadPrinter();
    }

    public static function checkRequirements()
    {
        if (! extension_loaded('inotify'))
            throw new ErrorException('Missing PHP ext inotify.', 1);
    }

}
