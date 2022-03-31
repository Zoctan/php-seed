<?php

namespace App\Core\Http;

class Response
{
    public bool $contentLength = true;

    public static array $codes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',

        226 => 'IM Used',

        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',

        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',

        426 => 'Upgrade Required',

        428 => 'Precondition Required',
        429 => 'Too Many Requests',

        431 => 'Request Header Fields Too Large',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',

        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];
    
    protected int $status = 200;

    protected array $headers = [];

    protected string $content = '';

    protected bool $sent = false;

    protected $config;
    protected $debug = [];
    protected $data = [];

    public function setDebug($key, $value)
    {
        $this->debug[$key] = $value;
        return $this;
    }

    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function status(?int $code = null)
    {
        if (null === $code) {
            return $this->status;
        }

        if (\array_key_exists($code, self::$codes)) {
            $this->status = $code;
        } else {
            throw new \Exception('Invalid status code.');
        }

        return $this;
    }

    public function header($name, ?string $value = null)
    {
        if (\is_array($name)) {
            foreach ($name as $k => $v) {
                $this->headers[$k] = $v;
            }
        } else {
            $this->headers[$name] = $value;
        }

        return $this;
    }

    public function headers()
    {
        return $this->headers;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content ?? '';

        return $this;
    }

    public function clear(): self
    {
        $this->status = 200;
        $this->headers = [];
        $this->content = '';

        return $this;
    }

    public function cache($expires): self
    {
        if (false === $expires) {
            $this->headers['Expires'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
            $this->headers['Cache-Control'] = [
                'no-store, no-cache, must-revalidate',
                'post-check=0, pre-check=0',
                'max-age=0',
            ];
            $this->headers['Pragma'] = 'no-cache';
        } else {
            $expires = \is_int($expires) ? $expires : strtotime($expires);
            $this->headers['Expires'] = gmdate('D, d M Y H:i:s', $expires) . ' GMT';
            $this->headers['Cache-Control'] = 'max-age=' . ($expires - time());
            if (isset($this->headers['Pragma']) && 'no-cache' == $this->headers['Pragma']) {
                unset($this->headers['Pragma']);
            }
        }

        return $this;
    }

    public function sendHeaders(): self
    {
        // Send status code header
        if (false !== strpos(\PHP_SAPI, 'cgi')) {
            header(
                sprintf(
                    'Status: %d %s',
                    $this->status,
                    self::$codes[$this->status]
                ),
                true
            );
        } else {
            header(
                sprintf(
                    '%s %d %s',
                    $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1',
                    $this->status,
                    self::$codes[$this->status]
                ),
                true,
                $this->status
            );
        }

        // Send other headers
        foreach ($this->headers as $field => $value) {
            if (\is_array($value)) {
                foreach ($value as $v) {
                    header($field . ': ' . $v, false);
                }
            } else {
                header($field . ': ' . $value);
            }
        }

        if ($this->contentLength) {
            // Send content length
            $length = $this->getContentLength();

            if ($length > 0) {
                header('Content-Length: ' . $length);
            }
        }

        return $this;
    }

    public function getContentLength(): int
    {
        return \extension_loaded('mbstring') ?
            mb_strlen($this->content, 'UTF8') :
            \strlen($this->content);
    }

    public function sent(): bool
    {
        return $this->sent;
    }

    public function send(): void
    {
        if (ob_get_length() > 0) {
            ob_end_clean();
        }

        if (!headers_sent()) {
            $this->sendHeaders();
        }

        $this->config = \App\DI()->config;

        // 发送前再装载 debug 信息
        if ($this->config['app']['debug'] && !empty($this->debug)) {
            $this->data[$this->config['app']['response']['structureMap']['debug']] = $this->debug;
        }

        switch ($this->config['app']['response']['type']) {
            default:
            case 'json':
                // JSON_UNESCAPED_UNICODE 中文也能显示
                $this->setContent(json_encode($this->data, JSON_UNESCAPED_UNICODE));
                break;
        }

        echo $this->content;

        $this->sent = true;
    }
}
