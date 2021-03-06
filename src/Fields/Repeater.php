<?php

namespace Sitepilot\Theme\Fields;

class Repeater extends Field
{
    /**
     * The repeatable fields.
     *
     * @var array
     */
    public $fields = [];

    /**
     * Returns the ACF field configuration.
     *
     * @return array
     */
    protected function config($prefix = true): array
    {
        $subfields = [];
        foreach ($this->fields as $field) {
            $subfields[] = $field->get_config('acf', false);
        }

        return [
            'type' => 'repeater',
            'layout' => 'block',
            'button_label' => __('New item', 'sitepilot-block'),
            'sub_fields' => $subfields
        ];
    }

    /**
     * Set the repeater fields.
     *
     * @param array $fields
     * @return self
     */
    public function fields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }
}
