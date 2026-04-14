# Laravel HTML Form

A lightweight HTML and Form Builders library for Laravel.

## Motivation & History

For years, the `laravelcollective/html` package was standard for managing forms and HTML in Laravel applications. However, as Laravel progressed into newer major releases, the `laravelcollective/html` package was deprecated and stopped receiving support.

This presented a significant upgrade hurdle: legacy codebases with hundreds of forms utilizing the `Form::` facade would require massive refactoring efforts just to upgrade the core Laravel framework. 

**Laravel HTML Form** was built to solve this exact problem. It serves as a custom, lightweight, drop-in replacement that precisely mimics the API and core features of the original `laravelcollective/html` package. By simply installing this library, you can safely upgrade your project to modern Laravel versions without needing to rewrite a single line of your existing Blade templates!

## Features

- Seamless drop-in replacement for legacy syntax
- Compatible with Laravel 10.x, 11.x, 12.x and 13.x
- Automatic CSRF Token injection
- Automatic method spoofing (`PUT`, `PATCH`, `DELETE`)
- Form Model Binding (including nested `dot.notation` elements)
- Supports repopulating with `old()` session inputs

## Installation

```bash
composer require souravmsh/laravel-html
```

## Quick Start

This package provides the `Form` facade which allows you to build HTML forms easily. 

## Opening a Form

```blade
{!! Form::open(['url' => 'foo/bar']) !!}
    //
{!! Form::close() !!}
```

**Using Named Routes:**

```blade
{!! Form::open(['route' => 'route.name']) !!}
```

**Using Controller Actions:**

```blade
{!! Form::open(['action' => 'Controller@method']) !!}
```

**Specifying the Method:**
By default, forms using the `open` method will use `POST`. You can customize this:

```blade
{!! Form::open(['url' => 'foo/bar', 'method' => 'put']) !!}
```

> **Note:** Since HTML forms only support `POST` and `GET`, `PUT`, `PATCH` and `DELETE` will be spoofed by automatically adding a `_method` hidden field to your form.

**File Uploads:**
If your form includes file uploads, add a `files` option:

```blade
{!! Form::open(['url' => 'foo/bar', 'files' => true]) !!}
```

## Model Binding

You can populate forms automatically using model binding. It automatically gets values from `old()` session inputs and falls back to attributes of your model instance. It also supports dot notation formatting for array inputs like `address.city`.

```blade
{!! Form::model($user, ['route' => ['user.update', $user->id], 'method' => 'put']) !!}
    {!! Form::text('name') !!}
{!! Form::close() !!}
```

## Labels & Inputs

### Labels
```blade
{!! Form::label('email', 'E-Mail Address', ['class' => 'control-label']) !!}
```

### Text, Email & Password
```blade
{!! Form::text('username', null, ['class' => 'form-control']) !!}
{!! Form::email('email', null, ['class' => 'form-control']) !!}
{!! Form::password('password', ['class' => 'form-control']) !!}
```

*Note: Password inputs do not prepopulate from old input or model binding.*

### Other Inputs
```blade
{!! Form::number('age', null, ['class' => 'form-control']) !!}
{!! Form::date('birthdate', null, ['class' => 'form-control']) !!}
{!! Form::url('website_url', null, ['class' => 'form-control']) !!}
{!! Form::hidden('invisible', 'secret') !!}
```

### Textarea
```blade
{!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3]) !!}
```

### File Uploads
```blade
{!! Form::file('avatar', ['class' => 'form-control-file']) !!}
```

### Checkboxes and Radio Buttons
```blade
{!! Form::checkbox('name', 'value', true) !!} <!-- true sets it as checked -->
{!! Form::radio('name', 'value', true) !!}
```

### Dropdown Lists (Select)

You may pass an array (or a collection) into the select method:

```blade
{!! Form::select('size', ['L' => 'Large', 'S' => 'Small'], 'S', ['placeholder' => 'Select a size']) !!}
```

**Grouped Lists:**
```blade
{!! Form::select('animal', [
    'Cats' => ['leopard' => 'Leopard'],
    'Dogs' => ['spaniel' => 'Spaniel'],
], null) !!}
```

### Buttons
```blade
{!! Form::submit('Submit Now', ['class' => 'btn btn-primary']) !!}
{!! Form::button('Click Me', ['class' => 'btn btn-secondary', 'type' => 'button']) !!}
```

## Extending / Macros

Since the underlying `FormBuilder` uses the `Macroable` trait, you can easily register your own custom methods:

```php
Form::macro('myField', function() {
    return '<input type="awesome">';
});
```

Then use it via:

```blade
{!! Form::myField() !!}
```

## License

This package is open-source software licensed under the MIT license.
