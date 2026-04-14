<?php

namespace Souravmsh\Html\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\HtmlString open(array $options = [])
 * @method static \Illuminate\Support\HtmlString model(mixed $model, array $options = [])
 * @method static \Illuminate\Support\HtmlString close()
 * @method static \Illuminate\Support\HtmlString label(string $name, ?string $value = null, array $attributes = [])
 * @method static \Illuminate\Support\HtmlString text(string $name, mixed $value = null, array $attributes = [])
 * @method static \Illuminate\Support\HtmlString email(string $name, mixed $value = null, array $attributes = [])
 * @method static \Illuminate\Support\HtmlString password(string $name, array $attributes = [])
 * @method static \Illuminate\Support\HtmlString number(string $name, mixed $value = null, array $attributes = [])
 * @method static \Illuminate\Support\HtmlString date(string $name, mixed $value = null, array $attributes = [])
 * @method static \Illuminate\Support\HtmlString hidden(string $name, mixed $value = null, array $attributes = [])
 * @method static \Illuminate\Support\HtmlString file(string $name, array $attributes = [])
 * @method static \Illuminate\Support\HtmlString textarea(string $name, mixed $value = null, array $attributes = [])
 * @method static \Illuminate\Support\HtmlString select(string $name, mixed $list = [], mixed $selected = null, array $attributes = [])
 * @method static \Illuminate\Support\HtmlString checkbox(string $name, mixed $value = 1, ?bool $checked = null, array $attributes = [])
 * @method static \Illuminate\Support\HtmlString radio(string $name, mixed $value = null, ?bool $checked = null, array $attributes = [])
 * @method static \Illuminate\Support\HtmlString button(?string $value = null, array $attributes = [])
 * @method static \Illuminate\Support\HtmlString submit(?string $value = null, array $attributes = [])
 * @method static \Illuminate\Support\HtmlString input(string $type, string $name, mixed $value = null, array $attributes = [])
 *
 * @see \Souravmsh\Html\FormBuilder
 */
class Form extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'form';
    }
}
