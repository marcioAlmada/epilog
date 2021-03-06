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
     * @expectedException         Epilog\FlowException
     * @expectedExceptionMessage  Bye!
     */
    public function testNoFollowOption()
    {
        $this->getEpilog(['--no-follow' => true])
             ->run(new FakeLogTail);
    }

    /**
     * @expectedException  RegexGuard\RegexException
     */
    public function testInvalidFilterOption()
    {
        $this->getEpilog(['--filter' => '/bad-regex'])
             ->run(new FakeLogTail);
    }

    /**
     * @expectedException         Epilog\FlowException
     * @expectedExceptionMessage  Bye!
     */
    public function testQuitInteraction()
    {
        $stdin = $this->getStdinMockTemplate()
                      ->shouldReceive('readLine')->twice()
                      ->andReturn('', 'q')
                      ->getMock();
        $epilog = $this->getEpilog();
        $epilog->run(new FakeLogTail, $stdin);
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
             ->run(new FakeLogTail, $stdin);
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
             ->run(new FakeLogTail, $stdin);

    }

    public function testThemeInteraction()
    {
        $stdin = $this->getStdinMockTemplate()
                      ->shouldReceive('readLine')->times(4)
                      ->andReturn('3', 'q', 'r', 'q')
                      ->getMock();
        $epilog = $this->getEpilog();

        $this->assertEquals($epilog::$themes['3'], $this->sandbox($epilog, $stdin)->args()['--theme']);
        $this->sandbox($epilog, $stdin);
    }

    /**
     * @expectedException  Epilog\FlowException
     */
    public function testThemeInvertInteraction()
    {
        $this->expectOutputRegex('#\\e\[7m#');
        $stdin = $this->getStdinMockTemplate()
                      ->shouldReceive('readLine')->twice()
                      ->andReturn('i', 'q')
                      ->getMock();
        $this->getEpilog(['--silent' => null])
             ->run(new FakeLogTail, $stdin);
    }

    public function testDebugInteraction()
    {
        $stdin = $this->getStdinMockTemplate()
                      ->shouldReceive('readLine')->times(2)
                      ->andReturn('d', 'q')
                      ->getMock();
        $epilog = $this->getEpilog(['--debug' => true]);
        $this->assertTrue($this->sandbox($epilog, $stdin)->args()['--debug']);
    }

    public function testFilterInteraction()
    {
        $stdin = $this->getStdinMockTemplate()
                      ->shouldReceive('readLine')->times(4)
                      ->andReturn('/DEBUG/', 'q', '-', 'q')
                      ->getMock();
        $epilog = $this->getEpilog();
        $this->assertEquals('/DEBUG/', $this->sandbox($epilog, $stdin)->args()['--filter']);
        $this->assertNull($this->sandbox($epilog, $stdin)->args()['--filter']);
    }

    protected function getEpilog(array $options = [])
    {
        $args = array_replace_recursive($this->defaults, $options);

        return new Epilog(new Response($args));
    }

    protected function getStdinMockTemplate()
    {
        return M::mock('Epilog\Interfaces\InputReaderInterface')
                ->shouldReceive('block')
                ->andReturn(M::self())
                ->shouldReceive('readChar')
                ->andReturn('r');
    }

    protected function sandbox($epilog, $stdin)
    {
        try {
            $epilog->run(new FakeLogTail, $stdin);
        } catch (\Epilog\FlowException $e) {
          return $epilog;
        }
        $this->fail('Epilog console did not quit ptoperly');
    }
}
