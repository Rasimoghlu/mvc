<?php

namespace Src\Handlers;

use App\Interfaces\RequestInterface;

class RequestHandler implements RequestInterface
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
    public function __construct()
    {
        $this->checkCsrfToken();
    }

    private function checkCsrfToken()
    {
        if (strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
            if (!isset($_POST['_token'])) {
                echo '<div style="background-color: #ffecec; color: red; padding: 10px; margin: 10px 0; border-left: 4px solid red;">
                    WARNING: CSRF Token missing! This is a security risk.
                </div>';
            } 
            elseif (isset($_SESSION['_token']) && $_SESSION['_token'] !== $_POST['_token']) {
                echo '<div style="background-color: #ffecec; color: red; padding: 10px; margin: 10px 0; border-left: 4px solid red;">
                    WARNING: Invalid CSRF Token! Expected: '.$_SESSION['_token'].', Received: '.$_POST['_token'].'
                </div>';
            }
            
            if (isset($_SESSION['_token'])) {
                echo '<div style="background-color: #e7f7e9; color: green; padding: 10px; margin: 10px 0; border-left: 4px solid green;">
                    Current Session Token: '.$_SESSION['_token'].'<br>
                    Submitted Token: '.(isset($_POST['_token']) ? $_POST['_token'] : 'None').'
                </div>';
            }
        }
    }

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
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

        $protocol = $isSecure ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptName = $this->scriptName ?? '';

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
     * @param array $type
     * @param string $key
     * @return bool
     */
    public function has(array $type, string $key)
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

        return $this->has($type, $key) ? $type[$key] : null;
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
        if (isset($_REQUEST['url']) || isset($_REQUEST['_token'])) {
            unset($_REQUEST['url'], $_REQUEST['_token']);
        }

        return $_REQUEST;
    }

}