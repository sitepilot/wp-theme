<?php

namespace Sitepilot\WpTheme\Acf;

use Sitepilot\WpTheme\Support\Arr;
use Sitepilot\WpTheme\Support\Str;
use Sitepilot\Framework\Foundation\Application;

class AcfService
{
    /**
     * The application instance.
     */
    private Application $app;

    /**
     * Create a new beaver builder service instance.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * The cache for registered field groups.
     */
    private static array $field_groups_cache = [];

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
     * @param mixed $post
     * @return mixed
     */
    public function field(string $key, $default = null, $post = 0)
    {
        if (function_exists('get_field')) {
            return get_field($key, $post) ?: $default;
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
        return Arr::get($map, $this->field($key, $default));
    }

    /**
     * Map option value to another value.
     *
     * @param mixed $default
     * @return mixed
     */
    public function map_option(string $key, array $map, $default = null)
    {
        return Arr::get($map, $this->option($key, $default));
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
     * Add a field group.
     */
    public function add_fields(string $namespace, array $config): void
    {
        // Checks if the config is an array with only fields
        if (empty($config['fields']) && !empty($config[0]['name'])) {
            $config['fields'] = $config;
            foreach (array_keys($config) as $key) {
                if (!is_string($key)) {
                    unset($config[$key]);
                }
            }
        }

        $config['fields'] = $this->parse_fields($namespace, $config['fields'] ?? []);

        $config = wp_parse_args($config, [
            'key' => 'group_' . md5($namespace . '_' . count(static::$field_groups_cache[$namespace] ?? [])),
            'title' => Str::title($namespace),
            'position' => 'normal'
        ]);

        static::$field_groups_cache[$namespace][] = $config;

        add_action('acf/init', function () use ($config) {
            acf_add_local_field_group($config);
        });
    }

    private function parse_fields(string $namespace, array $fields)
    {
        $fields = Arr::where($fields ?? [], function ($field) {
            return !empty($field['name']);
        });

        foreach ($fields as &$field) {
            if (!empty($field['sub_fields'])) {
                $field['sub_fields'] = $this->parse_fields($namespace, $field['sub_fields']);
            }

            $field = wp_parse_args($field, [
                'key' => 'field_' . md5($namespace . '_' . $field['name']),
                'label' => Str::title($field['name'])
            ]);
        }

        return $fields;
    }

    /**
     * Add option page field group.
     *
     * @see https://www.advancedcustomfields.com/resources/register-fields-via-php/
     */
    public function add_option_fields(string $page_id, array $config = []): void
    {
        $config = wp_parse_args($config, [
            'location' => [[
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => $page_id
                ]
            ]]
        ]);

        $this->add_fields("opt_{$page_id}", $config);
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
     * Add block field group.
     *
     * @see https://www.advancedcustomfields.com/resources/register-fields-via-php/
     */
    public function add_block_fields(string $block_id, array $config = []): void
    {
        $config = wp_parse_args($config, [
            'location' => [[
                [
                    'param' => 'block',
                    'operator' => '==',
                    'value' => "acf/$block_id",
                ]
            ]]
        ]);

        $this->add_fields("block_{$block_id}", $config);
    }

    /**
     * Add post type field group.
     *
     * @see https://www.advancedcustomfields.com/resources/register-fields-via-php/
     */
    public function add_post_type_fields(string $post_type, array $config = []): void
    {
        $config = wp_parse_args($config, [
            'location' => [[
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => $post_type,
                ]
            ]]
        ]);

        $this->add_fields("cpt_{$post_type}", $config);
    }

    /**
     * Add taxonomy field group.
     *
     * @see https://www.advancedcustomfields.com/resources/register-fields-via-php/
     */
    public function add_taxonomy_fields(string $taxonomy, array $config = []): void
    {
        $config = wp_parse_args($config, [
            'location' => [[
                [
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => $taxonomy,
                ]
            ]]
        ]);

        $this->add_fields("tax_{$taxonomy}", $config);
    }

    /**
     * Get block template.
     */
    public function block_template(string $template, array $block, array $data = [], array $classes = []): string
    {
        $args = $this->block_template_data($block, $data, $classes);

        return $this->app->template($template, $args['style'], $args);
    }

    /**
     * Get block template data.
     */
    public function block_template_data(array $block, array $data = [], array $classes = []): array
    {
        return wp_parse_args($data, [
            'block' => $block,
            'name' => str_replace('acf/', '', $block['name']),
            'class' => str_replace('acf/', '', $block['name']) . '-block',
            'style' => $this->block_style($block),
            'attributes' => $this->block_attributes($block, $classes)
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

        return "class=\"" . $this->dynamic_class($classes) . "\" id=\"{$id}\"";
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

    /**
     * Get dynamic class string.
     */
    public function dynamic_class(array $classes): string
    {
        foreach ($classes as &$class) {
            if (substr($class, 0, 6) == 'field:') {
                $class = $this->field(substr($class, 6));
            }
        }

        return implode(' ', array_filter($classes));
    }

    /**
     * Get block style without prefix.
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
     * Get block style class.
     */
    public function block_style_class(array $block, array $classes): array
    {
        $keys = array();

        foreach ($classes as $item) {
            foreach (array_keys($item) as $key) {
                if (!in_array($key, $keys)) $keys[] = $key;
            }
        }

        foreach ($keys as $key) {
            $return[$key] =  implode(" ", $classes[$this->block_style($block)][$key] ?? $classes['default'][$key] ?? []);
        }

        return $return;
    }
}
