<?php

namespace App\Core\Http;

use App\Util\Collection;

/**
 * Request
 */
final class Request
{
    /**
     * @var string URL being requested with args
     */
    public string $fullUri;

    /**
     * @var string URL being requested without args
     */
    public string $uri;

    /**
     * @var string Parent subdirectory of the URL
     */
    public string $base;

    /**
     * @var string Request method (GET, POST, PUT, DELETE)
     */
    public string $method;

    /**
     * @var string referer URL
     */
    public string $referer;

    /**
     * @var string IP address of the client
     */
    public string $ip;

    /**
     * @var bool Whether the request is an AJAX request
     */
    public bool $ajax;

    /**
     * @var string Server protocol (http, https)
     */
    public string $scheme;

    /**
     * @var string Browser information
     */
    public string $userAgent;

    /**
     * @var string Content type
     */
    public string $contentType;

    /**
     * @var int Content length
     */
    public int $contentLength;

    /**
     * @var Collection Header array
     */
    public Collection $header;

    /**
     * @var Collection Query string parameters
     */
    public Collection $query;

    /**
     * @var Collection Post parameters
     */
    public Collection $data;

    /**
     * @var Collection Cookie parameters
     */
    public Collection $cookies;

    /**
     * @var Collection Uploaded files
     */
    public Collection $files;

    /**
     * @var Collection Server parameters
     */
    public Collection $server;

    /**
     * @var bool Whether the connection is secure
     */
    public bool $secure;

    /**
     * @var string HTTP accept parameters
     */
    public string $accept;

    /**
     * @var string Proxy IP address of the client
     */
    public string $proxyIp;

    /**
     * @var string HTTP host name
     */
    public string $host;

    /**
     * Constructor
     *
     * @param array $config Request configuration
     */
    public function __construct(array $config = [])
    {
        if (empty($config)) {
            $this->query = new Collection($_GET);
            $this->data = new Collection($_POST);
            $this->cookies = new Collection($_COOKIE);
            $this->files = new Collection($_FILES);
            $this->server = new Collection($_SERVER);
            $this->fullUri = $this->server->get('REQUEST_URI', '/');

            $config = [
                'base' => str_replace(['\\', ' '], ['/', '%20'], dirname($this->server->get('SCRIPT_NAME'))),
                'method' => $this->getMethod(),
                'header' => $this->getHeader(),
                'referer' => $this->server->get('HTTP_REFERER', ''),
                'ip' => $this->server->get('REMOTE_ADDR'),
                'ajax' => 'XMLHttpRequest' === $this->server->get('HTTP_X_REQUESTED_WITH'),
                'scheme' => $this->getScheme(),
                'userAgent' => $this->server->get('HTTP_USER_AGENT'),
                'contentType' => $this->server->get('CONTENT_TYPE', ''),
                'contentLength' => intval($this->server->get('CONTENT_LENGTH', 0)),
                'secure' => 'https' === $this->getScheme(),
                'accept' => $this->server->get('HTTP_ACCEPT'),
                'proxyIp' => $this->getProxyIpAddress(),
                'host' => $this->server->get('HTTP_HOST'),
            ];
        }

        $this->init($config);
    }

    /**
     * Initialize request properties.
     *
     * @param array $properties Array of request properties
     */
    public function init(array $properties = [])
    {
        // Set all the defined properties
        foreach ($properties as $key => $value) {
            $this->$key = $value;
        }

        // Get the requested URL without the base directory
        if ($this->base !== '/' && $this->base !== '' && strpos($this->fullUri, $this->base) !== false) {
            $this->fullUri = substr($this->fullUri, strlen($this->base));
        }

        // Default url
        if (empty($this->fullUri)) {
            $this->fullUri = '/';
        } else {
            // Merge URL query parameters with $_GET
            $_GET = array_merge($_GET, $this->parseQuery($this->fullUri));

            $this->query->setData($_GET);
        }

        $this->uri = parse_url($this->fullUri, PHP_URL_PATH);

        // Check for JSON input
        if (strpos($this->contentType, 'application/json') !== false) {
            $body = $this->getBody();
            if ('' !== $body && null !== $body) {
                try {
                    $data = json_decode($body, true);
                    if (is_array($data)) {
                        $this->data->setData($data);
                    }
                } catch (\Exception $exception) {
                    throw new \Exception('Can not parse json data: ' . $exception->getMessage());
                }
            }
        }
    }

    public function get(string $key, $default = null)
    {
        $value = null;

        if ($this->query->has($key)) {
            $value = $this->query->get($key);
        }
        if ($this->data->has($key)) {
            $value = $this->data->get($key);
        }
        if ($this->cookies->has($key)) {
            $value = $this->cookies->get($key);
        }
        if ($this->files->has($key)) {
            $value = $this->files->get($key);
        }

        return $value ? $value : $default;
    }

    /**
     * Gets the body of the request.
     *
     * @return string Raw HTTP request body
     */
    public function getBody(): ?string
    {
        $method = $this->getMethod();

        if ('POST' === $method || 'PUT' === $method || 'DELETE' === $method || 'PATCH' === $method) {
            $body = file_get_contents('php://input');
        }

        return $body;
    }

    /**
     * Gets the request method.
     */
    public function getMethod(): string
    {
        $method = $this->server->get('REQUEST_METHOD', 'GET');

        if ($this->server->has('HTTP_X_HTTP_METHOD_OVERRIDE')) {
            $method = $this->server->get('HTTP_X_HTTP_METHOD_OVERRIDE');
        } elseif (isset($_REQUEST['_method'])) {
            $method = $_REQUEST['_method'];
        }

        return strtoupper($method);
    }

    /**
     * Get Header
     */
    public function getHeader(): Collection
    {
        $header = [];
        foreach ($this->server as $key => $value) {
            if (strpos($key, 'HTTP_') !== false) {
                $header[str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            }
        }
        return new Collection($header);
    }

    /**
     * Gets the real remote IP address.
     *
     * @return string IP address
     */
    public function getProxyIpAddress(): string
    {
        $forwarded = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
        ];

        $flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;

        foreach ($forwarded as $key) {
            if ($this->server->keyExists($key)) {
                sscanf($this->server[$key], '%[^,]', $ip);
                if (false !== filter_var($ip, FILTER_VALIDATE_IP, $flags)) {
                    return $ip;
                }
            }
        }

        return '';
    }

    /**
     * Parse query parameters from a URL.
     *
     * @param string $url URL string
     *
     * @return array Query parameters
     */
    public function parseQuery(string $url): array
    {
        $params = [];

        $args = parse_url($url);
        if (isset($args['query'])) {
            parse_str($args['query'], $params);
        }

        return $params;
    }

    public function getScheme(): string
    {
        if (
            strcasecmp($this->server->get('HTTPS'), 'on')
            || strcasecmp($this->server->get('HTTP_FRONT_END_HTTPS'), 'on')
            || strcasecmp($this->server->get('HTTP_X_FORWARDED_PROTO'), 'https')
            || strcasecmp($this->server->get('REQUEST_SCHEME'), 'https')
        ) {
            return 'https';
        }

        return 'http';
    }
}
