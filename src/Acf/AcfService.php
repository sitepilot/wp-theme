<?php

namespace Sitepilot\WpTheme\Acf;

use Sitepilot\WpTheme\Support\Arr;
use Sitepilot\WpTheme\Support\Str;

class AcfService
{
    /**
     * The cache for registered block fields.
     */
    private static array $block_fields_cache = [];

    /**
     * The cache for registered theme options.
     */
    private static array $option_fields_cache = [];

    /**
     * Determine if ACF is installed.
     */
    public function is_installed(): bool
    {
        return class_exists('ACF');
    }

    /**
     * Get option value.
     *
     * @param mixed $default
     * @return mixed
     */
    public function option(string $key, $default = null)
    {
        if (function_exists('get_field')) {
            return get_field($key, 'option') ?: $default;
        }

        return null;
    }

    /**
     * Get field value.
     *
     * @param mixed $default
     * @return mixed
     */
    public function field(string $key, $default = null, int $post_id = 0)
    {
        if (function_exists('get_field')) {
            return get_field($key, $post_id) ?: $default;
        }

        return null;
    }

    /**
     * Map field value to another value.
     *
     * @param mixed $default
     * @return mixed
     */
    public function map_field(string $key, array $map, $default = null)
    {
        return Arr::get($map, $this->field($key), $default);
    }

    /**
     * Map option value to another value.
     *
     * @param mixed $default
     * @return mixed
     */
    public function map_option(string $key, array $map, $default = null)
    {
        return Arr::get($map, $this->option($key), $default);
    }

    /**
     * Add option page.
     *
     * @see https://www.advancedcustomfields.com/resources/options-page/
     */
    public function add_option_page(string $page_id, array $config = []): void
    {
        $config['menu_slug'] = $page_id;

        $config = wp_parse_args($config, [
            'page_title' => Str::studly($page_id)
        ]);

        add_action('acf/init', function () use ($config) {
            acf_add_options_page($config);
        });
    }

    /**
     * Add sub option page.
     *
     * @see https://www.advancedcustomfields.com/resources/options-page/
     */
    public function add_option_sub_page(string $page_id, string $parent_id, array $config): void
    {
        $config['menu_slug'] = $page_id;
        $config['parent_slug'] = $parent_id;

        $config = wp_parse_args($config, [
            'page_title' => Str::studly($page_id)
        ]);

        add_action('acf/init', function () use ($config) {
            acf_add_options_sub_page($config);
        }, 15);
    }

    /**
     * Add option page fields.
     *
     * @see https://www.advancedcustomfields.com/resources/register-fields-via-php/
     */
    public function add_option_fields(string $page_id, string $title, array $fields, array $config = []): void
    {
        $fields = Arr::where($fields, function ($field) {
            return !empty($field['name']);
        });

        foreach ($fields as &$field) {
            $field = wp_parse_args($field, [
                'key' => $page_id . '_' . $field['name'],
                'label' => Str::title($field['name'])
            ]);
        }

        $config = wp_parse_args($config, [
            'key' => $page_id . '_group_' . count(static::$option_fields_cache[$page_id] ?? []),
            'title' => $title,
            'fields' => $fields,
            'location' => [[
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => $page_id
                ]
            ]]
        ]);

        static::$option_fields_cache[$page_id][] = $config;

        add_action('acf/init', function () use ($config) {
            acf_add_local_field_group($config);
        });
    }

    /**
     * Add a block type.
     *
     * @see https://www.advancedcustomfields.com/resources/acf_register_block_type/
     */
    public function add_block(string $block_id, array $config = []): void
    {
        $config['name'] = $block_id;

        $config = wp_parse_args($config, [
            'title' => Str::studly($block_id)
        ]);

        add_action('acf/init', function () use ($config) {
            acf_register_block_type($config);
        });
    }

    /**
     * Add block fields.
     *
     * @see https://www.advancedcustomfields.com/resources/register-fields-via-php/
     */
    public function add_block_fields(string $block_id, array $fields, array $config = []): void
    {
        $fields = Arr::where($fields, function ($field) {
            return !empty($field['name']);
        });

        foreach ($fields as &$field) {
            $field = wp_parse_args($field, [
                'key' => $block_id . '_' . $field['name'],
                'label' => Str::title($field['name'])
            ]);
        }

        $config = wp_parse_args($config, [
            'key' => $block_id . '_group_' . count(static::$block_fields_cache[$block_id] ?? []),
            'fields' => $fields,
            'location' => [[
                [
                    'param' => 'block',
                    'operator' => '==',
                    'value' => "acf/$block_id",
                ]
            ]]
        ]);

        static::$block_fields_cache[$block_id][] = $config;

        add_action('acf/init', function () use ($config) {
            acf_add_local_field_group($config);
        });
    }

    /**
     * Get block template.
     */
    public function block_template(string $template, array $block, array $data = []): string
    {
        $args = $this->block_template_data($block, $data);

        ob_start();
        get_template_part($template, $args['style'], $args);
        $template = ob_get_contents();
        ob_end_clean();

        return $template;
    }

    /**
     * Get block template data.
     */
    public function block_template_data(array $block, array $merge = []): array
    {
        return wp_parse_args($merge, [
            'block' => $block,
            'name' => str_replace('acf/', '', $block['name']),
            'class' => str_replace('acf/', '', $block['name']) . '-block',
            'style' => $this->block_style($block),
            'attributes' => $this->block_attributes($block)
        ]);
    }

    /**
     * Get block attributes.
     */
    public function block_attributes(array $block, array $classes = array()): string
    {
        $id = $block['id'];
        $name = str_replace('acf/', '', $block['name']) . '-block';

        if (!empty($block['anchor'])) {
            $id = $block['anchor'];
        }

        array_unshift($classes, $name);

        if (!empty($block['className'])) {
            array_push($classes, $block['className']);
        }

        if (!empty($block['textColor'])) {
            array_push($classes, 'has-' . $block['textColor'] . '-color');
        }

        if (!empty($block['backgroundColor'])) {
            array_push($classes, 'has-' . $block['backgroundColor'] . '-background-color');
        }

        if (!empty($block['gradient'])) {
            array_push($classes, 'has-' . $block['gradient'] . '-gradient-background');
        }

        if (!empty($block['align'])) {
            array_push($classes, 'align' . $block['align']);
        }

        if (!empty($block['fontSize'])) {
            array_push($classes, 'has-' . $block['fontSize'] . '-font-size');
        }

        return "class=\"" . implode(' ', $classes) . "\" id=\"{$id}\"";
    }

    /**
     * Get block style.
     */
    public function block_style(array $block): string
    {
        $match = array();

        if (preg_match('/is-style-[a-zA-Z0-9_-]*/', $block['className'] ?? '', $match)) {
            return str_replace(['is-style-sp-', 'is-style-'], '', reset($match));
        }

        return '';
    }

    /**
     * Get ACF inner blocks HTML.
     *
     * @see https://www.advancedcustomfields.com/resources/acf_register_block_type/
     */
    public function inner_blocks_html(array $allowed_blocks = [], array $template = [], string $lock = ''): string
    {
        $attributes = [];

        if ($allowed_blocks) {
            $attributes[] = 'allowedBlocks="' . esc_attr(json_encode($allowed_blocks)) . '"';
        }

        if ($template) {
            $attributes[] = 'template="' . esc_attr(json_encode([$template])) . '"';
        }

        if ($lock) {
            $attributes[] = 'templateLock="' . $lock . '"';
        }

        return '<InnerBlocks ' . implode(' ', $attributes) . '/>';
    }
}
