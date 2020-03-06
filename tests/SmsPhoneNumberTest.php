<?php

namespace Pinnacle\CommonValueObjects\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Pinnacle\CommonValueObjects\SmsPhoneNumber;

class SmsPhoneNumberTest extends TestCase
{
    /**
     * @param string $rawNumber
     * @param bool   $shouldSucceed
     *
     * @dataProvider instantiationsDataSource
     *
     * @throws InvalidArgumentException
     */
    public function testInstantiations(string $rawNumber, bool $shouldSucceed)
    {
        // Can't follow AAA convention here due to the way the exception will be thrown on instantiation.

        if (!$shouldSucceed) {
            $this->expectException(InvalidArgumentException::class);
        }

        $smsPhoneNumber = new SmsPhoneNumber($rawNumber);

        if ($shouldSucceed) {
            $this->assertNotNull($smsPhoneNumber, 'The SMS phone number was null.');
        }
    }

    /**
     * @param string $rawNumber
     * @param string $expectedFormattedNumber
     *
     * @dataProvider formatsDataSource
     *
     * @throws InvalidArgumentException
     */
    public function testFormats(string $rawNumber, string $expectedFormattedNumber)
    {
        // Assemble
        $smsPhoneNumber = new SmsPhoneNumber($rawNumber);

        // Act
        $formattedNumber = $smsPhoneNumber->format();

        // Assert
        $this->assertSame($expectedFormattedNumber, $formattedNumber);
    }

    /**
     * @param string $rawNumber
     * @param string $expectedNormalizedNumber
     *
     * @dataProvider normalizationsDataSource
     *
     * @throws InvalidArgumentException
     */
    public function testNormalizations(string $rawNumber, string $expectedNormalizedNumber)
    {
        // Assemble
        $smsPhoneNumber = new SmsPhoneNumber($rawNumber);

        // Act
        $normalizedNumber = $smsPhoneNumber->normalized();

        // Assert
        $this->assertSame($expectedNormalizedNumber, $normalizedNumber);
    }

    /**
     * @param string $rawNumber
     * @param bool   $expectedIsShortCode
     *
     * @dataProvider isShortCodeDataSource
     *
     * @throws InvalidArgumentException
     */
    public function testIsShortCode(string $rawNumber, bool $expectedIsShortCode)
    {
        // Assemble
        $smsPhoneNumber = new SmsPhoneNumber($rawNumber);

        // Act
        $isShortCode = $smsPhoneNumber->isShortCode();

        // Assert
        $this->assertSame($expectedIsShortCode, $isShortCode);
    }

    /**
     * @param string $rawNumber
     * @param bool   $expectedIsLongCode
     *
     * @dataProvider isLongCodeDataSource
     *
     * @throws InvalidArgumentException
     */
    public function testIsLongCode(string $rawNumber, bool $expectedIsLongCode)
    {
        // Assemble
        $smsPhoneNumber = new SmsPhoneNumber($rawNumber);

        // Act
        $isLongCode = $smsPhoneNumber->isLongCode();

        // Assert
        $this->assertSame($expectedIsLongCode, $isLongCode);
    }

    //public function testE164Numbers(string $rawNumber, string $expectedE164Number)
    //{
    //    // Assemble
    //    $smsPhoneNumber = new SmsPhoneNumber($rawNumber);
    //
    //    // Act
    //    $normalizedNumber = $smsPhoneNumber->normalized();
    //
    //    // Assert
    //    $this->assertSame($expectedNormalizedNumber, $normalizedNumber);
    //}

    /**
     * Data provider for testing the instantiation of good and bad SMS phone numbers.
     *
     * @return array
     */
    public function instantiationsDataSource(): array
    {
        return [
            ['8015551212', true],
            ['55115', true],
            [' 55115', true],
            ['55115 ', true],
            ['551156', true],

            ['', false],
            ['123', false],
            ['611', false],
            ['abc', false],
            ['12345', false],
            ['123456', false],
            ['5511567', false],
            ['a44415', false],
            ['02234', false],
            ['022345', false],
            ['8015551212 x123', false],
        ];
    }

    /**
     * Data provider for testing the formatting of SMS phone numbers.
     *
     * @return array
     */
    public function formatsDataSource(): array
    {
        return [
            ['8015551212', '(801) 555-1212'],
            ['77369', '77369'],
            [' 77369 ', '77369'],
            ['551551', '551551'],
        ];
    }

    /**
     * Data provider for testing the normalization of SMS phone numbers.
     *
     * @return array
     */
    public function normalizationsDataSource(): array
    {
        return [
            ['(801) 555-1212', '18015551212'],
            [' 77369 ', '77369'],
            [' 551551', '551551'],
        ];
    }

    /**
     * Data provider for testing is short code.
     *
     * @return array
     */
    public function isShortCodeDataSource(): array
    {
        return [
            ['8015551212', false],
            ['43553', true],
            ['425552', true],
            [' 535551 ', true],
        ];
    }

    /**
     * Data provider for testing is short code.
     *
     * @return array
     */
    public function isLongCodeDataSource(): array
    {
        return [
            ['8015551212', true],
            ['43553', false],
            ['425552', false],
            [' 535551 ', false],
        ];
    }
}