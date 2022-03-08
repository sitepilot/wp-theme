<?php
global $template_args;

get_header();
?>

<div class="<?= implode(' ', $template_args['classes']) ?>" style="width: 100%;">
    <?= $template_args['content'] ?>
</div>

<?php get_footer(); ?>
