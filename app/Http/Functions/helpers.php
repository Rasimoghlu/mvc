<?php

use Core\Facades\View;

if (!function_exists('getModelName')) {
    /**
     * @param $model
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
     * @param string $name
     * @param array $data
     * @return mixed
     */
    function view(string $name, array $data = [])
    {
        return View::send($name, $data);
    }
}

if (!function_exists('arrayFlatten')) {
    /**
     * @param $array
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

if (!function_exists('implodeArrayWithComma')) {
    /**
     * @param array $values
     * @return string
     */
    function implodeArrayWithComma(array $values): string
    {
        return implode(',', $values);
    }
}

if (!function_exists('dd')) {
    /**
     * @param $data
     * @return void
     */
    function dd($data)
    {
        echo '<pre>';
        print_r($data);
        die();
    }
}

if (!function_exists('clean')) {
    /**
     * @param $html
     * @return array|mixed|string|string[]|null
     */
    function clean($html)
    {
        if ($html) {
            $remove = ["script", "onafterprint", "onbeforeprint", "onbeforeunload", "onerror", "onhashchange", "onload", "onoffline", "ononline", "onpageshow", "onresize", "onunload", "onblur", "onchange", "oncontextmenu", "onfocus", "oninput", "oninvalid", "onreset", "onsearch", "onselect", "onsubmit", "onkeydown", "onkeypress", "onkeyup", "onclick", "ondblclick", "onmousedown", "onmouseenter", "onmouseleave", "onmousemove", "onmouseout", "onmouseover", "onmouseup", "onwheel", "ondrag", "ondragend", "ondragenter", "ondragleave", "ondragover", "ondragstart", "ondrop", "onscroll", "oncopy", "oncut", "onpaste", "ontoggle", "onabort", "oncanplay", "oncanplaythrough", "oncuechange", "ondurationchange", "onemptied", "onended", "onerror", "onloadeddata", "onloadedmetadata", "onloadstart", "onpause", "onplay", "onplaying", "onprogress", "onratechange", "onseeked", "onseeking", "onstalled", "onsuspend", "ontimeupdate", "onvolumechange", "onwaiting"];
            $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/im', '', $html);
            $html = preg_replace('/\\r\\n|\\r|\\n/miu', '', $html);
            $html = preg_replace('/(' . (implode('|', $remove)) . ')="[^"]+"/im', '', $html);
        }
        return $html;
    }

    if (!function_exists('cleanSql')) {
        function cleanSql($data): array
        {
            $cleanArray = [];

            if (is_array($data)) {
                foreach ($data as $value) {
                    if (isset($_POST[$value]) or isset($_GET[$value]) or count($data))
                    {
                        $clean = !empty($_POST[$value]) ? trim($_POST[$value]) : (!empty($_GET[$value]) ? trim($_GET[$value]) : $value);
                        $clean = strip_tags($clean);
                        $clean = htmlspecialchars($clean, ENT_QUOTES);
                        $clean = str_replace('insert', '', $clean);
                        $clean = str_replace('INSERT', '', $clean);
                        $clean = str_replace('select', '', $clean);
                        $clean = str_replace('SELECT', '', $clean);
                        $clean = str_replace('exec', '', $clean);
                        $clean = str_replace('EXEC', '', $clean);
                        $clean = str_replace('union', '', $clean);
                        $clean = str_replace('UNION', '', $clean);
                        $clean = str_replace('drop', '', $clean);
                        $clean = str_replace('DROP', '', $clean);
                        $clean = str_replace('update', '', $clean);
                        $clean = str_replace('UPDATE', '', $clean);
                        $clean = str_replace('delete', '', $clean);
                        $clean = str_replace('DELETE', '', $clean);

                        $cleanArray[] = $clean;
                    }
                }
            }

        return $cleanArray;
        }
    }

}