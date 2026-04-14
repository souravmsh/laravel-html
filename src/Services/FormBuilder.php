<?php

namespace Souravmsh\Html\Services;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Traits\Macroable;

/**
 * Drop-in replacement for laravelcollective/html Form facade.
 * Supports Laravel 10, 11, and 12 without any blade template changes.
 */
class FormBuilder
{
    use Macroable;

    /** @var mixed The current model instance for model binding */
    protected mixed $model = null;

    /** @var array Labels for fields (used for accessibility) */
    protected array $labels = [];

    // -------------------------------------------------------------------------
    // Form open / close
    // -------------------------------------------------------------------------

    /**
     * Open a form tag.
     *
     * @param array $options
     * @return HtmlString
     */
    public function open(array $options = []): HtmlString
    {
        $method = strtoupper($options['method'] ?? 'POST');
        $spoofedMethod = null;

        if (in_array($method, ['PUT', 'PATCH', 'DELETE'])) {
            $spoofedMethod = $method;
            $method = 'POST';
        }

        $attributes = $this->buildAttributes(array_merge($options, [
            'method'         => $method,
            'action'         => $this->getAction($options),
            'accept-charset' => 'UTF-8',
        ]), ['url', 'files', 'model', 'route']);

        if (!empty($options['files'])) {
            $attributes['enctype'] = 'multipart/form-data';
        }

        $html = '<form' . $this->attributesToString($attributes) . '>';
        $html .= csrf_field();

        if ($spoofedMethod) {
            $html .= '<input type="hidden" name="_method" value="' . $spoofedMethod . '">';
        }

        return new HtmlString($html);
    }

    /**
     * Open a form bound to a model instance for value pre-population.
     *
     * @param mixed $model
     * @param array $options
     * @return HtmlString
     */
    public function model(mixed $model, array $options = []): HtmlString
    {
        $this->model = $model;
        return $this->open($options);
    }

    /**
     * Close the form tag and unbind any model.
     *
     * @return HtmlString
     */
    public function close(): HtmlString
    {
        $this->model = null;
        return new HtmlString('</form>');
    }

    // -------------------------------------------------------------------------
    // Input helpers
    // -------------------------------------------------------------------------

    /**
     * Create a label element.
     */
    public function label(string $name, ?string $value = null, array $attributes = []): HtmlString
    {
        $this->labels[] = $name;
        $attrs = array_merge(['for' => $name], $attributes);
        $text  = e($value ?? ucwords(str_replace(['-', '_'], ' ', $name)));
        return new HtmlString('<label' . $this->attributesToString($attrs) . '>' . $text . '</label>');
    }

    /**
     * Create a text input.
     */
    public function text(string $name, mixed $value = null, array $attributes = []): HtmlString
    {
        return $this->input('text', $name, $value, $attributes);
    }

    /**
     * Create an email input.
     */
    public function email(string $name, mixed $value = null, array $attributes = []): HtmlString
    {
        return $this->input('email', $name, $value, $attributes);
    }

    /**
     * Create a password input (never pre-fills value).
     */
    public function password(string $name, array $attributes = []): HtmlString
    {
        return $this->input('password', $name, null, $attributes);
    }

    /**
     * Create a number input.
     */
    public function number(string $name, mixed $value = null, array $attributes = []): HtmlString
    {
        return $this->input('number', $name, $value, $attributes);
    }

    /**
     * Create a date input.
     */
    public function date(string $name, mixed $value = null, array $attributes = []): HtmlString
    {
        return $this->input('date', $name, $value, $attributes);
    }

    /**
     * Create a url input.
     */
    public function url(string $name, mixed $value = null, array $attributes = []): HtmlString
    {
        return $this->input('url', $name, $value, $attributes);
    }

    /**
     * Create a hidden input.
     */
    public function hidden(string $name, mixed $value = null, array $attributes = []): HtmlString
    {
        return $this->input('hidden', $name, $value, $attributes);
    }

    /**
     * Create a file input.
     */
    public function file(string $name, array $attributes = []): HtmlString
    {
        return $this->input('file', $name, null, $attributes);
    }

    /**
     * Create a submit input.
     */
    public function submit(?string $value = null, array $attributes = []): HtmlString
    {
        return $this->input('submit', '', $value, $attributes);
    }

    /**
     * Create a button element.
     */
    public function button(?string $value = null, array $attributes = []): HtmlString
    {
        $attrs = array_merge(['type' => 'button'], $attributes);
        return new HtmlString(
            '<button' . $this->attributesToString($attrs) . '>' . $value . '</button>'
        );
    }

    /**
     * Create a generic input element.
     */
    public function input(string $type, string $name, mixed $value = null, array $attributes = []): HtmlString
    {
        $id = $attributes['id'] ?? $name;

        // Resolve value: explicit → old() → model binding
        if ($value === null && $type !== 'password') {
            $value = $this->getValueFromModel($name) ?? old($name);
        }

        $attrs = array_merge([
            'type'  => $type,
            'name'  => $name,
            'id'    => $id,
            'value' => $value,
        ], $attributes);

        return new HtmlString('<input' . $this->attributesToString($attrs) . '>');
    }

    // -------------------------------------------------------------------------
    // Textarea
    // -------------------------------------------------------------------------

    /**
     * Create a textarea element.
     */
    public function textarea(string $name, mixed $value = null, array $attributes = []): HtmlString
    {
        $id = $attributes['id'] ?? $name;

        if ($value === null) {
            $value = $this->getValueFromModel($name) ?? old($name);
        }

        $attrs = array_merge([
            'name' => $name,
            'id'   => $id,
            'rows' => 5,
            'cols' => 50,
        ], $attributes);

        return new HtmlString(
            '<textarea' . $this->attributesToString($attrs) . '>' . e($value ?? '') . '</textarea>'
        );
    }

