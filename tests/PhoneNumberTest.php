<?php

namespace Pinnacle\CommonValueObjects\Tests;

use PHPUnit\Framework\TestCase;
use Pinnacle\CommonValueObjects\PhoneNumber;

class PhoneNumberTest extends TestCase
{
    /**
     * @param PhoneNumber $phoneNumber
     * @param string      $format
     * @param string      $extensionPrefix
     * @param string      $expected
     *
     * @dataProvider formatDataProvider
     */
    public function testFormat(
        PhoneNumber $phoneNumber,
        string $format,
        string $extensionPrefix,
        string $expected
    ) {
        $this->assertSame($expected, $phoneNumber->format($format, $extensionPrefix));
    }

    /**
     * Data provider for testing the format.
     *
     * @return array
     */
    public function formatDataProvider(): array
    {
        return [
            [new PhoneNumber('8015551212'), '(%a) %e-%n', '', '(801) 555-1212'],
            [new PhoneNumber('8015551212 x55'), '(%a) %e-%n %x', 'ext. ', '(801) 555-1212 ext. 55'],
            [new PhoneNumber('8015551212'), '(%a) %e-%n %x', 'x', '(801) 555-1212'],
        ];
    }

    /**
     * Data provider for testing equals.
     *
     * @return array
     */
    public function equalityTestPhoneNumbers(): array
    {
        return [
            [new PhoneNumber('8015551212'), new PhoneNumber('8015551210'), false],
            [new PhoneNumber('8015551212'), new PhoneNumber('8015551212'), true],
            [new PhoneNumber('8015551212 x55'), new PhoneNumber('8015551212 ext. 55'), true],
            [new PhoneNumber('8015551212'), null, false],
            [new PhoneNumber('8015551212'), "string", false],
        ];
    }

    /**
     * Test the default format.
     */
    public function testDefaultFormat()
    {
        $phoneNumber = new PhoneNumber('8015551212');

        $this->assertSame('(801) 555-1212', $phoneNumber->format());
    }

    public function testDefaultExtensionFormat()
    {
        $phoneNumber = new PhoneNumber('8015551212 ext. 51');

        $this->assertSame('x51', $phoneNumber->format('%x'));
    }

    /**
     * @param PhoneNumber      $first
     * @param PhoneNumber|null $second
     * @param bool             $shouldEqual
     *
     * @dataProvider equalityTestPhoneNumbers
     */
    public function testEquals(PhoneNumber $first, $second, bool $shouldEqual)
    {
        if ($shouldEqual) {
            $this->assertTrue($first->equals($second));
        } else {
            $this->assertFalse($first->equals($second));
        }
    }
}
