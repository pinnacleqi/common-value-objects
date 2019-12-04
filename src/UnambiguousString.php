<?php

namespace Pinnacle\CommonValueObjects;

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
     * UnambiguousString constructor.
     *
     * @param int $characterCount
     */
    public function __construct(int $characterCount)
    {
        $offensiveWordSearcher = new OffensiveWordSearcher();

        do {
            $unambiguousString = $this->generateUnambiguousString($characterCount);
        } while ($offensiveWordSearcher->hasOffensiveLanguage($unambiguousString));
    }

    /**
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