<?php

namespace Epilog;

use org\bovigo\vfs\vfsStream as Vsf;

/**
 * LogFinderTest
 *
 * @group support
 */
class LogFinderTest extends \PHPUnit_Framework_TestCase
{
   
    /**
     * @expectedException Epilog\FlowException
     * @expectedExceptionMessage Could not read log file 'vfs://dir/deny.log'
     */
    public function testGenericFinder()
    {
        $finder = LogFinderFactory::getLogFinder('generic');
        $dir = Vsf::setup('dir/');

        Vsf::newFile('allow.log')->at($dir);
        $this->assertEquals('vfs://dir/allow.log', $finder->find(Vsf::url('dir/allow.log'))); // pass

        Vsf::newFile('dir/deny.log')->at($dir)->withContent('*')->chmod(555);
        $finder->find(Vsf::url('dir/deny.log')); // throw
    }

    public function laravelLogProvider()
    {
        return [
            ['/fixtures/laravel_a/app/storage/logs/log.log', '/fixtures/laravel_a/'],
            ['/fixtures/laravel_a/app/storage/logs/log.log', '/fixtures/laravel_a/app/storage/logs/log.log'],
            ['/fixtures/laravel_b/app/storage/logs/log.txt', '/fixtures/laravel_b/'],
            ['/fixtures/laravel_b/app/storage/logs/log.txt', '/fixtures/laravel_b/app/storage/logs/log.txt'],
        ];
    }

    /**
     * @dataProvider laravelLogProvider
     */
    public function testLaravelFinder($log, $app)
    {
        $finder = LogFinderFactory::getLogFinder('laravel');
        $this->assertEquals(__DIR__ . $log, $finder->find(__DIR__ . $app));
    }

    /**
     * @expectedException Epilog\FlowException
     * @expectedExceptionMessage Could not read latest log file
     */
    public function testLaravelFinderFailure()
    {
        $finder = LogFinderFactory::getLogFinder('laravel');
        $finder->find('missing');
    }

    /**
     * @expectedException Epilog\FlowException
     * @expectedExceptionMessage Log finder for Foo is not available
     */
    public function testLogFinderFactoryFailure()
    {
        LogFinderFactory::getLogFinder('Foo');
    }

}
