<?php

namespace Sitepilot\WpTheme\BeaverBuilder;

use Sitepilot\Framework\Support\ServiceProvider;

class BeaverBuilderServiceProvider extends ServiceProvider
{
    private BeaverBuilderService $beaverbuilder;

    /**
     * Bootstrap application services and hooks.
     */
    public function boot(BeaverBuilderService $beaverbuilder): void
    {
        $this->beaverbuilder = $beaverbuilder;

        if (sitepilot_branding()->enabled()) {
            $this->add_filter('all_plugins', 'filter_plugins');

            if (!class_exists('FLBuilderWhiteLabel')) {
                require_once __DIR__ . '/includes/FLBuilderWhiteLabel.php';
                \FLBuilderWhiteLabel::set_service($beaverbuilder);
            }
        }
    }

    /**
     * Filter the WordPress plugins list.
     */
    public function filter_plugins(array $plugins): array
    {
        $namespace = 'bb-plugin/fl-builder.php';

        if (isset($plugins[$namespace])) {
            $plugins[$namespace]['Name'] = $this->beaverbuilder->name();
            $plugins[$namespace]['Description'] = $this->beaverbuilder->description();
            $plugins[$namespace]['PluginURI'] = $this->beaverbuilder->website();
            $plugins[$namespace]['Author'] = $this->beaverbuilder->author();
            $plugins[$namespace]['AuthorURI'] = $this->beaverbuilder->website();
            $plugins[$namespace]['Title'] = $this->beaverbuilder->name();
            $plugins[$namespace]['AuthorName'] = $this->beaverbuilder->author();;
        }

        return $plugins;
    }
}
