<?php

namespace Epilog;

use Epilog\Interfaces\LineParserInterface;

class MonologLineParser implements LineParserInterface
{
    protected $pattern = '/\[(?P<date>.*)\] (?P<logger>\w+).(?P<level>\w+): (?P<message>.*[^ ]+) (?P<context>[^ ]+) (?P<extra>[^ ]+)/';

    /**
     * Constructor
     * @param string $pattern
     */
    public function __construct($pattern = null)
    {
        $this->pattern = ($pattern) ?: $this->pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($log)
    {
        if ( !is_string($log) || strlen($log) === 0) return null;

        preg_match($this->pattern, $log, $data);

        if (! isset($data['date'])) return null;
        return [
            '{date}'    => \DateTime::createFromFormat('Y-m-d H:i:s', $data['date']),
            '{logger}'  => $data['logger'],
            '{level}'   => $data['level'],
            '{message}' => $data['message'],
            '{context}' => json_decode($data['context'], true),
            '{extra}'   => json_decode($data['extra'], true)
        ];
    }
}
