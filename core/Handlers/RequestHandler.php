<?php

namespace Core\Handlers;

class RequestHandler
{
    /**
     * @var string
     */
    private string $scriptName;

    /**
     * @var string
     */
    private string $baseUrl;

    /**
     * @var string
     */
    private string $url;

    /**
     * @var string
     */
    private string $fullUrl;

    /**
     * @var string
     */
    private string $queryString;

    /**
     *
     */
    public function __construct(){}

    /**
     * @return void
     */
    public function handle(): void
    {
        $this->scriptName = str_replace('\\', '', dirname($_SERVER['SCRIPT_NAME']));
        $this->setBaseUrl();
        $this->setUrl();
    }

    /**
     * @return string
     */
    public function setBaseUrl()
    {
        $protocol = $_SERVER['REQUEST_SCHEME'] . '://';
        $host = $_SERVER['HTTP_HOST'];
        $scriptName = $this->scriptName;

       return $this->baseUrl = $protocol . $host . $scriptName;
    }

    /**
     * @return void
     */
    public function setUrl()
    {
        $requestUri = urldecode($_SERVER['REQUEST_URI']);
        $requestUri = rtrim(preg_replace("#^" . $this->scriptName . '#', '', $requestUri), '/');

        $queryString = '';

        $this->fullUrl = $requestUri;

        if (str_contains($requestUri, '?')) {
            list($requestUri, $queryString) = explode('?', $requestUri);
        }

        $this->url = $requestUri;
        $this->queryString = $queryString;

    }

    /**
     * @return string
     */
    public function baseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function url()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function queryString()
    {
        return $this->queryString;
    }

    /**
     * @return string
     */
    public function fullUrl()
    {
        return $this->fullUrl;
    }

    /**
     * @return mixed
     */
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @param $type
     * @param string $key
     * @return bool
     */
    public function has($type, string $key)
    {
        return array_key_exists($key, $type);
    }

    /**
     * @param string $key
     * @param $type
     * @return mixed|null
     */
    public function value(string $key, $type = null)
    {
        $type = $type ?? $_REQUEST;

        return static::has($type, $key) ? $type[$key] : null;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        return $this->value(strip_tags($key), $_GET);
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function post(string $key)
    {
        return $this->value(strip_tags($key), $_POST);
    }

    /**
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function set(string $key, $value)
    {
        $_REQUEST[$key] = $value;
        $_GET[$key] = $value;
        $_POST[$key] = $value;

        return $value;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $_REQUEST;
    }
    
}