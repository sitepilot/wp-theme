<?php

namespace Sitepilot\WpTheme\Template;

use Sitepilot\Framework\Foundation\Application;

class TemplateService
{
    /**
     * The application instance.
     */
    private Application $app;

    /**
     * Create a new template service instance.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the template slug to include.
     */
    public function slug(): ?string
    {
        return $this->app->filter('template/slug', null);
    }

    /**
     * Get the template classes.
     */
    public function classes(): array
    {
        return $this->app->filter('template/classes', []);
    }

    /**
     * Get the template post type.
     */
    public function post_type(): string
    {
        return $this->app->filter('template/post_type', $this->app->namespace('tmpl', '-'));
    }

    /**
     * Get template include file.
     */
    public function file(): string
    {
        return $this->app->filter('template/file', __DIR__ . '/includes/template.php');
    }
}