    // -------------------------------------------------------------------------
    // Select
    // -------------------------------------------------------------------------

    /**
     * Create a select element.
     *
     * @param string $name
     * @param array|\Illuminate\Support\Collection $list
     * @param mixed $selected
     * @param array $attributes
     * @return HtmlString
     */
    public function select(string $name, mixed $list = [], mixed $selected = null, array $attributes = []): HtmlString
    {
        $id = $attributes['id'] ?? $name;

        if ($selected === null) {
            $selected = $this->getValueFromModel($name) ?? old($name);
        }

        $attrs = array_merge(['name' => $name, 'id' => $id], $attributes);

        // Handle placeholder
        $placeholder = $attrs['placeholder'] ?? null;
        unset($attrs['placeholder']);

        if (is_object($list) && method_exists($list, 'toArray')) {
            $list = $list->toArray();
        }

        $options = '';

        if ($placeholder !== null) {
            $options .= '<option value="">' . e($placeholder) . '</option>';
        }

        foreach ((array) $list as $value => $display) {
            if (is_array($display)) {
                // Option group
                $options .= '<optgroup label="' . e($value) . '">';
                foreach ($display as $groupValue => $groupDisplay) {
                    $options .= $this->buildOption($groupValue, $groupDisplay, $selected);
                }
                $options .= '</optgroup>';
            } else {
                $options .= $this->buildOption($value, $display, $selected);
            }
        }

        return new HtmlString('<select' . $this->attributesToString($attrs) . '>' . $options . '</select>');
    }

    // -------------------------------------------------------------------------
    // Checkbox / Radio
    // -------------------------------------------------------------------------

    /**
     * Create a checkbox input.
     */
    public function checkbox(string $name, mixed $value = 1, ?bool $checked = null, array $attributes = []): HtmlString
    {
        return $this->checkable('checkbox', $name, $value, $checked, $attributes);
    }

    /**
     * Create a radio input.
     */
    public function radio(string $name, mixed $value = null, ?bool $checked = null, array $attributes = []): HtmlString
    {
        return $this->checkable('radio', $name, $value ?? $name, $checked, $attributes);
    }

    /**
     * Build a checkable (checkbox/radio) input.
     */
    protected function checkable(string $type, string $name, mixed $value, ?bool $checked, array $attributes): HtmlString
    {
        $id = $attributes['id'] ?? $name;

        if ($checked === null) {
            $modelValue = $this->getValueFromModel($name);
            $checked    = $modelValue !== null ? ((string) $modelValue === (string) $value) : false;
        }

        $attrs = array_merge([
            'type'  => $type,
            'name'  => $name,
            'id'    => $id,
            'value' => $value,
        ], $attributes);

        if ($checked) {
            $attrs['checked'] = 'checked';
        }

        return new HtmlString('<input' . $this->attributesToString($attrs) . '>');
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    /**
     * Attempt to get a value from the bound model.
     */
    protected function getValueFromModel(string $name): mixed
    {
        if ($this->model === null) {
            return null;
        }

        // Support dot-notation and bracket notation (e.g. "address[city]" → "address.city")
        $key = str_replace(['[', ']'], ['.', ''], $name);
        $key = trim($key, '.');

        if (is_array($this->model)) {
            return data_get($this->model, $key);
        }

        if (is_object($this->model)) {
            return data_get($this->model, $key);
        }

        return null;
    }

    /**
     * Build a single <option> element.
     */
    protected function buildOption(mixed $value, mixed $display, mixed $selected): string
    {
        $isSelected = false;

        if (is_array($selected)) {
            $isSelected = in_array((string) $value, array_map('strval', $selected), true);
        } elseif ($selected !== null) {
            $isSelected = (string) $value === (string) $selected;
        }

        $selectedAttr = $isSelected ? ' selected="selected"' : '';
        return '<option value="' . e($value) . '"' . $selectedAttr . '>' . e($display) . '</option>';
    }

    /**
     * Convert an attributes array to an HTML attribute string.
     *
     * @param array $attributes
     * @param array $except Keys to exclude
     * @return string
     */
    protected function attributesToString(array $attributes, array $except = []): string
    {
        $html = '';

        foreach ($attributes as $key => $value) {
            if (in_array($key, $except, true) || $value === null || $value === false) {
                continue;
            }

            if ($value === true) {
                $html .= ' ' . $key;
            } else {
                $html .= ' ' . $key . '="' . e($value) . '"';
            }
        }

        return $html;
    }

    /**
     * Build an attributes array by removing unwanted keys.
     */
    protected function buildAttributes(array $attributes, array $except = []): array
    {
        foreach ($except as $key) {
            unset($attributes[$key]);
        }
        return $attributes;
    }

    /**
     * Resolve the action URL from the provided options.
     *
     * @param array $options
     * @return string
     */
    protected function getAction(array $options): string
    {
        if (!empty($options['url'])) {
            return url($options['url']);
        }

        if (!empty($options['route'])) {
            if (is_array($options['route'])) {
                return route($options['route'][0], array_slice($options['route'], 1));
            }
            return route($options['route']);
        }

        if (isset($options['action'])) {
            if (is_array($options['action'])) {
                return action($options['action'][0], array_slice($options['action'], 1));
            }
            return action($options['action']);
        }

        return '';
    }
}
