<?php

namespace Epilog;

use Epilog\Interfaces\TailInterface;
use DateTime;

class FakeLogTail implements TailInterface
{
    protected $faker;

    protected $eof = true;

    static $levels = [
        'DEBUG', 'INFO', 'NOTICE', 'DEBUG', 'INFO', 'NOTICE', 'WARNING',
        'DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'CRITICAL', 'ALERT',
        'EMERGENCY', 'ERROR'
    ];

    protected static $wordList = array(
        'alias', 'consequatur', 'aut', 'perferendis', 'sit', 'voluptatem',
        'accusantium', 'doloremque', 'aperiam', 'eaque','ipsa', 'quae', 'ab',
        'illo', 'inventore', 'veritatis', 'et', 'quasi', 'architecto',
        'beatae', 'vitae', 'dicta', 'sunt', 'explicabo', 'aspernatur', 'aut',
        'odit', 'aut', 'fugit', 'sed', 'quia', 'consequuntur', 'magni',
        'dolores', 'eos', 'qui', 'ratione', 'voluptatem', 'sequi', 'nesciunt',
        'neque', 'dolorem', 'ipsum', 'quia', 'dolor', 'sit', 'amet',
        'consectetur', 'adipisci', 'velit', 'sed', 'quia', 'non', 'numquam',
        'eius', 'modi', 'tempora', 'incidunt', 'ut', 'labore', 'et', 'dolore',
        'magnam', 'aliquam', 'quaerat', 'voluptatem', 'ut', 'enim', 'ad',
        'minima', 'veniam', 'quis', 'nostrum', 'exercitationem', 'ullam',
        'corporis', 'nemo', 'enim', 'ipsam', 'voluptatem', 'quia', 'voluptas',
        'sit', 'suscipit', 'laboriosam', 'nisi', 'ut', 'aliquid', 'ex', 'ea',
        'commodi', 'consequatur', 'quis', 'autem', 'vel', 'eum', 'iure',
        'reprehenderit', 'qui', 'in', 'ea', 'voluptate', 'velit', 'esse',
        'quam', 'nihil', 'molestiae', 'et', 'iusto', 'odio', 'dignissimos',
        'ducimus', 'qui', 'blanditiis', 'praesentium', 'laudantium', 'totam',
        'rem', 'voluptatum', 'deleniti', 'atque', 'corrupti', 'quos',
        'dolores', 'et', 'quas', 'molestias', 'excepturi', 'sint'
    );

    /**
     * Just a method passtru
     *
     */
    public function __call($method, $args) {}

    public function fgets()
    {
        $level = static::$levels[array_rand(static::$levels)];
        $date = (new DateTime)->format('Y-m-d H:i:s');
        $message = self::sentence(rand(5, 10));

        return "[{$date}] log.{$level}: {$message} {} {}";
    }

    public function eof()
    {
        return $this->eof = ! $this->eof;
    }

    public function getRealPath()
    {
        return 'nsa://';
    }

    /**
     * Generate an array of random words
     *
     * @example array('Lorem', 'ipsum', 'dolor')
     * @param  integer      $nb     how many words to return
     * @param  bool         $asText if true the sentences are returned as one string
     * @return array|string
     */
    protected static function words($nb = 3, $asText = false)
    {
        $words = array();
        for ($i=0; $i < $nb; $i++) {
            $words []= static::randomElement(static::$wordList);
        }

        return $asText ? join(' ', $words) : $words;
    }

    /**
     * Generate a random sentence
     *
     * @example 'Lorem ipsum dolor sit amet.'
     * @param  integer $nbWords         around how many words the sentence should contain
     * @param  boolean $variableNbWords set to false if you want exactly $nbWords returned,
     *                                  otherwise $nbWords may vary by +/-40% with a minimum of 1
     * @return string
     */
    protected static function sentence($nbWords = 6, $variableNbWords = true)
    {
        if ($variableNbWords) {
            $nbWords = self::randomizeNbElements($nbWords);
        }

        $words = static::words($nbWords);
        $words[0] = ucwords($words[0]);

        return join($words, ' ') . '.';
    }

    protected static function randomizeNbElements($nbElements)
    {
        return (int) ($nbElements * mt_rand(60, 140) / 100) + 1;
    }

    /**
     * Returns random elements from a provided array
     *
     * @param  array            $array Array to take elements from. Defaults to a-f
     * @param  integer          $count Number of elements to take.
     * @throws \LengthException When requesting more elements than provided
     *
     * @return array New array with $count elements from $array
     */
    protected static function randomElements(array $array = array('a', 'b', 'c'), $count = 1)
    {
        $allKeys = array_keys($array);
        $numKeys = count($allKeys);
        $highKey = $numKeys - 1;
        $elements = array();
        $numElements = 0;

        while ($numElements < $count) {
            $num = mt_rand(0, $highKey);
            $elements[] = $array[$allKeys[$num]];
            $numElements++;
        }

        return $elements;
    }

    /**
     * Returns a random element from a passed array
     *
     * @param  array $array
     * @return mixed
     */
    protected static function randomElement($array = array('a', 'b', 'c'))
    {
        $elements = static::randomElements($array, 1);

        return $elements[0];
    }
}
