<?php

namespace Sitepilot\WpTheme\Support;

class Url
{
    /**
     * Get tel href for the given phone number.
     */
    public static function tel(string $phone, string $country = 'nl'): string
    {
        return 'tel:' . static::phone($phone, $country);
    }

    /**
     * Get mailto href for the given email.
     */
    public static function mailto(string $email): string
    {
        return 'mailto:' . trim($email);
    }

    /**
     * Get WhatsApp href for the given phone number.
     */
    public static function whatsapp(string $phone, string $country = 'nl'): string
    {
        return 'https://wa.me/' . str_replace('+', '', static::phone($phone, $country));
    }

    /**
     * Format the given phone number.
     */
    public static function phone(string $phone, string $country = 'nl'): string
    {
        $phone = str_replace(['(', ')', '-', '[', ']'], '', trim($phone));

        $country_codes = array(
            'us' => '+1',
            'nl' => '+31',
            'de' => '+43',
            'be' => '+32',
            'gb' => '+44'
        );

        return preg_replace('/[^0-9+]/', '', preg_replace('/^0/', $country_codes[$country], $phone));
    }
}
