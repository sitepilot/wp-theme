<?php
global $template_args;

$classes = array_merge([
    $template_args['post_type']
], $template_args['classes']);

get_header();
?>

<div class="<?= implode(' ', $classes) ?>">
    <?= \Sitepilot\WpTheme\Support\Post::content($template_args['slug'], $template_args['post_type']) ?>
</div>

<?php get_footer(); ?>
