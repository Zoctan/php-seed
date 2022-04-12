<?php

namespace App\Core\Filter;

use App\Core\Filter;
use App\Util\JwtUtil;
use App\Core\Exception\AccessTokenException;

/**
 * 认证过滤器
 */
class AuthenticationFilter implements Filter
{
    /**
     * @var array
     */
    private $routeList;
    /**
     * @var Request
     */
    private $request;

    public function __construct()
    {
        $this->routeList = \App\DI()->router->getRouteList();
        $this->request = \App\DI()->request;
    }

    public function doFilter()
    {
        $uri = $this->request->uri;

        \App\debug('uri', $uri);
        \App\debug('request base', $this->request->base);
        // \App\debug('routeList', $this->routeList);

        $auth = $this->routeList[$uri]->auth;
        if ($auth) {
            $jwtUtil = JwtUtil::getInstance();
            $token = $jwtUtil->getTokenFromRequest($this->request);
            if (empty($token)) {
                throw new AccessTokenException('empty token');
                return false;
            }
            if (!$jwtUtil->validateTokenRedis($token)) {
                throw new AccessTokenException('invalid token');
                return false;
            }
            $needPermissionList = $this->routeList[$uri]->permission;
            $authMember = $jwtUtil->getAuthMember($token);
            if (!$authMember->checkPermission($needPermissionList)) {
                throw new AccessTokenException('no permission to visit this route');
                return false;
            }
            // inject auth member info
            \App\DI()->authMember = $authMember;
        }
        return true;
    }
}
