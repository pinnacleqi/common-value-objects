<?php

namespace Pinnacle\CommonValueObjects;

use InvalidArgumentException;

/**
 * Value object for a North American phone number.
 */
class PhoneNumber
{
    /**
     * @var string The phone number in format 1XXXYYYZZZZ xNNNNNN.
     */
    private $phoneNumber;

    /**
     * PhoneNumber constructor.
     *
     * @param string $phoneNumber
     *
     * @throws InvalidArgumentException If the phone number is not a valid North American phone number.
     */
    public function __construct($phoneNumber)
    {
        $normalizedPhoneNumber = self::parseNorthAmericanPhoneNumber($phoneNumber);
        if ($normalizedPhoneNumber === null) {
            throw new InvalidArgumentException(
                sprintf(
                    'The specified value [%s] does not appear to be a valid North American phone number.',
                    $phoneNumber
                )
            );
        }

        $this->phoneNumber = $normalizedPhoneNumber;
    }

    /**
     * The string representation of the phone number.
     *
     * Result should not be parsed or used for any logic operations as we reserve the right to change the way the phone
     * number is displayed.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->format();
    }

    /**
     * Returns the phone number area code
     *
     * @return string
     */
    public function areaCode(): string
    {
        return substr($this->phoneNumber, 1, 3);
    }

    /**
     * Returns the exchange of the phone number.
     *
     * @return string
     */
    public function exchange(): string
    {
        return substr($this->phoneNumber, 4, 3);
    }

    /**
     * Returns the subscriber number portion of the phone number.
     *
     * @return string
     */
    public function subscriberNumber(): string
    {
        return substr($this->phoneNumber, 7, 4);
    }

    /**
     * Returns the extension of the phone number, or null if no extension.
     *
     * @return string|null
     */
    public function extension()
    {
        if (strlen($this->phoneNumber) >= 14) {
            return substr($this->phoneNumber, 13);
        } else {
            return null;
        }
    }

    /**
     * Returns the normalized phone number w/ format 1XXXYYYZZZZ xNNNNNN.
     *
     * @return string
     */
    public function normalized(): string
    {
        return $this->phoneNumber;
    }

    /**
     * Returns the phone number in E.164 format (e.g. +1XXXYYYZZZZ).
     *
     * This will not return the extension (if there is one) because that's not part of E.164.
     *
     * @return string
     */
    public function e164(): string
    {
        return '+' . substr($this->phoneNumber, 0, 11);
    }

    /**
     * Indicates whether this is a toll-free phone number.
     */
    public function isTollFree(): bool
    {
        $areaCode = $this->areaCode();

        return $areaCode === '800' || $areaCode === '888' || $areaCode === '877' || $areaCode === '866' ||
               $areaCode === '855' || $areaCode === '844' || $areaCode === '833';
    }

    /**
     * Formats the phone number for display.
     *
     * Tokens allowed in the format string are:
     * %a : area code
     * %e : exchange
     * %n : subscriber number
     * %x : extension
     *
     * If no extension is found in the phone number, the %x will render empty and any surrounding whitespace is removed.
     *
     * @param string $format          The format string to use.
     * @param string $extensionPrefix The prefix to add before the extension if an extension exists.
     *
     * @return string
     */
    public function format(string $format = '(%a) %e-%n %x', string $extensionPrefix = 'x')
    {
        $areaCode         = $this->areaCode();
        $exchange         = $this->exchange();
        $subscriberNumber = $this->subscriberNumber();
        $extension        = $this->extension();

        // We don't support escaping percent characters yet, but we can implement later if it's ever actually needed

        $formattedNumber = str_replace(
            ['%a', '%e', '%n'],
            [$areaCode, $exchange, $subscriberNumber],
            $format
        );

        if ($extension !== null) {
            $formattedNumber = str_replace('%x', $extensionPrefix . $extension, $formattedNumber);
        } else {
            // Remove %x from formatted number (including any whitespace around it)
            $formattedNumber = preg_replace('/\s*\%x\s*/', '', $formattedNumber);
        }

        return $formattedNumber;
    }

    /**
     * Indicates whether the specified phone number equals this phone number.
     *
     * @param PhoneNumber|null $other
     *
     * @return bool
     */
    public function equals($other)
    {
        if ($other === null) {
            return false;
        }

        if (!$other instanceof PhoneNumber) {
            return false;
        }

        return $this->phoneNumber === $other->phoneNumber;
    }

    /**
     * Attempts to parse the specified phone number.
     *
     * @param string $phoneNumberString
     * @param static $phoneNumber The variable to assign the parsed phone number to.
     *
     * @return bool Whether the specified phone number could be parsed.
     */
    public static function tryParse($phoneNumberString, &$phoneNumber)
    {
        try {
            $phoneNumber = new static($phoneNumberString);

            return true;
        } catch (InvalidArgumentException $e) {
            $phoneNumber = null;

            return false;
        }
    }

    /**
     * Parses a phone number to a normalized result consisting of the country code, phone number, and optional
     * extension.
     *
     * @param string $phoneNumberString
     *
     * @return string|null
     */
    private static function parseNorthAmericanPhoneNumber($phoneNumberString)
    {
        if (!isset($phoneNumberString) || strlen($phoneNumberString) === 0) {
            return null;
        }

        // Replace all non-digits (except for the letter x) with nothing
        $cleaned = preg_replace('/[^0-9x]/i', '', $phoneNumberString);

        // Make sure the string contains an actual NANP phone number, and an optional extension (up to 6 digits)
        if (preg_match('/^1?([2-9][0-9]{2}[2-9][0-9]{2}[0-9]{4})(?:x([0-9]{1,6})(?:\b))?/i', $cleaned, $matches)) {
            $phoneNumber = '1' . $matches[1];
            if (isset($matches[2])) {
                $phoneNumber .= ' x' . $matches[2]; // Add "x1234" if there was one
            }

            return $phoneNumber;
        }

        return null;
    }
}
