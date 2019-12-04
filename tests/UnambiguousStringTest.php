<?php

namespace Pinnacle\CommonValueObjects\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Pinnacle\CommonValueObjects\OffensiveWordSearcher;
use Pinnacle\CommonValueObjects\UnambiguousString;

/**
 * Class UnambiguousStringTest
 */
class UnambiguousStringTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider unambiguousStringDataProvider
     *
     * @param int $characterCount
     */
    public function unambiguousString_VariousCharacterCounts_HasExpectedResult(int $characterCount)
    {
        $allowedCharacters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

        //If character count is less than 1 we should expect an exception.
        if ($characterCount < 1) {
            $this->expectException(InvalidArgumentException::class);
        }

        $unambiguousString = new UnambiguousString($characterCount);

        if ($characterCount > 0) {
            //Assert that the string is the proper length.
            $this->assertEquals(strlen($unambiguousString), $characterCount);

            //Assert that the string contains no offensive language.
            $this->assertEquals((new OffensiveWordSearcher())->hasOffensiveLanguage($unambiguousString), false);

            //Assert that the string only contains the allowed characters.
            foreach (str_split($unambiguousString) as $stringCharacter) {
                $this->assertContains($stringCharacter, $allowedCharacters);
            }
        }
    }

    /**
     * @return array
     */
    public function unambiguousStringDataProvider(): array
    {
        return [
            //Invalid character counts.
            [
                -1,
            ],
            [
                0,
            ],
            //Valid character counts.
            [
                1,
            ],
            [
                2,
            ],
            [
                3,
            ],
            [
                10,
            ],
            [
                50,
            ],
            [
                100,
            ],
            [
                1000,
            ],
        ];
    }
}