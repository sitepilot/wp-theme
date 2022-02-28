<?php

namespace Sitepilot\WpTheme\Template;

use Sitepilot\Framework\Support\ServiceProvider;

class TemplateServiceProvider extends ServiceProvider
{
    /**
     * The template service instance.
     */
    private TemplateService $template;

    /**
     * Bootstrap application services and hooks.
     */
    public function boot(TemplateService $template): void
    {
        $this->template = $template;

        $this->add_action('init', 'register_post_type');
        $this->add_filter('template_include', 'filter_template_include');
    }

    /**
     * Register template post type.
     */
    public function register_post_type(): void
    {
        register_post_type(
            $this->template->post_type(),
            array(
                'labels' => array(
                    'name' => __('Templates'),
                    'singular_name' => __('Template')
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
     * Filter the path of the current template before including it.
     *
     * @param string $template
     */
    public function filter_template_include($template): string
    {
        global $post;
        global $template_args;

        $template_args['slug'] = $this->template->slug();
        $template_args['classes'] = $this->template->classes();
        $template_args['post_type'] = $this->template->post_type();

        if ($post && $template_args['post_type'] == get_post_type()) {
            $template_args['slug'] = $post->post_name;
        }

        if ($template_args['slug']) {
            return $this->template->file();
        }

        return $template;
    }
}
