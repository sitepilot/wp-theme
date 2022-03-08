<?php

namespace Sitepilot\WpTheme\Element;

use Sitepilot\Framework\Support\ServiceProvider;
use Sitepilot\WpTheme\Support\Str;

class ElementServiceProvider extends ServiceProvider
{
    /**
     * The element service instance.
     */
    private ElementService $element;

    /**
     * Bootstrap application services and hooks.
     */
    public function boot(ElementService $element): void
    {
        $this->element = $element;

        $this->add_action('init', 'register_post_type');
        $this->add_filter('template_include', 'filter_element_include');
        $this->add_filter('generateblocks_do_content', 'filter_generateblocks_content');
    }

    /**
     * Register element post type.
     */
    public function register_post_type(): void
    {
        register_post_type(
            $this->element->post_type(),
            array(
                'labels' => array(
                    'name' => __('Elements'),
                    'singular_name' => __('Element')
                ),
                'public' => true,
                'has_archive' => false,
                'show_in_rest' => true,
                'show_in_menu' => 'sitepilot-menu',
                'supports' => array('title', 'editor', 'thumbnail')
            )
        );
    }

    /**
     * Render an element.
     */
    public function render(?string $slug): ?string
    {
        global $element_args;

        $element = get_page_by_path($slug, OBJECT, $this->element->post_type());

        if ($element) {
            $class = $this->app->namespace('element', '-');

            $element_args['classes'] = array_merge([$class, "$class-{$element->ID}", "$class-$slug"], $this->element->classes());
            $element_args['content'] = apply_filters('the_content', $element->post_content);

            ob_start();
            include __DIR__ . '/includes/element.php';
            return ob_get_clean();
        }

        return null;
    }

    /**
     * Filter the path of the current element before including it.
     *
     * @param string $template
     */
    public function filter_element_include($template): string
    {
        global $template_args;

        $element = $this->render($this->element->template());

        if ($element && !is_null($this->element->template())) {
            $class = $this->app->namespace('template', '-');

            $template_args['classes'] = array_merge([$class], $this->element->template_classes());
            $template_args['content'] = $element;

            return $this->element->template_file();
        }

        return $template;
    }

    /**
     * Allow GeneratePress to analyze the element content and inject styles.
     *
     * @return string
     */
    public function filter_generateblocks_content(string $content): ?string
    {
        $element = get_page_by_path($this->element->template(), OBJECT, $this->element->post_type());

        if ($element) {
            $content .= $element->post_content;
        }

        return $content;
    }
}
