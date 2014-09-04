<?php

namespace Epilog;

use Mockery as M;
use Docopt\Response;

/**
 * EpilogTest
 *
 * @group main
 */
class EpilogTest extends \PHPUnit_Framework_TestCase
{
    protected $defaults = [
        '--no-follow' => false,
        '--sleep-interval' => 1,
        '--theme' => 'default',
        '--theme-invert' => false,
        '--filter' => null,
        '--debug' => false,
        '--sleep-interval' =>  0,
        '--silent' => true
    ];

    public function tearDown()
    {
        M::close();
    }

    /**
     * @expectedException        Epilog\FlowException
     * @expectedExceptionMessae  Bye!
     */
    public function testNoFollowOption()
    {
        $this->getEpilog(['--no-follow' => true])
             ->run(new FakeLogTail, new FakeMonitor);
    }

    /**
     * @expectedException  RegexGuard\RegexException
     */
    public function testInvalidFilterOption()
    {
        $this->getEpilog(['--filter' => '/bad-regex'])
             ->run(new FakeLogTail, new FakeMonitor);
    }

    /**
     * @expectedException        Epilog\FlowException
     * @expectedExceptionMessae  Bye!
     */
    public function testQuitInteraction()
    {
        $stdin = $this->getStdinMockTemplate()
                      ->shouldReceive('readLine')->twice()
                      ->andReturn('', 'q')
                      ->getMock();
        $epilog = $this->getEpilog();
        $epilog->run(new FakeLogTail, new FakeMonitor, $stdin);
    }

    /**
     * @expectedException  Epilog\FlowException
     */
    public function testInvalidInteraction()
    {
        $this->expectOutputRegex('#Invalid option "\?" given\.#');
        $stdin = $this->getStdinMockTemplate()
                      ->shouldReceive('readLine')->twice()
                      ->andReturn('?', 'q')
                      ->getMock();
        $this->getEpilog(['--silent' => null])
             ->run(new FakeLogTail, new FakeMonitor, $stdin);
    }

    /**
     * @expectedException  Epilog\FlowException
     */
    public function testClearInteraction()
    {
        $this->expectOutputRegex('#\\e\[2J#');
        $stdin = $this->getStdinMockTemplate()
                      ->shouldReceive('readLine')->twice()
                      ->andReturn('c', 'q')
                      ->getMock();
        $this->getEpilog(['--silent' => null])
             ->run(new FakeLogTail, new FakeMonitor, $stdin);

    }

    protected function getEpilog(array $options = [])
    {
        $args = array_replace_recursive($this->defaults, $options);

        return new Epilog(new Response($args));
    }

    protected function getStdinMockTemplate()
    {
        return M::mock('Epilog\Interfaces\StreamReaderInterface')
                ->shouldReceive('block')
                ->andReturn(M::self())
                ->shouldReceive('readChar')
                ->andReturn('r');
    }

    protected function sandbox($epilog, $stdin)
    {
        try {
            $epilog->run(new FakeLogTail, new FakeMonitor, $stdin);
        } catch (\Epilog\FlowException $e) {

          return $epilog;
        }
        $this->fail('Epilog console did not quit ptoperly');
    }
}
