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
     * @param string $localPart
     *
     * @dataProvider validEmailAddressesWithParts
     */
    public function testLocalPart(EmailAddress $emailAddress, string $localPart)
    {
        $this->assertSame($localPart, $emailAddress->localPart());
    }

    /**
     * @param EmailAddress $emailAddress
     * @param string $localPart
     * @param string $domainPart
     *
     * @dataProvider validEmailAddressesWithParts
     */
    public function testDomainPart(EmailAddress $emailAddress, string $localPart, string $domainPart)
    {
        $this->assertSame($domainPart, $emailAddress->domainPart());
    }

    /**
     * @param EmailAddress $first
     * @param EmailAddress|null $second
     * @param bool $shouldEqual
     * @param bool $caseInsensitive
     *
     * @dataProvider equalityTestEmailAddresses
     */
    public function testEquals(EmailAddress $first, $second, bool $shouldEqual, bool $caseInsensitive = false)
    {
        if ($shouldEqual) {
            $this->assertTrue($first->equals($second, $caseInsensitive));
        } else {
            $this->assertFalse($first->equals($second, $caseInsensitive));
        }
    }

    /**
     * @param EmailAddress $emailAddress
     * @param bool $expectedResult
     *
     * @dataProvider hasTechnicalRoleAliasDataProvider
     */
    public function testHasTechnicalRoleAlias(EmailAddress $emailAddress, bool $expectedResult)
    {
        $this->assertEquals($emailAddress->isTechnicalRoleAlias(), $expectedResult);
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
            [new EmailAddress('fake@example.com'), new EmailAddress('Fake@example.com'), false],
            [new EmailAddress('fake@example.com'), new EmailAddress('FAKE@example.com'), true, true],
        ];
    }

    /**
     * @return mixed[][]
     */
    public function hasTechnicalRoleAliasDataProvider(): array
    {
        return [
            // Non technical roles
            [new EmailAddress('fake@example.com'), false],
            [new EmailAddress('info@example.com'), false],
            [new EmailAddress('marketing@example.com'), false],
            [new EmailAddress('sales@example.com'), false],
            [new EmailAddress('support@example.com'), false],
            [new EmailAddress('hostmaster@example.com'), false],
            [new EmailAddress('usenet@example.com'), false],
            [new EmailAddress('news@example.com'), false],
            [new EmailAddress('webmaster@example.com'), false],
            [new EmailAddress('www@example.com'), false],
            [new EmailAddress('uucp@example.com'), false],
            [new EmailAddress('ftp@example.com'), false],
            // Technical roles
            [new EmailAddress('abuse@example.com'), true],
            [new EmailAddress('noc@example.com'), true],
            [new EmailAddress('security@example.com'), true],
            [new EmailAddress('postmaster@example.com'), true],
            // Test uppercase values.
            [new EmailAddress('FTP@example.com'), true],
        ];
    }
}
