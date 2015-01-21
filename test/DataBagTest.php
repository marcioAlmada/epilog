<?php

namespace Epilog;

/**
 * DataBagTest
 *
 * @group support
 */
class DataBagTest extends \PHPUnit_Framework_TestCase
{
    protected $bag;

    protected $data = [
        'a' => [
            'b' => [
                'c' => 'd'
            ]
        ]
    ];

    public function setUp()
    {
        $this->bag = new DataBag($this->data);
    }

    public function testAll()
    {
        $this->assertEquals($this->data, $this->bag->all());
    }

    public function testSet()
    {
        $this->bag->set('a.b.c', 'PATCH'); // update nested index
        $this->assertArraySubset(['a' => ['b' => ['c' => 'PATCH']]], $this->bag->all());

        $this->bag->set('a.b.c.d', 'PATCH'); // override non-array value
        $this->assertArraySubset(['a' => ['b' => ['c' => ['d' => 'PATCH']]]], $this->bag->all());

        $this->bag->{'x y z'} = 'PATCH'; // create nested index
        $this->assertArraySubset(['x' => ['y' => ['z' => 'PATCH']]], $this->bag->all());
    }

    public function testGet()
    {
        $this->assertEquals('d', $this->bag->get('a.b.c'));
        $this->assertEquals('d', $this->bag->{'a b c'});
        $this->assertEquals('default', $this->bag->get('not set', 'default'));
    }

    public function testKeys()
    {
        $this->assertSame(['a'], $this->bag->keys());
    }

    public function testValues()
    {
        $this->bag->set('a', 'b');
        $this->assertSame(['b'], $this->bag->values());
    }

}
