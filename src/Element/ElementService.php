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
        return $this->app->filter('element/post_type', $this->app->namespace());
    }

    /**
     * Get template include file.
     */
    public function template_file(): string
    {
        return $this->app->filter('element/file', __DIR__ . '/includes/template.php');
    }

    /**
     * Render an element.
     */
    public function render(?string $slug): ?string
    {
        global $element_args;

        $element = get_page_by_path($slug, OBJECT, $this->post_type());

        if ($element) {
            $class = $this->app->namespace('element', '-');

            $element_args['classes'] = array_merge([$class, "$class-{$element->ID}", "$class-$slug"], $this->classes());
            $element_args['content'] = apply_filters('the_content', $element->post_content);

            ob_start();
            include __DIR__ . '/includes/element.php';
            return ob_get_clean();
        }

        return null;
    }
}
