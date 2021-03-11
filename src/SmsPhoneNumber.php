<?php

namespace Pinnacle\CommonValueObjects;

use InvalidArgumentException;
use UnexpectedValueException;

class SmsPhoneNumber
{
    /**
     * @var PhoneNumber|null If this is a standard NANPA number, this will contain the phone number.
     */
    private $phoneNumber;
    /**
     * @var string|null If this is a short code, this will contain the short code.
     */
    private $shortCode;

    /**
     * SmsPhoneNumber constructor.
     *
     * Note: We do not consider internal phone provider short codes (three digits) as valid SMS phone numbers.
     *
     * @param string $numberString A short code (five or six digits) or a North American phone number.
     *
     * @throws InvalidArgumentException If the specified string could not be parsed into a short code or phone number.
     */
    public function __construct(string $numberString)
    {
        // First attempt to parse it as a North American phone number.
        $phoneNumber = self::parseNorthAmericanPhoneNumber($numberString);
        if ($phoneNumber !== null) {
            $this->phoneNumber = $phoneNumber;

            return;
        }

        // Attempt to parse it as a short code.
        $shortCode = self::parseNorthAmericanShortCode($numberString);
        if ($shortCode !== null) {
            $this->shortCode = $shortCode;

            return;
        }

        // Could not parse, throw exception
        throw new InvalidArgumentException(
            sprintf(
                'The specified value [%s] does not appear to be a valid North American short code or phone number.',
                $numberString
            )
        );
    }

    /**
     * The string representation of the phone number.
     *
     * Result should not be parsed or used for any logic operations as we reserve the right to change the way the phone
     * number is displayed.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->format();
    }

    /**
     * Indicates whether this SMS phone number is a short code.
     *
     * @return bool
     */
    public function isShortCode(): bool
    {
        return ($this->shortCode !== null);
    }

    /**
     * Indicates whether this SMS phone number is a long code.
     *
     * @return bool
     */
    public function isLongCode(): bool
    {
        return ($this->phoneNumber !== null);
    }

    /**
     * Returns the long code phone number for the SMS phone number or throws an exception if it doesn't exist.
     *
     * @return PhoneNumber
     */
    public function getLongCode(): PhoneNumber
    {
        if ($this->isShortCode()) {
            throw new UnexpectedValueException(
                sprintf('This SMS phone number [%s] dis not a long code', $this->shortCode)
            );
        }

        return $this->phoneNumber;
    }

    /**
     * Formats the SMS phone number.
     *
     * @return string
     */
    public function format(): string
    {
        if ($this->phoneNumber !== null) {
            return $this->phoneNumber->format();
        } else {
            return $this->shortCode;
        }
    }

    /**
     * Returns the normalized SMS phone number.
     *
     * If this is a 10-digit code, returns w/ format 1XXXYYYZZZZ xNNNNNN.
     * If this is a short code, returns w/ format NNNNNN.
     *
     * @return string
     */
    public function normalized(): string
    {
        if ($this->phoneNumber !== null) {
            return $this->phoneNumber->normalized();
        } else {
            return $this->shortCode;
        }
    }

    /**
     * Returns a delivery safe version of the phone number or shortcode.
     *
     * @return string
     */
    public function deliveryNumber(): string
    {
        if ($this->isLongCode()) {
            return $this->phoneNumber->e164();
        }

        return $this->shortCode;
    }

    /**
     * Indicates whether the specified phone number equals this phone number.
     *
     * @param static|null $other
     *
     * @return bool
     */
    public function equals(self $other = null): bool
    {
        if ($other === null) {
            return false;
        }

        if (!$other instanceof SmsPhoneNumber) {
            return false;
        }

        if ($other->isLongCode() && $this->isLongCode()) {
            return $this->phoneNumber->equals($other->phoneNumber);
        }

        if ($other->isShortCode() && $this->isShortCode()) {
            return $this->shortCode === $other->shortCode;
        }

        return false;
    }

    /**
     * Attempts to parse the specified phone number.
     *
     * @param string|null $numberString
     * @param static      $smsPhoneNumber The variable to assign the parsed SMS phone number to.
     *
     * @return bool Whether the specified phone number could be parsed.
     */
    public static function tryParse($numberString, &$smsPhoneNumber): bool
    {
        try {
            $smsPhoneNumber = new static($numberString);

            return true;
        } catch (InvalidArgumentException $e) {
            $smsPhoneNumber = null;

            return false;
        }
    }

    /**
     * Parses a North American phone number, or returns null if it could not be parsed.
     *
     * @param string $numberString
     *
     * @return PhoneNumber|null
     */
    private static function parseNorthAmericanPhoneNumber(string $numberString)
    {
        try {
            $phoneNumber = new PhoneNumber($numberString);

            if ($phoneNumber->extension() !== null) {
                // SMS phone numbers can't contain extensions.
                return null;
            }

            return $phoneNumber;
        } catch (InvalidArgumentException $exception) {
            return null;
        }
    }

    /**
     * Parses a North American short code, or returns null if it could not be parsed.
     *
     * According to usshortcodes.com (the organization that runs the short code registry for CTIA), codes can be five
     * or six digits and cannot start with a 0 or 1.
     *
     * @param string $numberString
     *
     * @return string|null
     */
    private static function parseNorthAmericanShortCode(string $numberString)
    {
        $cleanedNumberString = trim($numberString);

        if (preg_match('/^[2-9][0-9]{4,5}$/', $cleanedNumberString)) {
            return $cleanedNumberString;
        }

        return null;
    }
}
