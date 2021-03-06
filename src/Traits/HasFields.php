<?php

namespace Sitepilot\Theme\Traits;

use Sitepilot\Theme\Block;
use Sitepilot\Theme\Fields\Post;
use Sitepilot\Theme\Fields\Text;
use Sitepilot\Theme\Fields\Color;
use Sitepilot\Theme\Fields\Group;
use Sitepilot\Theme\Fields\Image;
use Sitepilot\Theme\Fields\Editor;
use Sitepilot\Theme\Fields\Number;
use Sitepilot\Theme\Fields\Select;
use Sitepilot\Theme\Fields\Repeater;
use Sitepilot\Theme\Fields\Taxonomy;
use Sitepilot\Theme\Fields\Textarea;
use Sitepilot\Theme\Fields\Dimension;
use Sitepilot\Theme\Fields\GoogleMap;
use Sitepilot\Theme\Fields\Preset\YesNo;
use Sitepilot\Theme\Fields\Style\Height;
use Sitepilot\Theme\Fields\Style\Margin;
use Sitepilot\Theme\Fields\Style\Opacity;
use Sitepilot\Theme\Fields\Style\Padding;
use Sitepilot\Theme\Fields\Style\Rounded;
use Sitepilot\Theme\Fields\Style\FontSize;
use Sitepilot\Theme\Fields\Style\MaxWidth;
use Sitepilot\Theme\Fields\Style\BoxShadow;
use Sitepilot\Theme\Fields\Style\TextAlign;
use Sitepilot\Theme\Fields\Style\TextColor;
use Sitepilot\Theme\Fields\Preset\ImageSize;
use Sitepilot\Theme\Fields\Style\BackgroundColor;
use Sitepilot\Theme\Fields\Style\BackgroundAttachment;

trait HasFields
{
    public function fields(): array
    {
        return [];
    }

    public function field_namespace()
    {
        if ($this instanceof Block) {
            return $this->config->id;
        } else {
            return 'theme';
        }
    }

    static public function get_field($key, $post_id = null, $default = null)
    {
        $value = null;

        if (function_exists('get_field')) {
            $value = get_field($key, $post_id);
        }

        return $value ? $value : $default;
    }

    public function field_image($name, $attribute)
    {
        return Image::make($name, $attribute, $this->field_namespace());
    }

    public function field_text($name, $attribute)
    {
        return Text::make($name, $attribute, $this->field_namespace());
    }

    public function field_textarea($name, $attribute)
    {
        return Textarea::make($name, $attribute, $this->field_namespace());
    }

    public function field_number($name, $attribute)
    {
        return Number::make($name, $attribute, $this->field_namespace());
    }

    public function field_editor($name, $attribute)
    {
        return Editor::make($name, $attribute, $this->field_namespace());
    }

    public function field_repeater($name, $attribute)
    {
        return Repeater::make($name, $attribute, $this->field_namespace());
    }

    public function field_post($name, $attribute)
    {
        return Post::make($name, $attribute, $this->field_namespace());
    }

    public function field_taxonomy($name, $attribute)
    {
        return Taxonomy::make($name, $attribute, $this->field_namespace());
    }

    public function field_google_map($name, $attribute)
    {
        return GoogleMap::make($name, $attribute, $this->field_namespace());
    }

    public function field_select($name, $attribute)
    {
        return Select::make($name, $attribute, $this->field_namespace());
    }

    public function field_color($name, $attribute)
    {
        return Color::make($name, $attribute, $this->field_namespace());
    }

    public function field_group($name, $attribute)
    {
        return Group::make($name, $attribute, $this->field_namespace());
    }

    public function field_sp_dimension($name, $attribute)
    {
        return Dimension::make($name, $attribute, $this->field_namespace());
    }

    public function field_style_max_width($name, $attribute)
    {
        return MaxWidth::make($name, $attribute, $this->field_namespace());
    }

    public function field_style_text_color($name, $attribute)
    {
        return TextColor::make($name, $attribute, $this->field_namespace());
    }

    public function field_style_text_align($name, $attribute)
    {
        return TextAlign::make($name, $attribute, $this->field_namespace());
    }

    public function field_style_bg_color($name, $attribute)
    {
        return BackgroundColor::make($name, $attribute, $this->field_namespace());
    }

    public function field_style_bg_attachment($name, $attribute)
    {
        return BackgroundAttachment::make($name, $attribute, $this->field_namespace());
    }

    public function field_style_padding($name, $attribute)
    {
        return Padding::make($name, $attribute, $this->field_namespace());
    }

    public function field_style_rounded($name, $attribute)
    {
        return Rounded::make($name, $attribute, $this->field_namespace());
    }

    public function field_style_box_shadow($name, $attribute)
    {
        return BoxShadow::make($name, $attribute, $this->field_namespace());
    }

    public function field_style_font_size($name, $attribute)
    {
        return FontSize::make($name, $attribute, $this->field_namespace());
    }

    public function field_style_height($name, $attribute)
    {
        return Height::make($name, $attribute, $this->field_namespace());
    }

    public function field_style_opacity($name, $attribute)
    {
        return Opacity::make($name, $attribute, $this->field_namespace());
    }

    public function field_style_margin($name, $attribute)
    {
        return Margin::make($name, $attribute, $this->field_namespace());
    }

    public function field_preset_yes_no($name, $attribute)
    {
        return YesNo::make($name, $attribute, $this->field_namespace());
    }

    public function field_preset_image_size($name, $attribute)
    {
        return ImageSize::make($name, $attribute, $this->field_namespace());
    }
}
