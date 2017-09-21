<?php

namespace Pinnacle\CommonValueObjects;

use InvalidArgumentException;

class EmailAddress
{
    /**
     * @var string
     */
    private $emailAddressString;

    /**
     * EmailAddress constructor.
     *
     * @param string $emailAddressString
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $emailAddressString)
    {
        if (filter_var($emailAddressString, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException(
                sprintf('The specified value [%s] does not appear to be a valid email address.', $emailAddressString)
            );
        }

        $this->emailAddressString = $emailAddressString;
    }

    /**
     * Returns the string representation of an email address.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->emailAddressString;
    }

    /**
     * Returns the email address as a string.
     *
     * @return string
     */
    public function value(): string
    {
        return $this->emailAddressString;
    }

    /**
     * Returns the portion of the email address before the "@" sign.
     *
     * @return string
     */
    public function localPart(): string
    {
        return substr($this->emailAddressString, 0, strrpos($this->emailAddressString, '@'));
    }

    /**
     * Returns the portion of the email address after the "@" sign.
     *
     * @return string
     */
    public function domainPart(): string
    {
        return substr($this->emailAddressString, strrpos($this->emailAddressString, '@') + 1);
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
