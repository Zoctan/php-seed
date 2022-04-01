<?php

namespace App\Core\Http;

use App\Core\Collection;

final class Request
{
    /**
     * @var string URL being requested
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
     * Constructor.
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

            $config = [
                'uri' => str_replace('@', '%40', $this->server->get('REQUEST_URI', '/')),
                'base' => str_replace(['\\', ' '], ['/', '%20'], dirname($this->server->get('SCRIPT_NAME'))),
                'method' => $this->getMethod(),
                'header' => $this->getHeader(),
                'referer' => $this->server->get('HTTP_REFERER', ''),
                'ip' => $this->server->get('REMOTE_ADDR'),
                'ajax' => 'XMLHttpRequest' === $this->server->get('HTTP_X_REQUESTED_WITH'),
                'scheme' => $this->getScheme(),
                'userAgent' => $this->server->get('HTTP_USER_AGENT'),
                'contentType' => $this->server->get('CONTENT_TYPE', ''),
                'contentLength' => (int) $this->server->get('CONTENT_LENGTH', 0),
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
        if ('/' !== $this->base && '' !== $this->base && 0 === strpos($this->uri, $this->base)) {
            $this->uri = substr($this->uri, strlen($this->base));
        }

        // Default url
        if (empty($this->uri)) {
            $this->uri = '/';
        } else {
            // Merge URL query parameters with $_GET
            $_GET = array_merge($_GET, $this->parseQuery($this->uri));

            $this->query->setData($_GET);
        }

        // Check for JSON input
        if (0 === strpos($this->contentType, 'application/json')) {
            $body = $this->getBody();
            if ('' !== $body && null !== $body) {
                try {
                    $data = json_decode($body, true);
                    if (is_array($data)) {
                        $this->data->setData($data);
                    }
                } catch (\Exception $exception) {
                    throw new \Exception('can not parse json data: ' . $exception->getMessage());
                }
            }
        }
    }

    public function get(string $key, mixed $default = null): ?string
    {
        if ($this->query->has($key)) {
            return $this->query->get($key);
        }
        if ($this->data->has($key)) {
            return $this->data->get($key);
        }
        if ($this->cookies->has($key)) {
            return $this->cookies->get($key);
        }
        if ($this->files->has($key)) {
            return $this->files->get($key);
        }

        return $default;
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
            if (strpos($key, 'HTTP_') === 0) {
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
            ($this->server->has('HTTPS') && 'on' === strtolower($this->server['HTTPS']))
            ||
            ($this->server->has('HTTP_X_FORWARDED_PROTO') && 'https' === $this->server['HTTP_X_FORWARDED_PROTO'])
            ||
            ($this->server->has('HTTP_FRONT_END_HTTPS') && 'on' === $this->server['HTTP_FRONT_END_HTTPS'])
            ||
            ($this->server->has('REQUEST_SCHEME') && 'https' === $this->server['REQUEST_SCHEME'])
        ) {
            return 'https';
        }

        return 'http';
    }
}
