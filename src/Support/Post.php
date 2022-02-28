<?php

namespace Sitepilot\WpTheme\Support;

class Post
{
    /**
     * Get post id by slug.
     */
    public static function id(string $slug, string $post_type = 'page'): ?int
    {
        if ($post = get_page_by_path($slug, OBJECT, $post_type)) {
            return $post->ID;
        }

        return null;
    }

    /**
     * Get post content by slug, id or post object.
     *
     * @param string|int|\WP_Post $post
     */
    public static function content($post, string $post_type = 'page'): ?string
    {
        if (is_string($post)) {
            if ($post = get_page_by_path($post, OBJECT, $post_type)) {
                return apply_filters('the_content', $post->post_content);
            }
        } else {
            if ($post = get_post($post, OBJECT)) {
                return apply_filters('the_content', $post->post_content);
            }
        }

        return '';
    }
}
