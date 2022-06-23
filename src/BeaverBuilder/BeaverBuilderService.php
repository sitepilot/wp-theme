<?php

namespace Sitepilot\WpTheme\BeaverBuilder;

use Sitepilot\Framework\Foundation\Application;

class BeaverBuilderService
{
    /**
     * The application instance.
     */
    private Application $app;

    /**
     * Create a new Beaver Builder service instance.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the branding name.
     */
    public function name(): string
    {
        return $this->app->filter('beaverbuilder/name', sprintf('%s Builder', sitepilot_branding()->name()));
    }

    /**
     * Get the branding description.
     */
    public function description()
    {
        return $this->app->filter('beaverbuilder/description', 'A drag and drop frontend page builder plugin that works with almost any theme.');
    }

    /**
     * Get the branding website.
     */
    public function website()
    {
        return $this->app->filter('beaverbuilder/website', sitepilot_branding()->website());
    }

    /**
     * Get the branding author.
     */
    public function author()
    {
        return $this->app->filter('beaverbuilder/author', sitepilot_branding()->name());
    }

    /**
     * Get the branding icon.
     */
    public function icon()
    {
        return $this->app->filter('beaverbuilder/icon', sitepilot_branding()->icon());
    }
}
