<?php

namespace Pinnacle\CommonValueObjects\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Pinnacle\CommonValueObjects\EmailAddress;

class EmailAddressTest extends TestCase
{
    /**
     * @param string $invalidEmailAddressString
     *
     * @dataProvider invalidEmailAddressStrings
     */
    public function testInvalidAddresses(string $invalidEmailAddressString)
    {
        $this->expectException(InvalidArgumentException::class);

        new EmailAddress($invalidEmailAddressString);
    }

    /**
     * @param string $invalidEmailAddressString
     *
     * @dataProvider invalidEmailAddressStrings
     */
    public function testTryParseInvalidAddresses(string $invalidEmailAddressString)
    {
        $this->assertFalse(EmailAddress::tryParse($invalidEmailAddressString, $emailAddress));
    }

    /**
     * @param EmailAddress $emailAddress
     *
     * @dataProvider validEmailAddressesWithParts
     */
    public function testTryParseValidAddresses(EmailAddress $emailAddress)
    {
        /* @var EmailAddress $result */
        $this->assertTrue(EmailAddress::tryParse($emailAddress->value(), $result));
        $this->assertSame($emailAddress->value(), $result->value());
    }

    /**
     * @param EmailAddress $emailAddress
     * @param string       $localPart
     *
     * @dataProvider validEmailAddressesWithParts
     */
    public function testLocalPart(EmailAddress $emailAddress, string $localPart)
    {
        $this->assertSame($localPart, $emailAddress->localPart());
    }

    /**
     * @param EmailAddress $emailAddress
     * @param string       $localPart
     * @param string       $domainPart
     *
     * @dataProvider validEmailAddressesWithParts
     */
    public function testDomainPart(EmailAddress $emailAddress, string $localPart, string $domainPart)
    {
        $this->assertSame($domainPart, $emailAddress->domainPart());
    }

    /**
     * @param EmailAddress      $first
     * @param EmailAddress|null $second
     * @param bool              $shouldEqual
     *
     * @dataProvider equalityTestEmailAddresses
     */
    public function testEquals(EmailAddress $first, $second, bool $shouldEqual)
    {
        if ($shouldEqual) {
            $this->assertTrue($first->equals($second));
        } else {
            $this->assertFalse($first->equals($second));
        }
    }

    /**
     * @return string[][]
     */
    public function invalidEmailAddressStrings(): array
    {
        return [
            [' fake@example.com'],
            ['fake@example.com '],
            ['asdf'],
            [' fake@example.com '],
            ['localhost'],
            ['bad@localhost'],
            ['bad@domain'],
            ['bad@bad.com@bad'],
        ];
    }

    /**
     * @return EmailAddress[][]
     */
    public function validEmailAddressesWithParts(): array
    {
        return [
            [new EmailAddress('test@example.com'), 'test', 'example.com'],
            [new EmailAddress('jimmy.lee@sub.example.com'), 'jimmy.lee', 'sub.example.com'],
            [new EmailAddress('j@j.com'), 'j', 'j.com'],
            [
                new EmailAddress('disposable.style.email.with+symbol@example.com'),
                'disposable.style.email.with+symbol',
                'example.com',
            ],
            [
                new EmailAddress('"very.unusual.@.unusual.com"@example.com'),
                '"very.unusual.@.unusual.com"',
                'example.com',
            ],
            [
                new EmailAddress('customer/department=shipping@example.com'),
                'customer/department=shipping',
                'example.com',
            ],
        ];
    }

    /**
     * @return array
     */
    public function equalityTestEmailAddresses(): array
    {
        return [
            [new EmailAddress('fake@example.com'), new EmailAddress('fake@example.com'), true],
            [new EmailAddress('fake@example.com'), new EmailAddress('diff@example.com'), false],
            [new EmailAddress('fake@example.com'), null, false],
            [new EmailAddress('fake@example.com'), 'string', false],
        ];
    }
}
