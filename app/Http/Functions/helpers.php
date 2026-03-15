<?php

use Src\Facades\Request;
use Src\Facades\Session;
use Src\Facades\View;

if (!function_exists('request')) {
    function request(?string $key = null): mixed
    {
        if ($key === null) {
            return Request::all();
        }

        return Request::get($key);
    }
}

if (!function_exists('error')) {
    function error(string $key): ?string
    {
        return $_SESSION['errors'][$key] ?? null;
    }
}

if (!function_exists('clearErrors')) {
    function clearErrors(): void
    {
        unset($_SESSION['errors']);
    }
}

if (!function_exists('old')) {
    function old(string $key, string $default = ''): string
    {
        return $_SESSION['old'][$key] ?? $default;
    }
}

if (!function_exists('clearOld')) {
    function clearOld(): void
    {
        unset($_SESSION['old']);
    }
}

if (!function_exists('getModelName')) {
    function getModelName(string $model): string
    {
        $explodeModelName = explode('\\', $model);
        return strtolower(end($explodeModelName));
    }
}

if (!function_exists('view')) {
    function view(string $name, array $data = []): mixed
    {
        return View::send($name, $data);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url, int $statusCode = 302): never
    {
        header('Location: ' . $url, true, $statusCode);
        exit;
    }
}

if (!function_exists('arrayFlatten')) {
    function arrayFlatten(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, arrayFlatten($value));
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}

if (!function_exists('dd')) {
    function dd(mixed ...$data): never
    {
        foreach ($data as $item) {
            dump($item);
        }

        exit(1);
    }
}

if (!function_exists('_token')) {
    function _token(): string
    {
        return Session::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(_token()) . '">';
    }
}

if (!function_exists('class_basename')) {
    function class_basename(string|object $class): string
    {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }
}

if (!function_exists('snake_case')) {
    function snake_case(string $input): string
    {
        $pattern = '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!';
        preg_match_all($pattern, $input, $matches);
        $ret = $matches[0];

        foreach ($ret as &$match) {
            $match = $match == strtoupper($match)
                ? strtolower($match)
                : lcfirst($match);
        }

        return implode('_', $ret);
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
    }
}

if (!function_exists('dump')) {
    function dump(mixed $data): void
    {
        echo '<pre>';
        var_export($data);
        echo '</pre>';
    }
}
