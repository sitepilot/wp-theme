<?php

namespace Sitepilot\WpTheme\Acf\Fields;

class ClassSelect extends \acf_field
{
    function __construct()
    {
        $this->name     = 'sp_class_select';
        $this->label    = _x('Class Select', 'noun', 'sp-theme');
        $this->category = 'choice';
        $this->defaults = array(
            'choices' => array(),
            'default_value' => '',
            'default_choice' => ''
        );

        parent::__construct();
    }

    function render_field(array $field): void
    {
        $choices = $field['choices'];

        $choices = array_merge([
            'default' => ['label' => (!empty($choices[$field['default_choice']]['label']) ? 'Default (' . strtolower($choices[$field['default_choice']]['label']) . ')' : '-')]
        ], $choices);
?>
        <select name="<?= $field['name'] ?>">
            <?php foreach ($choices as $key => $value) : ?>
                <option value="<?= $key ?>" <?= $field['value'] == $key ? 'selected' : '' ?>><?= $value['label'] ?></option>
            <?php endforeach ?>
        </select>
<?php
    }

    function format_value($value, $post_id, $field): ?string
    {
        return isset($field['choices'][$value]['class']) ? $field['choices'][$value]['class'] : $field['choices'][$field['default_choice']]['class'] ?? null;
    }
}
