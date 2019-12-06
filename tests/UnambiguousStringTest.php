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
            $this->assertTrue(UnambiguousString::isUnambiguousString($unambiguousString));
        }
    }

    /**
     * @test
     *
     * @dataProvider isUnambiguousStringDataProvider
     *
     * @param string $string
     * @param bool   $expectedResult
     */
    public function isUnambiguousString_VariousStrings_HasExpectedResult(string $string, bool $expectedResult)
    {
        $this->assertEquals(UnambiguousString::isUnambiguousString($string), $expectedResult);
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

    /**
     * @return array
     */
    public function isUnambiguousStringDataProvider(): array
    {
        return [
            //Valid Unambiguous strings (23456789ABCDEFGHJKLMNPQRSTUVWXYZ)
            [
                '23456789ABCDEFGHJKLMNPQRSTUVWXYZ',
                true,
            ],
            [
                '2',
                true,
            ],
            [
                'A',
                true,
            ],
            [
                '23456789',
                true,
            ],
            [
                'ABCDEFGHJKLMNPQRSTUVWXYZ',
                true,
            ],
            //Invalid Unambiguous strings
            [
                '',
                false,
            ],
            [
                '23456789abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ',
                false,
            ],
            [
                '2a',
                false,
            ],
            [
                'a',
                false,
            ],
            [
                'Ii',
                false,
            ],
            [
                'o',
                false,
            ],
        ];
    }
}