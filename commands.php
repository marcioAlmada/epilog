<?php

namespace Epilog;

use Docopt\Handler;
use Docopt\Response;
use Minime\Annotations\Reader as AnnotationsReader;

/**
 * @command.spec
 * ​             _   _
 *             |_| | |            
 *    ___ _ __  _  | | ___   __ _ 
 *   / _ \ '_ \| | | |/ _ \_/ _` |
 *  |  __/ |_) | |_| | (_) _ (_| |
 *   \___| .__/|_____|\___/ \__, |
 *       | |                 __/ |
 *       |_|                |___/ 
 * 
 * Usage:
 *   epilog <command> [<args>...]
 * 
 * Commands are:
 *   epilog watch   Monitor a log files
 *   epilog pretend Display a fake log stream, for testing only
 * 
 * See 'epilog <command> -h' to read about a specific subcommand.
 * 
 * Options:
 *   -h --help                Show this screen.
 *   -v --version             Show version.
 */
function epilog()
{
    $specReader = AnnotationsReader::createFromDefaults();
    $handler = new Handler([ 'version'=>'Epilog 0.1.2', 'optionsFirst' => true ]);
    
    $response = $handler->handle(
        $specReader->getFunctionAnnotations(__FUNCTION__)->get('command.spec'));

    $command = __NAMESPACE__ . '\\' . $response['<command>'];

    if(! function_exists($command))
        throw new FlowException("Command `{$response['<command>']}` not defined, try epilog --help", 1);
    
    $handler->optionsFirst = false;
    $response = $handler->handle(
        $specReader->getFunctionAnnotations($command)->get('command.spec'));

    $command($response);
}

/**
 * @command.spec
 * Usage:
 *     epilog pretend [--filter=<filter>][--sleep-interval=<s>][--theme=<theme>][--theme-invert][--no-follow][--debug]
 * 
 * Options:
 *   -f --filter=<filter>     Filter log entries with a given regular expression.
 *   -s --sleep-interval=<s>  Sleep interval [default: .5].
 *   -t --theme=<theme>       Theme, see `Epilogs themes` to see list the list of themes [default: sunrise].
 *   -i --theme-invert        Invert theme foreground vs background colors.
 *   -n --no-follow           Print last logged lines and quit.
 *   -d --debug               Reloads theme on every loop. Slow, but useful while building new themes.
 *   -h --help                Show this screen.
 */
function pretend(Response $response)
{
    (new Epilog($response))->run(new FakeLogTail);    
}

/**
 * @command.spec
 *  Usage:
 *     epilog watch <file> [--filter=<filter>][--sleep-interval=<s>][--theme=<theme>][--theme-invert][--no-follow][--app=<app>][--debug]
 * 
 * Options:
 *   -f --filter=<filter>     Filter log entries with a given regular expression.
 *   -s --sleep-interval=<s>  Sleep interval [default: .5].
 *   -t --theme=<theme>       Theme, see `Epilogs themes` to see list the list of themes [default: sunrise].
 *   -i --theme-invert        Invert theme foreground vs background colors.
 *   -n --no-follow           Print last logged lines and quit.
 *   -a --app=<app>           Finds latest project log automatically by framework (ex: --app laravel) [default: generic].
 *   -d --debug               Reloads theme on every loop. Slow, but useful while building new themes.
 *   -h --help                Show this screen.
 */
function watch(Response $response)
{
    $epilog = new Epilog($response);
    $logFinder = LogFinderFactory::getLogFinder($response['--app']);
    $log = new LogTail($logFinder->find($response['<file>']));
    $epilog->run($log);
}
