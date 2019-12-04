<?php

namespace Pinnacle\CommonValueObjects;

use UnexpectedValueException;

/**
 * Class OffensiveWordSearcher.
 */
class OffensiveWordSearcher
{
    /**
     * @var string Path to offensive words file.
     */
    const OFFENSIVE_WORDS_FILE_PATH = __DIR__ . '/resources/offensive-words.txt';
    /**
     * @var array
     */
    private $offensiveWords;

    /**
     * OffensiveWordSearcher constructor.
     */
    public function __construct()
    {
        $offensiveWordData = file_get_contents(self::OFFENSIVE_WORDS_FILE_PATH);

        if ($offensiveWordData === false) {
            throw new UnexpectedValueException('Unable to retrieve offensive language file.');
        }

        $this->offensiveWords = explode("\n", $offensiveWordData);
    }

    /**
     * Determines if the provided string contains offensive language.
     *
     * @param string $string
     *
     * @return bool
     */
    public function hasOffensiveLanguage(string $string): bool
    {
        foreach ($this->offensiveWords as $offensiveWord) {
            if ($offensiveWord === '') {
                continue;
            }

            if (strpos(strtoupper($string), strtoupper($offensiveWord)) !== false) {
                return true;
            }
        }

        return false;
    }
}