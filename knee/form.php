<?php
/*
 * Помощник в создании и управлении формами ввода
 */

namespace Knee;

class Form
{
    /**
     * Открытие формы
     */
    public static function open($attrs = array())
    {
        return "<form".static::attributes($attrs).">";
    }

    /**
     * Закрытие формы
     */
    public static function close()
    {
        return '</form>';
    }

    /**
     * label(['for'=>"id_input", 'value'=>"It is label!!!"])
     */
    public static function label($attrs = array())
    {
        $value = static::array_key_delete('value', $attrs);

        return "<label".static::attributes($attrs).">".$value."</label>";
    }

    /**
     * input type="text"
     */
    public static function text($attrs = array())
    {
        $default = array();
        $default['type'] = "text";

        $attrs = $default + $attrs; // Что-бы атрибуты по умолчанию были в начале!

        return "<input".static::attributes($attrs).">";
    }

    /**
     * input type="password"
     */
    public static function password($attrs = array())
    {
        $default = array();
        $default['type'] = "password";

        $attrs = $default + $attrs;

        return "<input".static::attributes($attrs).">";
    }

    /**
     * input type="file"
     */
    public static function file($attrs = array())
    {
        $default = array();
        $default['type'] = "file";

        $attrs = $default + $attrs;

        return "<input".static::attributes($attrs).">";
    }

    /**
     * input type="hidden"
     */
    public static function hidden($attrs = array())
    {
        $default = array();
        $default['type'] = "hidden";

        $attrs = $default + $attrs;

        return "<input".static::attributes($attrs).">";
    }

    /**
     * textarea
     */
    public static function textarea($attrs = array())
    {
        $value = static::array_key_delete('value', $attrs);

        return "<textarea".static::attributes($attrs).">".$value."</textarea>";
    }

    /**
     * input type="submit"
     */
    public static function submit($attrs = array())
    {
        $default = array();
        $default['type'] = "submit";

        $attrs = $default + $attrs;

        return "<input".static::attributes($attrs).">";
    }

    /**
     * input type="button"
     */
    public static function button($attrs = array())
    {
        $default = array();
        $default['type'] = "button";

        $attrs = $default + $attrs;

        return "<input".static::attributes($attrs).">";
    }

    /**
     * <button></button>
     */
    public static function button2($attrs = array())
    {
        $default = array();
        $default['type'] = "button";

        $attrs = $default + $attrs;

        $value = static::array_key_delete('value', $attrs);

        return "<button".static::attributes($attrs).">".$value."</button>";
    }

    /**
     * input type="reset"
     */
    public static function reset($attrs = array())
    {
        $default = array();
        $default['type'] = "reset";

        $attrs = $default + $attrs;

        return "<input".static::attributes($attrs).">";
    }

    /**
     * input type="checkbox"
     */
    public static function checkbox($attrs = array())
    {
        $default = array();
        $default['type'] = "checkbox";

        $attrs = $default + $attrs;

        return "<input".static::attributes($attrs).">";
    }

    /**
     * input type="radio"
     */
    public static function radio($attrs = array())
    {
        $default = array();
        $default['type'] = "radio";

        $attrs = $default + $attrs;

        return "<input".static::attributes($attrs).">";
    }

    /**
     * select
     */
    public static function select($attrs = array())
    {
        $options = static::array_key_delete('options', $attrs);
        $selected = static::array_key_delete('selected', $attrs);

        if ($options == "") $options = array();
        if ($selected == "") $selected = null;

        $options_list = "";
        foreach ($options as $value=>$display) {
            if (is_array($display)) {
                $options_list[] = static::optgroup($display, $value, $selected);
            } else {
                $options_list[] = static::option($value, $display, $selected);
            }
        }

        return "<select".static::attributes($attrs).">".implode("\n", $options_list)."</select>";
    }

    /**
     * select optgroup
     */
    protected static function optgroup($options, $label, $selected)
    {
        $options_list = array();
        foreach ($options as $value=>$display) {
            $options_list[] = static::option($value, $display, $selected);
        }

        return "<optgroup label=\"".$label."\">".implode("\n", $options_list)."</optgroup>";
    }

    /**
     * select option
     */
    protected static function option($value, $display, $selected)
    {
        if (is_array($selected)) {
            $selected = (in_array($value, $selected)) ? true : false;
        } else {
            $selected = ($value == $selected) ? true : false;
        }

        $option_attrs = array();
        $option_attrs['value'] = $value;
        $option_attrs['selected'] = $selected;

        return "<option".static::attributes($option_attrs).">".$display."</option>";
    }

    /**
     * Строка атрибутов тегов
     */
    protected static function attributes($attrs)
    {
        $attr_list = array("");
        foreach ($attrs as $attr_name => $attr_value) {
            if (is_bool($attr_value)) {
                if ($attr_value == true) $attr_list[] = $attr_name;
            } else {
                $attr_list[] = $attr_name .'="'. $attr_value .'"';
            }
        }

        return (count($attr_list) > 1) ? implode(" ", $attr_list) : "";
    }

    /**
     * Удаление элемента массива по ключу
     * Возвращает значение удаленного элемента
     */
    protected static function array_key_delete($key, &$array)
    {
        $value = "";
        if (array_key_exists($key, $array)) {
            $value = $array[$key];
            unset($array[$key]);
        }

        return $value;
    }
}

?>