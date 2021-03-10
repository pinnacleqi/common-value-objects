<?php

namespace Pinnacle\CommonValueObjects;

use InvalidArgumentException;

class EmailAddress
{
    const TECHNICAL_ROLE_ALIASES = [
        'abuse',
        'noc',
        'security',
        'postmaster',
    ];

    /**
     * @var string The email address string.
     */
    private $emailAddress;

    /**
     * EmailAddress constructor.
     *
     * @param string $emailAddress
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $emailAddress)
    {
        if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException(
                sprintf('The specified value (%s) does not appear to be a valid email address.', $emailAddress)
            );
        }

        $this->emailAddress = $emailAddress;
    }

    /**
     * Returns the string representation of an email address.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->emailAddress;
    }

    /**
     * Returns the email address as a string.
     *
     * @return string
     */
    public function value(): string
    {
        return $this->emailAddress;
    }

    /**
     * Returns the portion of the email address before the "@" sign.
     *
     * @return string
     */
    public function localPart(): string
    {
        return substr($this->emailAddress, 0, strrpos($this->emailAddress, '@'));
    }

    /**
     * Returns the portion of the email address after the "@" sign.
     *
     * @return string
     */
    public function domainPart(): string
    {
        return substr($this->emailAddress, strrpos($this->emailAddress, '@') + 1);
    }

    /**
     * Indicates whether the specified email address equals this email address.
     *
     * @param EmailAddress|null $other
     * @param bool $caseInsensitive Whether to compare addresses in a case-insensitive manner. Defaults to
     *                                           false.
     *
     * @return bool
     */
    public function equals($other, bool $caseInsensitive = false)
    {
        if ($other === null) {
            return false;
        }

        if (!$other instanceof EmailAddress) {
            return false;
        }

        if ($caseInsensitive) {
            return mb_strtolower($this->emailAddress) === mb_strtolower($other->emailAddress);
        } else {
            return $this->emailAddress === $other->emailAddress;
        }
    }

    /**
     * Returns true if the email contains a standard role alias.
     *
     * @return bool
     */
    public function isTechnicalRoleAlias(): bool
    {
        return in_array(strtolower($this->localPart()), self::TECHNICAL_ROLE_ALIASES, true);
    }

    /**
     * Attempts to parse the specified email address.
     *
     * @param string $emailAddressString The email address string to try parsing.
     * @param EmailAddress $emailAddress The variable to assign the email address to.
     *
     * @return bool Whether the specified email address could be parsed.
     */
    public static function tryParse(string $emailAddressString, &$emailAddress)
    {
        try {
            $emailAddress = new static($emailAddressString);

            return true;
        } catch (InvalidArgumentException $e) {
            $emailAddress = null;

            return false;
        }
    }
}
