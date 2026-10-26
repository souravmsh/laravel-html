<?php

// Autoloader setup
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists('/var/www/html/online_bo/www/vendor/autoload.php')) {
    require_once '/var/www/html/online_bo/www/vendor/autoload.php';
}

// Fallback mocks if Illuminate support is not loaded
if (!class_exists(\Illuminate\Support\HtmlString::class)) {
    eval('namespace Illuminate\Support; class HtmlString { public function __construct(protected string $html) {} public function toHtml(): string { return $this->html; } public function __toString(): string { return $this->html; } }');
}
if (!trait_exists(\Illuminate\Support\Traits\Macroable::class)) {
    eval('namespace Illuminate\Support\Traits; trait Macroable { protected static array $macros = []; public static function macro(string $name, callable $macro): void { static::$macros[$name] = $macro; } public function __call(string $method, array $parameters): mixed { if (isset(static::$macros[$method])) { return call_user_func_array(static::$macros[$method]->bindTo($this, static::class), $parameters); } throw new \BadMethodCallException("Method {$method} does not exist."); } }');
}

// Package autoloader
spl_autoload_register(function ($class) {
    $prefix = 'Souravmsh\\Html\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) === 0) {
        $file = $base_dir . str_replace('\\', '/', substr($class, $len)) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    $testPrefix = 'Tests\\';
    $testLen = strlen($testPrefix);
    if (strncmp($testPrefix, $class, $testLen) === 0) {
        $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, $testLen)) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Configure Container bindings if Laravel Container is present
if (class_exists(\Illuminate\Container\Container::class)) {
    $container = \Illuminate\Container\Container::getInstance();

    $urlGenerator = new class {
        public function asset($path, $secure = null) {
            if (preg_match('#^(https?:)?//#i', $path)) {
                return $path;
            }
            return ($secure ? 'https://example.com/asset/' : 'https://example.com/asset/') . ltrim($path, '/');
        }
        public function to($path, $extra = [], $secure = null) {
            if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, '#') || str_starts_with($path, 'mailto:')) {
                return $path;
            }
            return ($secure ? 'https://example.com/' : 'https://example.com/') . ltrim($path, '/');
        }
        public function route($name, $parameters = []) {
            return 'https://example.com/route/' . $name;
        }
        public function action($action, $parameters = []) {
            return 'https://example.com/action/' . $action;
        }
    };

    $container->instance('url', $urlGenerator);
    if (interface_exists(\Illuminate\Contracts\Routing\UrlGenerator::class)) {
        $container->instance(\Illuminate\Contracts\Routing\UrlGenerator::class, $urlGenerator);
    }

    $request = new class {
        public function old($key = null, $default = null) {
            return $default;
        }
    };
    $container->instance('request', $request);

    $session = new class {
        public function token() {
            return 'test-csrf-token';
        }
    };
    $container->instance('session', $session);

    $container->singleton('form', function () {
        return new \Souravmsh\Html\Services\FormBuilder();
    });

    $container->singleton('html', function () {
        return new \Souravmsh\Html\Services\HtmlBuilder();
    });
}

// Fallback helper mocks for tests if not provided by Laravel
if (!function_exists('asset')) {
    function asset($url, $secure = null) {
        return ($secure ? 'https://example.com/asset/' : 'https://example.com/asset/') . ltrim($url, '/');
    }
}
if (!function_exists('url')) {
    function url($url, $extra = [], $secure = null) {
        return ($secure ? 'https://example.com/' : 'https://example.com/') . ltrim($url, '/');
    }
}
if (!function_exists('route')) {
    function route($name, $params = []) {
        return 'https://example.com/route/' . $name;
    }
}
if (!function_exists('action')) {
    function action($action, $params = []) {
        return 'https://example.com/action/' . $action;
    }
}
if (!function_exists('e')) {
    function e($val, $double = true) {
        return htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8', $double);
    }
}
if (!function_exists('old')) {
    function old($name = null, $default = null) {
        return $default;
    }
}
if (!function_exists('csrf_field')) {
    function csrf_field() {
        return '<input type="hidden" name="_token" value="test-csrf-token">';
    }
}

// Include helpers
require_once __DIR__ . '/../src/helpers.php';

// Require test classes
require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/FormBuilderDateTest.php';
require_once __DIR__ . '/HtmlBuilderTest.php';

$testClasses = [
    \Tests\FormBuilderDateTest::class,
    \Tests\HtmlBuilderTest::class,
];

echo "\n\033[1;36mRunning Test Suite for laravel-html...\033[0m\n\n";

$totalTests = 0;
$totalAssertions = 0;
$failures = [];

foreach ($testClasses as $class) {
    $className = (new \ReflectionClass($class))->getShortName();
    echo "\033[1;33m{$className}\033[0m\n";

    $methods = get_class_methods($class);
    foreach ($methods as $method) {
        if (!str_starts_with($method, 'test')) {
            continue;
        }

        $totalTests++;
        $instance = new $class();
        if (method_exists($instance, 'setUp')) {
            $instance->setUp();
        }

        try {
            $instance->$method();
            $testFailures = $instance->getFailures();
            $totalAssertions += $instance->getAssertionCount();

            if (!empty($testFailures)) {
                echo "  \033[31m✗ {$method}\033[0m\n";
                foreach ($testFailures as $fail) {
                    $failures[] = "{$className}::{$method}(): {$fail}";
                }
            } else {
                echo "  \033[32m✓ {$method}\033[0m\n";
            }
        } catch (\Throwable $e) {
            echo "  \033[31m✗ {$method} (Exception)\033[0m\n";
            $failures[] = "{$className}::{$method}(): Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString();
        }
    }
    echo "\n";
}

if (!empty($failures)) {
    $failureCount = count($failures);
    echo "\033[1;31mFAILURES ({$failureCount}):\033[0m\n\n";
    foreach ($failures as $f) {
        echo "  - {$f}\n\n";
    }
    echo "\033[1;31mTests Failed! Tests: {$totalTests}, Assertions: {$totalAssertions}\033[0m\n\n";
    exit(1);
}

echo "\033[1;32mPASS! All {$totalTests} tests passed successfully ({$totalAssertions} assertions).\033[0m\n\n";
exit(0);
