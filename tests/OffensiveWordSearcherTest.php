<?php

namespace Pinnacle\CommonValueObjects\Tests;

use PHPUnit\Framework\TestCase;
use Pinnacle\CommonValueObjects\OffensiveWordSearcher;

/**
 * Class OffensiveWordSearcherTest
 */
class OffensiveWordSearcherTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider hasOffensiveLanguageDataProvider
     *
     * @param string $string
     * @param bool   $hasOffensiveLanguage
     */
    public function hasOffensiveLanguage_VariousStrings_expectedBoolean(string $string, bool $hasOffensiveLanguage)
    {
        $offensiveWordSearcher = new OffensiveWordSearcher();

        $this->assertEquals($offensiveWordSearcher->hasOffensiveLanguage($string), $hasOffensiveLanguage);
    }

    /**
     * Data provider for hasOffensiveLanguage test.
     *
     * @return array
     */
    public function hasOffensiveLanguageDataProvider(): array
    {
        return [
            //Inoffensive strings.
            [
                'abcdef',
                false,
            ],
            [
                'abcasDsdef',
                false,
            ],
            //Offensive strings.
            [
                'abcdeflmao',
                true,
            ],
            [
                'abcassdef',
                true,
            ],
            [
                'wankxyz',
                true,
            ],
            [
                'lame',
                true,
            ],
        ];
    }
}