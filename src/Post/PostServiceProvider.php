<?php

namespace Sitepilot\WpTheme\Post;

use Sitepilot\WpTheme\Template\TemplateService;
use Sitepilot\Framework\Support\ServiceProvider;

class PostServiceProvider extends ServiceProvider
{
    private TemplateService $template;

    /**
     * Bootstrap application services and hooks.
     */
    public function boot(TemplateService $template): void
    {
        $this->template = $template;

        $this->add_shortcode('post_date', 'date_shortcode');
        $this->add_shortcode('post_title', 'title_shortcode');
        $this->add_shortcode('post_content', 'content_shortcode');
    }

    /**
     * Post date shortcode.
     *
     * @param array $atts
     */
    public function date_shortcode($atts): string
    {
        $atts = shortcode_atts([
            'post_id' => 0,
            'format' => get_option('date_format')
        ], $atts);

        return get_the_date($atts['format'], $atts['post_id']);
    }

    /**
     * Post title shortcode.
     *
     * @param array $atts
     */
    public function title_shortcode($atts): string
    {
        $atts = shortcode_atts([
            'post_id' => 0
        ], $atts);

        return get_the_title($atts['post_id']);
    }

    /**
     * Post content shortcode.
     *
     * @param array $atts
     */
    public function content_shortcode($atts): string
    {
        $atts = shortcode_atts([
            'post_id' => 0
        ], $atts);

        if ($this->template->post_type() != get_post_type()) {
            return apply_filters(
                'the_content',
                get_the_content(null, false, $atts['post_id'])
            );
        } else {
            // Prevent infinite content loop
            return '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ligula tortor, maximus id purus placerat, ultricies volutpat velit. Duis venenatis metus et diam laoreet sollicitudin. Sed iaculis interdum congue. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed consectetur viverra nulla, hendrerit interdum quam lacinia sed. Donec hendrerit ante nulla, id rutrum nulla accumsan at. Nulla facilisi. Aliquam aliquam magna aliquet nunc pretium pretium. Morbi ipsum urna, rhoncus in odio ut, feugiat aliquam magna. Cras ac metus nec nunc dictum accumsan. Vivamus molestie sem at enim sollicitudin, ac hendrerit ligula sodales. Cras volutpat sem urna, at consequat dolor sollicitudin et. Vestibulum posuere enim sit amet metus venenatis, ut tempor dui efficitur. Cras non turpis quis magna scelerisque placerat.</p>
                <p>Cras at luctus ex, sed eleifend lacus. Nullam interdum risus eget tortor pretium consectetur. Aliquam erat volutpat. Proin ut ex porta, mollis quam in, ornare dolor. Curabitur rhoncus justo ornare nibh placerat mattis. Sed non diam placerat felis mattis consectetur. Nullam fringilla urna sed nisi ornare dapibus. Suspendisse feugiat, mi quis pretium vulputate, velit lacus iaculis mauris, pellentesque aliquet neque nisl ut elit. Etiam ac ipsum condimentum, sollicitudin urna in, imperdiet purus.</p>';
        }
    }
}
