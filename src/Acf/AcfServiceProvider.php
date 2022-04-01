<?php

namespace Sitepilot\WpTheme\Acf;

use Sitepilot\WpTheme\Acf\Fields\ClassSelect;
use Sitepilot\Framework\Support\ServiceProvider;

class AcfServiceProvider extends ServiceProvider
{
    private AcfService $acf;

    /**
     * Register application services and hooks.
     */
    public function register(): void
    {
        $this->app->alias(AcfService::class, 'acf');
    }

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

        $this->add_action('acf/include_field_types', 'include_field_types');
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
            'suffix' => '',
            'allowed_tags' => ''
        ], $atts);

        $value = $this->acf->option($atts['key'], $atts['default']);

        if (!empty($atts['allowed_tags'])) {
            $value = strip_tags($value, explode(',', $atts['allowed_tags']));
        }

        return $atts['prefix'] . $value . $atts['suffix'];
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
            'suffix' => '',
            'allowed_tags' => ''
        ], $atts);

        $value = $this->acf->field($atts['key'], $atts['default'], $atts['post_id']);

        if (!empty($atts['allowed_tags'])) {
            $value = strip_tags($value, explode(',', $atts['allowed_tags']));
        }

        return $atts['prefix'] . $value . $atts['suffix'];
    }

    /**
     * Register customm ACF fields.
     *
     * @return void
     */
    public function include_field_types(): void
    {
        new ClassSelect();
    }
}
