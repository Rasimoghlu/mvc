<?php

use Src\Facades\Request;
use Src\Facades\Session;
use Src\Facades\View;

if (!function_exists('request')) {
    /**
     * Get value from request
     *
     * @param string|null $key
     * @return mixed
     */
    function request($key = null)
    {
        if ($key === null) {
            return Request::all();
        }
        
        return Request::get($key);
    }
}

if (!function_exists('error')) {
    /**
     * Get error message by key
     *
     * @param string $key
     * @return mixed|null
     */
    function error($key)
    {
        if (isset($_SESSION['errors'][$key])) {
            $error = $_SESSION['errors'][$key];
            unset($_SESSION['errors'][$key]);
            return $error;
        }

        return null;
    }
}

if (!function_exists('old')) {
    /**
     * Get old form value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function old($key, $default = '')
    {
        if (isset($_SESSION['old'][$key])) {
            $old = $_SESSION['old'][$key];
            unset($_SESSION['old'][$key]);
            return $old;
        }
        
        return $default;
    }
}

if (!function_exists('getModelName')) {
    /**
     * Get model name from namespace
     *
     * @param string $model
     * @return string
     */
    function getModelName($model): string
    {
        $explodeModelName = explode('\\', $model);
        return strtolower(end($explodeModelName));
    }
}

if (!function_exists('view')) {
    /**
     * Render a view
     *
     * @param string $name
     * @param array $data
     * @return mixed
     */
    function view(string $name, array $data = [])
    {
        return View::send($name, $data);
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to another URL
     *
     * @param string $url
     * @param int $statusCode
     * @return void
     */
    function redirect(string $url, int $statusCode = 302)
    {
        header('Location: ' . $url, true, $statusCode);
        exit;
    }
}

if (!function_exists('arrayFlatten')) {
    /**
     * Flatten an array
     *
     * @param array $array
     * @return bool|array
     */
    function arrayFlatten($array): bool|array
    {
        if (!is_array($array)) {
            return false;
        }

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
    /**
     * Dump and die
     *
     * @param mixed $data
     * @return void
     */
    function dd(...$data)
    {
        foreach ($data as $item) {
            dump($item);
        }
        
        die(1);
    }
}

if (!function_exists('_token')) {
    /**
     * Generate CSRF token
     *
     * @return string
     */
    function _token()
    {
        return Session::token();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate CSRF field
     *
     * @return string
     */
    function csrf_field()
    {
        return '<input type="hidden" name="_token" value="' . _token() . '">';
    }
}

if (!function_exists('class_basename')) {
    /**
     * Get the class "basename" of the given object / class
     *
     * @param string|object $class
     * @return string
     */
    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }
}

if (!function_exists('snake_case')) {
    /**
     * Convert a string to snake case
     *
     * @param string $input
     * @return string
     */
    function snake_case($input) {
        $pattern = '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!';
        preg_match_all($pattern, $input, $matches);
        $ret = $matches[0];

        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ?
                strtolower($match) :
                lcfirst($match);
        }

        return implode('_', $ret);
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML special characters
     *
     * @param string $value
     * @return string
     */
    function e($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
    }
}

if (!function_exists('clean')) {
    /**
     * Clean HTML input from dangerous elements
     *
     * @param string $html
     * @return string
     */
    function clean($html)
    {
        if (!$html) {
            return '';
        }
        
        // Remove all script tags and event handlers
        $remove = ["script", "onafterprint", "onbeforeprint", "onbeforeunload", "onerror", "onhashchange", "onload", 
                   "onoffline", "ononline", "onpageshow", "onresize", "onunload", "onblur", "onchange", "oncontextmenu", 
                   "onfocus", "oninput", "oninvalid", "onreset", "onsearch", "onselect", "onsubmit", "onkeydown", 
                   "onkeypress", "onkeyup", "onclick", "ondblclick", "onmousedown", "onmouseenter", "onmouseleave", 
                   "onmousemove", "onmouseout", "onmouseover", "onmouseup", "onwheel", "ondrag", "ondragend", 
                   "ondragenter", "ondragleave", "ondragover", "ondragstart", "ondrop", "onscroll", "oncopy", "oncut", 
                   "onpaste", "ontoggle", "onabort", "oncanplay", "oncanplaythrough", "oncuechange", "ondurationchange", 
                   "onemptied", "onended", "onerror", "onloadeddata", "onloadedmetadata", "onloadstart", "onpause", 
                   "onplay", "onplaying", "onprogress", "onratechange", "onseeked", "onseeking", "onstalled", "onsuspend", 
                   "ontimeupdate", "onvolumechange", "onwaiting"];
        
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/im', '', $html);
        $html = preg_replace('/\\r\\n|\\r|\\n/miu', '', $html);
        $html = preg_replace('/(' . (implode('|', $remove)) . ')="[^"]+"/im', '', $html);
        
        return $html;
    }
}

if (!function_exists('sanitize_input')) {
    /**
     * Sanitize user input for database operations
     *
     * @param string $input
     * @return string
     */
    function sanitize_input($input) 
    {
        if (empty($input)) {
            return '';
        }
        
        $input = trim($input);
        $input = strip_tags($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        // Remove common SQL injection patterns
        $patterns = [
            '/\bSELECT\b/i',
            '/\bINSERT\b/i',
            '/\bUPDATE\b/i',
            '/\bDELETE\b/i',
            '/\bDROP\b/i',
            '/\bUNION\b/i',
            '/\bEXEC\b/i',
            '/--/',
            '/;/',
            '/\/\*.*\*\//'
        ];
        
        return preg_replace($patterns, '', $input);
    }
}

if (!function_exists('cleanSql')) {
    /**
     * Sanitize and validate input data for SQL queries
     * 
     * @param array $data Data to be sanitized
     * @return array Sanitized data
     */
    function cleanSql(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            // Convert special characters to HTML entities
            if (is_string($value)) {
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
            
            // Remove any potentially harmful SQL characters
            if (is_string($value)) {
                $value = str_replace(['\\', "\0", "'", '"', "\x1a"], ['\\\\', '\\0', "\\'", '\\"', '\\Z'], $value);
            }
            
            $sanitized[$key] = $value;
        }
        
        return $sanitized;
    }
}

if (!function_exists('dump')) {
    /**
     * Dump data in a readable format
     *
     * @param mixed $data
     * @return void
     */
    function dump($data) {
        echo '<pre>';
        var_export($data);
        echo '</pre>';
    }
}