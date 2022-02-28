<?php

namespace Sitepilot\WpTheme\Acf;

use Sitepilot\Framework\Support\ServiceProvider;

class AcfServiceProvider extends ServiceProvider
{
    private AcfService $acf;

    /**
     * Bootstrap application services and hooks.
     */
    public function boot(AcfService $acf): void
    {
        if (!$acf->is_installed()) {
            $this->app->add_admin_notice('You need to install <a href="https://www.advancedcustomfields.com/" target="_blank">ACF</a> to use the activated theme.', 'error');
            return;
        }

        $this->acf = $acf;

        $this->add_shortcode('acf_field', 'field_shortcode');
        $this->add_shortcode('acf_option', 'option_shortcode');
    }

    /**
     * ACF option shortcode.
     */
    public function option_shortcode($atts)
    {
        $atts = shortcode_atts([
            'key' => '',
            'default' => null,
            'prefix' => '',
            'suffix' => ''
        ], $atts);

        return $atts['prefix'] . $this->acf->option($atts['key'], $atts['default']) . $atts['suffix'];
    }

    /**
     * ACF field shortcode.
     */
    public function field_shortcode($atts)
    {
        $atts = shortcode_atts([
            'key' => '',
            'default' => null,
            'post_id' => 0,
            'prefix' => '',
            'suffix' => ''
        ], $atts);

        return $atts['prefix'] . $this->acf->field($atts['key'], $atts['default'], $atts['post_id']) . $atts['suffix'];
    }
}
