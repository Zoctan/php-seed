<?php

namespace App\Core\Filter;

use App\Core\Filter;
use App\Util\JwtUtil;
use App\Core\Exception\UnAuthorizedException;

/**
 * 认证过滤器
 */
class AuthenticationFilter implements Filter
{
    private $routes;

    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    public function doFilter()
    {
        $request = \App\DI()->request;

        // 需要认证的路由才检查
        $uri = $request->getPath();
        $authOperate = $this->routes[$uri]->authOperate;
        if ($authOperate) {
            $jwtUtil = JwtUtil::getInstance();
            $token = $jwtUtil->getTokenFromRequest($request);
            if (empty($token)) {
                throw new UnAuthorizedException("empty token");
                return false;
            }
            if (!$jwtUtil->validateTokenRedis($token)) {
                throw new UnAuthorizedException("invalid token");
                return false;
            }
            $authMember = $jwtUtil->getAuthentication($token);
            if (!$authMember->has($authOperate)) {
                throw new UnAuthorizedException("no auth operate");
                return false;
            }
            // 注入已认证的成员信息
            \App\DI()->authMember = $authMember;
        }
        return true;
    }
}
