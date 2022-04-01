<?php

namespace App\Core\Http;

use App\Util\Array2Xml;

class Response
{
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

    protected int $code = 200;

    protected array $header = [];

    protected string $content = '';

    protected bool $sent = false;

    protected bool $isDebug;

    protected string $debugKey;

    protected array $debugData = [];

    protected string $responseType;

    protected array $mimeTypes = [
        'txt' => 'text/plain; charset=utf-8',
        'xml' => 'application/xml; charset=utf-8',
        'html' => 'application/html; charset=utf-8',
        'json' => 'application/json; charset=utf-8',
        'stream' => 'application/octet-stream',
    ];

    protected array $data = [];

    public function isDebug(bool $isDebug = false)
    {
        $this->isDebug = $isDebug;
        return $this;
    }

    public function setDebugKey(string $debugKey = 'debug')
    {
        $this->debugKey = $debugKey;
        return $this;
    }

    public function setResponseType(string $responseType = 'json')
    {
        $this->responseType = $responseType;
        return $this;
    }

    public function appendDebug($key, $value)
    {
        $this->debugData[$key] = $value;
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

    public function setCode(?int $code = null)
    {
        if (null === $code) {
            return $this->code;
        }

        if (array_key_exists($code, self::$codes)) {
            $this->code = $code;
        } else {
            throw new \Exception('Invalid status code.');
        }

        return $this;
    }

    public function appendHeader($data, ?string $value = null)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $this->header[$k] = $v;
            }
        } else {
            $this->header[$data] = $value;
        }

        return $this;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function setContentType(?string $type): self
    {
        $this->appendHeader('Content-type', $type);

        return $this;
    }

    public function appendContent(?string $content): self
    {
        $this->content .= $content ?? '';

        return $this;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content ?? '';

        return $this;
    }

    public function clear(): self
    {
        $this->code = 200;
        $this->header = [];
        $this->content = '';

        return $this;
    }

    public function allowCache($allowCache = true): self
    {
        if (!$allowCache) {
            $this->header['Expires'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
            $this->header['Cache-Control'] = [
                'no-store, no-cache, must-revalidate',
                'post-check=0, pre-check=0',
                'max-age=0',
            ];
            $this->header['Pragma'] = 'no-cache';
        }
        return $this;
    }

    public function cache($expires): self
    {
        if ($expires <= 0) {
            return $this->allowCache(false);
        } else {
            $expires = is_int($expires) ? $expires : strtotime($expires);
            $this->header['Expires'] = gmdate('D, d M Y H:i:s', $expires) . ' GMT';
            $this->header['Cache-Control'] = 'max-age=' . ($expires - time());
            if (isset($this->header['Pragma']) && 'no-cache' == $this->header['Pragma']) {
                unset($this->header['Pragma']);
            }
        }

        return $this;
    }

    public function sendHeader(): self
    {
        // Send status code header
        if (false !== strpos(PHP_SAPI, 'cgi')) {
            header(
                sprintf(
                    'Status: %d %s',
                    $this->code,
                    self::$codes[$this->code]
                ),
                true
            );
        } else {
            header(
                sprintf(
                    '%s %d %s',
                    $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1',
                    $this->code,
                    self::$codes[$this->code]
                ),
                true,
                $this->code
            );
        }

        // Send other header
        foreach ($this->header as $field => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    header($field . ': ' . $v, false);
                }
            } else {
                header($field . ': ' . $value);
            }
        }

        // Send content length
        $length = $this->getContentLength();

        if ($length > 0) {
            header('Content-Length: ' . $length);
        }
        return $this;
    }

    public function getContentLength(): int
    {
        return extension_loaded('mbstring') ?
            mb_strlen($this->content, 'UTF8') :
            strlen($this->content);
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

        // 发送前再装载 debug 信息
        if ($this->isDebug && !empty($this->debugData)) {
            $this->data[$this->debugKey] = $this->debugData;
        }

        switch ($this->responseType) {
            case 'html':
                break;
            case 'xml':
                $this->setContent(Array2Xml::convert($this->data));
                break;
            default:
            case 'json':
                // JSON_UNESCAPED_UNICODE 中文也能显示
                $this->setContent(json_encode($this->data, JSON_UNESCAPED_UNICODE));
                break;
        }

        if (!headers_sent()) {
            $this->setContentType($this->mimeTypes[$this->responseType]);
            $this->sendHeader();
        }

        echo $this->content;

        $this->sent = true;
    }
}
