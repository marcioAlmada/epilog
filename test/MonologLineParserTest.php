<?php

namespace Epilog;

/**
 * MonologLineParserTest
 *
 * @group support
 */
class MonologLineParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider logEntryProvider
     */
    public function testParse($entry, $parsed)
    {
        $parser = new MonologLineParser();
        $this->assertEquals($parsed, $parser->parse($entry));
    }

    public function logEntryProvider()
    {
        return [
            [
                "[2014-09-22 18:01:29] log.EMERGENCY: Ea ratione eaque quae adipisci odio. [] []",
                [
                    '{date}'    => \DateTime::createFromFormat('Y-m-d H:i:s', '2014-09-22 18:01:29'),
                    '{logger}'  => 'log',
                    '{level}'   => 'EMERGENCY',
                    '{message}' => 'Ea ratione eaque quae adipisci odio.',
                    '{context}' => json_decode('[]', true),
                    '{extra}'   => json_decode('[]', true)
                ]
            ],
            [
                "[2014-09-22 18:01:30] log.NOTICE: [\"lala\", \"lele\",{\"lili\": {\"lolo\" : \"lulu\"}}] [] []",
                [
                    '{date}'    => \DateTime::createFromFormat('Y-m-d H:i:s', '2014-09-22 18:01:30'),
                    '{logger}'  => 'log',
                    '{level}'   => 'NOTICE',
                    '{message}' => "[\"lala\", \"lele\",{\"lili\": {\"lolo\" : \"lulu\"}}]",
                    '{context}' => json_decode('[]', true),
                    '{extra}'   => json_decode('[]', true)
                ]
            ],
            [
                "invalid log Consequatur vitae molestias ut ipsa praesentium. [] []",
                null
            ]
        ];
    }
}
