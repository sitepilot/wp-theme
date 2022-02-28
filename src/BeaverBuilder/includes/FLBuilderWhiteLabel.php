<?php

use Sitepilot\Framework\Foundation\Application;
use Sitepilot\WpTheme\BeaverBuilder\BeaverBuilderService;

/**
 * White labeling for the builder.
 *
 * @since 1.8
 */
final class FLBuilderWhiteLabel
{
    /**
     * The beaver builder service instance.
     */
    protected static BeaverBuilderService $beaverbuilder;

    /**
     * Set beaver builder service instance.
     */
    public static function set_service(BeaverBuilderService $beaverbuilder): void
    {
        self::$beaverbuilder = $beaverbuilder;
    }

    /**
     * Checks if white label is enabled. Will check for the default branding
     * and Page Builder since Page Builder was the original default.
     *
     * @since 2.1
     * @return bool
     */
    static public function is_white_labeled()
    {
        return true;
    }

    /**
     * Returns the custom branding string.
     *
     * @since 1.3.1
     * @return string
     */
    static public function get_branding()
    {
        return static::$beaverbuilder->name();
    }

    /**
     * Returns the custom branding icon URL.
     *
     * @since 1.3.7
     * @return string
     */
    static public function get_branding_icon()
    {
        return static::$beaverbuilder->icon();
    }

    /**
     * Returns the settings for the builder's help button.
     *
     * @since 1.4.9
     * @return array
     */
    static public function get_help_button_settings()
    {
        $help_button['enabled'] = false;

        return $help_button;
    }

    /**
     * Prevent "function not defined" errors.
     *
     * @param string $name
     * @param array $arguments
     * @return bool
     */
    static public function __callStatic($name, $arguments)
    {
        return null;
    }
}
