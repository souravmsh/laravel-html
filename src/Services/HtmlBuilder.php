<?php

namespace Souravmsh\Html\Services;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Traits\Macroable;

/**
 * Drop-in replacement for laravelcollective/html Html facade.
 * Supports modern Laravel (10.x, 11.x, 12.x, 13.x, 14.x+) without any blade template changes.
 */
class HtmlBuilder
{
    use Macroable;

    /**
     * Convert an HTML string to entities.
     *
     * @param string $value
     * @return string
     */
    public function entities(string $value): string
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * Convert entities to HTML characters.
     *
     * @param string $value
     * @return string
     */
    public function decode(string $value): string
    {
        return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate a link to a JavaScript file.
     *
     * @param string $url
     * @param array $attributes
     * @param bool|null $secure
     * @return HtmlString
     */
    public function script(string $url, array $attributes = [], ?bool $secure = null): HtmlString
    {
        $attributes = array_merge(['src' => asset($url, $secure)], $attributes);

        return new HtmlString('<script' . $this->attributes($attributes) . '></script>');
    }

    /**
     * Generate a link to a CSS file.
     *
     * @param string $url
     * @param array $attributes
     * @param bool|null $secure
     * @return HtmlString
     */
    public function style(string $url, array $attributes = [], ?bool $secure = null): HtmlString
    {
        $defaults = ['media' => 'all', 'type' => 'text/css', 'rel' => 'stylesheet'];
        $attributes = array_merge($defaults, $attributes, ['href' => asset($url, $secure)]);

        return new HtmlString('<link' . $this->attributes($attributes) . '>');
    }

    /**
     * Generate an HTML image element.
     *
     * @param string $url
     * @param string|null $alt
     * @param array $attributes
     * @param bool|null $secure
     * @return HtmlString
     */
    public function image(string $url, ?string $alt = null, array $attributes = [], ?bool $secure = null): HtmlString
    {
        $defaults = ['src' => asset($url, $secure)];
        if ($alt !== null) {
            $defaults['alt'] = $alt;
        }

        $attributes = array_merge($defaults, $attributes);

        return new HtmlString('<img' . $this->attributes($attributes) . '>');
    }

    /**
     * Generate a link to a Favicon file.
     *
     * @param string $url
     * @param array $attributes
     * @param bool|null $secure
     * @return HtmlString
     */
    public function favicon(string $url, array $attributes = [], ?bool $secure = null): HtmlString
    {
        $defaults = ['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => asset($url, $secure)];
        $attributes = array_merge($defaults, $attributes);

        return new HtmlString('<link' . $this->attributes($attributes) . '>');
    }

    /**
     * Generate an HTML link.
     *
     * @param string $url
     * @param string|null $title
     * @param array $attributes
     * @param bool|null $secure
     * @param bool $escape
     * @return HtmlString
     */
    public function link(string $url, ?string $title = null, array $attributes = [], ?bool $secure = null, bool $escape = true): HtmlString
    {
        $url = url($url, [], $secure);

        if ($title === null || $title === false) {
            $title = $url;
        }

        if ($escape) {
            $title = $this->entities((string) $title);
        }

        return new HtmlString('<a href="' . $this->entities($url) . '"' . $this->attributes($attributes) . '>' . $title . '</a>');
    }

    /**
     * Generate a HTTPS HTML link.
     *
     * @param string $url
     * @param string|null $title
     * @param array $attributes
     * @param bool $escape
     * @return HtmlString
     */
    public function secureLink(string $url, ?string $title = null, array $attributes = [], bool $escape = true): HtmlString
    {
        return $this->link($url, $title, $attributes, true, $escape);
    }

    /**
     * Generate an HTML link to an asset.
     *
     * @param string $url
     * @param string|null $title
     * @param array $attributes
     * @param bool|null $secure
     * @param bool $escape
     * @return HtmlString
     */
    public function linkAsset(string $url, ?string $title = null, array $attributes = [], ?bool $secure = null, bool $escape = true): HtmlString
    {
        $url = asset($url, $secure);

        return $this->link($url, $title ?: $url, $attributes, $secure, $escape);
    }

    /**
     * Generate a HTTPS HTML link to an asset.
     *
     * @param string $url
     * @param string|null $title
     * @param array $attributes
     * @param bool $escape
     * @return HtmlString
     */
    public function linkSecureAsset(string $url, ?string $title = null, array $attributes = [], bool $escape = true): HtmlString
    {
        return $this->linkAsset($url, $title, $attributes, true, $escape);
    }

    /**
     * Generate an HTML link to a named route.
     *
     * @param string $name
     * @param string|null $title
     * @param mixed $parameters
     * @param array $attributes
     * @param bool|null $secure
     * @param bool $escape
     * @return HtmlString
     */
    public function linkRoute(string $name, ?string $title = null, mixed $parameters = [], array $attributes = [], ?bool $secure = null, bool $escape = true): HtmlString
    {
        return $this->link(route($name, $parameters), $title, $attributes, $secure, $escape);
    }

    /**
     * Generate an HTML link to a controller action.
     *
     * @param string $action
     * @param string|null $title
     * @param mixed $parameters
     * @param array $attributes
     * @param bool|null $secure
     * @param bool $escape
     * @return HtmlString
     */
    public function linkAction(string $action, ?string $title = null, mixed $parameters = [], array $attributes = [], ?bool $secure = null, bool $escape = true): HtmlString
    {
        return $this->link(action($action, $parameters), $title, $attributes, $secure, $escape);
    }

    /**
     * Generate an HTML link to an email address.
     *
     * @param string $email
     * @param string|null $title
     * @param array $attributes
     * @param bool $escape
     * @return HtmlString
     */
    public function mailto(string $email, ?string $title = null, array $attributes = [], bool $escape = true): HtmlString
    {
        $email = $this->email($email);
        $title = $title ?: $email;

        if ($escape) {
            $title = $this->entities((string) $title);
        }

        $email = $this->obfuscate('mailto:') . $email;

        return new HtmlString('<a href="' . $email . '"' . $this->attributes($attributes) . '>' . $title . '</a>');
    }

    /**
     * Obfuscate an e-mail address to prevent spam-bots from sniffing it.
     *
     * @param string $email
     * @return string
     */
    public function email(string $email): string
    {
        return str_replace('@', '&#64;', $this->obfuscate($email));
    }

    /**
     * Obfuscate a string to prevent spam-bots from sniffing it.
     *
     * @param string $value
     * @return string
     */
    public function obfuscate(string $value): string
    {
        $safe = '';

        foreach (str_split($value) as $letter) {
            if (ord($letter) > 128) {
                $safe .= $letter;
                continue;
            }

            // Convert character randomly to decimal, hex entity, or keep character
            switch (random_int(1, 3)) {
                case 1:
                    $safe .= '&#' . ord($letter) . ';';
                    break;
                case 2:
                    $safe .= '&#x' . dechex(ord($letter)) . ';';
                    break;
                case 3:
                    $safe .= $letter;
                    break;
            }
        }

        return $safe;
    }

    /**
     * Generates non-breaking space entities based on number supplied.
     *
     * @param int $num
     * @return string
     */
    public function nbsp(int $num = 1): string
    {
        return str_repeat('&nbsp;', $num);
    }

    /**
     * Generate an ordered list of items.
     *
     * @param array $list
     * @param array $attributes
     * @return HtmlString
     */
    public function ol(array $list, array $attributes = []): HtmlString
    {
        return $this->listing('ol', $list, $attributes);
    }

    /**
     * Generate an unordered list of items.
     *
     * @param array $list
     * @param array $attributes
     * @return HtmlString
     */
    public function ul(array $list, array $attributes = []): HtmlString
    {
        return $this->listing('ul', $list, $attributes);
    }

    /**
     * Generate a description list of items.
     *
     * @param array $list
     * @param array $attributes
     * @return HtmlString
     */
    public function dl(array $list, array $attributes = []): HtmlString
    {
        $attrs = $this->attributes($attributes);
        $html = "<dl{$attrs}>";

        foreach ($list as $key => $value) {
            $html .= '<dt>' . e($key, false) . '</dt>';
            $values = (array) $value;

            foreach ($values as $item) {
                $html .= '<dd>' . e($item, false) . '</dd>';
            }
        }

        $html .= '</dl>';

        return new HtmlString($html);
    }

    /**
     * Create a listing HTML element.
     *
     * @param string $type
     * @param array $list
     * @param array $attributes
     * @return HtmlString
     */
    protected function listing(string $type, array $list, array $attributes = []): HtmlString
    {
        if (empty($list)) {
            return new HtmlString('');
        }

        $html = '';

        foreach ($list as $key => $value) {
            $html .= $this->listingElement($key, $type, $value);
        }

        return new HtmlString("<{$type}" . $this->attributes($attributes) . ">{$html}</{$type}>");
    }

    /**
     * Create the HTML for a listing element.
     *
     * @param mixed $key
     * @param string $type
     * @param mixed $value
     * @return string
     */
    protected function listingElement(mixed $key, string $type, mixed $value): string
    {
        if (is_array($value)) {
            return $this->nestedListing($key, $type, $value);
        }

        return '<li>' . e($value, false) . '</li>';
    }

    /**
     * Create the HTML for a nested listing attribute.
     *
     * @param mixed $key
     * @param string $type
     * @param array $value
     * @return string
     */
    protected function nestedListing(mixed $key, string $type, array $value): string
    {
        if (is_int($key)) {
            return $this->listing($type, $value)->toHtml();
        }

        return '<li>' . e($key, false) . $this->listing($type, $value)->toHtml() . '</li>';
    }

    /**
     * Generate a meta tag.
     *
     * @param string $name
     * @param string $content
     * @param array $attributes
     * @return HtmlString
     */
    public function meta(string $name, string $content, array $attributes = []): HtmlString
    {
        $attributes = array_merge(['name' => $name, 'content' => $content], $attributes);

        return new HtmlString('<meta' . $this->attributes($attributes) . '>');
    }

    /**
     * Generate an HTML tag.
     *
     * @param string $tag
     * @param mixed $content
     * @param array $attributes
     * @return HtmlString
     */
    public function tag(string $tag, mixed $content = '', array $attributes = []): HtmlString
    {
        $content = is_array($content) ? implode('', $content) : (string) $content;

        return new HtmlString('<' . $tag . $this->attributes($attributes) . '>' . $content . '</' . $tag . '>');
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param array $attributes
     * @return string
     */
    public function attributes(array $attributes): string
    {
        $html = [];

        foreach ($attributes as $key => $value) {
            // Numeric key represents a boolean attribute like "required" or "defer"
            if (is_numeric($key)) {
                $html[] = $value;
                continue;
            }

            if ($value === null || $value === false) {
                continue;
            }

            if ($value === true) {
                $html[] = $key;
                continue;
            }

            if (is_array($value) && $key === 'class') {
                $html[] = 'class="' . e(implode(' ', array_filter($value))) . '"';
                continue;
            }

            $html[] = $key . '="' . e($value, false) . '"';
        }

        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }
}
