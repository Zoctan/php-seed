<?php

namespace App\Util;

class Url
{
    /**
     * remove scheme and www
     * remove trailing slash if there is no rest path
     *
     * eg.
     * demo.com => demo.com
     * http://demo.com => demo.com
     * http://demo.com/ => demo.com
     * http://demo.com/test => demo.com/test
     * http://demo.com/test/ => demo.com/test/
     * http://www.demo.com/test/ => demo.com/test/
     * https://www.demo.com/test/ => demo.com/test/
     * https://demo.www.demo.com/test/ => demo.www.demo.com/test/
     *
     * @param string $url
     */
    public static function stripUrl($url)
    {
        // remove scheme and www
        $url = preg_replace('/^https?:\/\/(www\.)?/', '', $url);
        // remove trailing slash
        $url = preg_replace('/^([^\/]+)\/$/', '$1', $url);
        return $url;
    }
    /**
     * add query params to url
     *
     * eg.
     * (http://demo.com', ['q'=>1]) => http://demo.com?q=1
     * (http://demo.com/', ['q'=>1]) => http://demo.com/?q=1
     * (http://demo.com/test', ['q'=>1]) => http://demo.com/test?q=1
     * (http://demo.com/test#redirect', ['q'=>1]) => http://demo.com/test?q=1#redirect
     *
     * @param string $url
     * @param array $addParams
     * @param bool $overwrite
     */
    public static function addQueryParams2Url($url, $addParams, $overwrite = true)
    {
        $parts = parse_url($url);
        if (array_key_exists('query', $parts) && $parts['query']) {
            parse_str($parts['query'], $params);
        } else {
            $params = [];
        }
        foreach ($addParams as $key => $value) {
            if (isset($params[$key]) && !$overwrite) {
                continue;
            }
            $params[$key] = $value;
        }
        $parts['query'] = http_build_query($params);
        return self::unparseUrl($parts);
    }
    /**
     * strip tracking params from url
     *
     * eg.
     * http://demo.com => http://demo.com
     * http://demo.com/test => http://demo.com/test
     * http://demo.com/?utm_source=wechat => http://demo.com/
     * http://demo.com/?q=1?utm_source=wechat => http://demo.com/?q=1
     *
     * @param string $url
     */
    public static function stripTrackingParams($url)
    {
        $parts = parse_url($url);
        if (!array_key_exists('query', $parts)) {
            return $url;
        }
        parse_str($parts['query'], $params);
        $newParams = [];
        foreach ($params as $key => $value) {
            if (substr($key, 0, 4) !== 'utm_') {
                $newParams[$key] = $value;
            }
        }
        $parts['query'] = http_build_query($newParams);
        return self::unparseUrl($parts);
    }
    /**
     * Input: Any URL or string, like: 'demo.com'
     * Output: Normalized URL (default to http if no scheme, force '/' path)
     *         or return null if not a valid URL
     *
     * eg.
     * http://demo.com/ => http://demo.com/
     * http://demo.com => http://demo.com/
     * demo.com => http://demo.com/
     * mailto:test@demo.com => null
     *
     * @param string $url
     */
    public static function normalize($url)
    {
        $parts = parse_url($url);
        if (array_key_exists('path', $parts) && $parts['path'] == '') {
            return null;
        }
        // parse_url returns just 'path' for naked domains
        if (count($parts) == 1 && array_key_exists('path', $parts)) {
            $parts['host'] = $parts['path'];
            unset($parts['path']);
        }
        if (!array_key_exists('scheme', $parts)) {
            $parts['scheme'] = 'http';
        }
        if (!array_key_exists('path', $parts)) {
            $parts['path'] = '/';
        }
        // invalid scheme
        if (!in_array($parts['scheme'], ['http', 'https'])) {
            return null;
        }
        return self::unparseUrl($parts);
    }
    /**
     * inverse of parse_url (https://php.net/parse_url)
     *
     * @param array $parsedUrl
     */
    public static function unparseUrl($parsedUrl)
    {
        $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $port = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $user = isset($parsedUrl['user']) ? $parsedUrl['user'] : '';
        $pass = isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }
    /**
     * is two host equal
     *
     * eg.
     * http://demo.com/ == https://demo.com/test
     * http://demo.com/ != https://sub.demo.com/test
     *
     * @param string $a
     * @param string $b
     */
    public static function hostEqual($a, $b)
    {
        return parse_url($a, PHP_URL_HOST) === parse_url($b, PHP_URL_HOST);
    }
    /**
     * is url
     *
     * @param string $url
     */
    public static function isUrl($url)
    {
        return is_string($url) && preg_match('/^https?:\/\/[a-z0-9\.\-]\/?/', $url);
    }
    /**
     * is public ip
     *
     * @param string $ip
     */
    public static function isPublicIp($ip)
    {
        $privateNetworkList = [
            '10.0.0.0~10.255.255.255', // single class A network
            '172.16.0.0~172.31.255.255', // 16 contiguous class B network
            '192.168.0.0~192.168.255.255', // 256 contiguous class C network
            '169.254.0.0~169.254.255.255', // Link-local address also refered to as Automatic Private IP Addressing
            '127.0.0.0~127.255.255.255', // localhost
        ];
        $longIp = ip2long($ip);
        if ($longIp) {
            foreach ($privateNetworkList as $privateNetwork) {
                list($start, $end) = explode('~', $privateNetwork);
                if ($longIp >= ip2long($start) && $longIp <= ip2long($end)) {
                    return true;
                }
            }
        }
        return false;
    }
}
