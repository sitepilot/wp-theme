<?php

namespace Sitepilot\WpTheme\Element;

use Sitepilot\Framework\Foundation\Application;

class ElementService
{
    /**
     * The application instance.
     */
    private Application $app;

    /**
     * Create a new element service instance.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the template slug to include.
     */
    public function template(): ?string
    {
        return $this->app->filter('element/template', null);
    }

    /**
     * Get the element classes.
     */
    public function classes(): array
    {
        return $this->app->filter('element/classes', []);
    }

    /**
     * Get the template classes.
     */
    public function template_classes(): array
    {
        return $this->app->filter('element/template_classes', []);
    }

    /**
     * Get the element post type.
     */
    public function post_type(): string
    {
        return $this->app->filter('element/post_type', 'sp_elements');
    }

    /**
     * Get template include file.
     */
    public function template_file(): string
    {
        return $this->app->filter('element/file', __DIR__ . '/includes/template.php');
    }
}
