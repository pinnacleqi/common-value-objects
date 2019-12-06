<?php

namespace Pinnacle\CommonValueObjects;

use InvalidArgumentException;

/**
 * Class UnambiguousString
 */
class UnambiguousString
{
    /**
     * @var string A string containing allowed characters.
     */
    const ALLOWED_CHARACTERS = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    /**
     * @var string
     */
    private $unambiguousString;

    /**
     * UnambiguousString constructor.
     *
     * @param int $characterCount
     */
    public function __construct(int $characterCount)
    {
        if ($characterCount < 1) {
            throw new InvalidArgumentException(
                'UnambiguousString can only generate strings with 1 or more characters.'
            );
        }

        $offensiveWordSearcher = new OffensiveWordSearcher();

        do {
            $unambiguousString = $this->generateUnambiguousString($characterCount);
        } while ($offensiveWordSearcher->hasOffensiveLanguage($unambiguousString));

        $this->unambiguousString = $unambiguousString;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->unambiguousString;
    }

    /**
     * Generates an unambiguous string with the number of provided characters.
     *
     * @param int $characterCount
     *
     * @return string
     */
    private function generateUnambiguousString(int $characterCount): string
    {
        $string = '';

        for ($index = 0; $index < $characterCount; $index++) {
            $maxOffset = strlen(self::ALLOWED_CHARACTERS) - 1;
            $string    .= self::ALLOWED_CHARACTERS [mt_rand(0, $maxOffset)];
        }

        return $string;
    }

    /**
     * Returns true if the provided string is unambiguous.
     *
     * @param string $string
     *
     * @return bool
     */
    public static function isUnambiguousString(string $string): bool
    {
        if ($string === '') {
            return false;
        }
        
        //Assert that the string only contains the allowed characters.
        foreach (str_split($string) as $stringCharacter) {
            if (strpos(self::ALLOWED_CHARACTERS, $stringCharacter) === false) {
                return false;
            }
        }

        return true;
    }
}