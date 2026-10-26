<?php

namespace Tests;

use DateTime;
use DateTimeImmutable;
use Souravmsh\Html\Services\FormBuilder;
use Souravmsh\Html\Services\HtmlBuilder;

class TestCase
{
    protected int $assertions = 0;
    protected array $failures = [];

    public function assertEquals(mixed $expected, mixed $actual, string $message = ''): void
    {
        $this->assertions++;
        $expectedStr = is_string($expected) ? $expected : (is_object($expected) && method_exists($expected, '__toString') ? (string) $expected : var_export($expected, true));
        $actualStr = is_string($actual) ? $actual : (is_object($actual) && method_exists($actual, '__toString') ? (string) $actual : var_export($actual, true));

        if ($expectedStr !== $actualStr) {
            $this->failures[] = ($message ? $message . ' - ' : '') . "Expected:\n  {$expectedStr}\nGot:\n  {$actualStr}";
        }
    }

    public function assertTrue(bool $condition, string $message = ''): void
    {
        $this->assertions++;
        if (!$condition) {
            $this->failures[] = ($message ? $message . ' - ' : '') . "Expected true, got false";
        }
    }

    public function assertStringContains(string $needle, string $haystack, string $message = ''): void
    {
        $this->assertions++;
        if (!str_contains($haystack, $needle)) {
            $this->failures[] = ($message ? $message . ' - ' : '') . "Expected string to contain: {$needle}";
        }
    }

    public function getAssertionCount(): int
    {
        return $this->assertions;
    }

    public function getFailures(): array
    {
        return $this->failures;
    }
}

