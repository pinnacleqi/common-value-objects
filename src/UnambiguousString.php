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
        $code = '';

        for ($index = 0; $index < $characterCount; $index++) {
            $maxOffset = strlen(self::ALLOWED_CHARACTERS) - 1;
            $code      .= self::ALLOWED_CHARACTERS [mt_rand(0, $maxOffset)];
        }

        return $code;
    }
}