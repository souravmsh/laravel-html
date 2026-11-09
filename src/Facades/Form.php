<?php

namespace Souravmsh\Html\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\HtmlString open(array $options = [])
 * @method static \Illuminate\Support\HtmlString model(mixed $model, array $options = [])
 * @method static \Illuminate\Support\HtmlString close()
 * @method static \Illuminate\Support\HtmlString label(string $name, ?string $value = null, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString text(string $name, mixed $value = null, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString email(string $name, mixed $value = null, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString password(string $name, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString number(string $name, mixed $value = null, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString date(string $name, mixed $value = null, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString datetime(string $name, mixed $value = null, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString datetimeLocal(string $name, mixed $value = null, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString time(string $name, mixed $value = null, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString url(string $name, mixed $value = null, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString hidden(string $name, mixed $value = null, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString file(string $name, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString textarea(string $name, mixed $value = null, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString select(string $name, mixed $list = [], mixed $selected = null, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString checkbox(string $name, mixed $value = 1, ?bool $checked = null, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString radio(string $name, mixed $value = null, ?bool $checked = null, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString button(?string $value = null, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString submit(?string $value = null, mixed $attributes = [])
 * @method static \Illuminate\Support\HtmlString input(string $type, string $name, mixed $value = null, mixed $attributes = [])
 *
 * @see \Souravmsh\Html\Services\FormBuilder
 */
class Form extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'form';
    }
}
