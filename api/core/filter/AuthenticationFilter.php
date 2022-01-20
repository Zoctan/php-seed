<?php

namespace PHPSeed\Core\Filter;

use PHPSeed\Core\DI;
use PHPSeed\Core\Filter;
use PHPSeed\Core\JwtUtil;
use PHPSeed\Core\Exception\UnAuthorizedException;

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
        $request = DI::getInstance()->request;
        // 需要认证的路由才检查
        $uri =  $request->getPath();
        if ($this->routes[$uri]->needAuth) {
            $jwtUtil = JwtUtil::getInstance();
            $token = $jwtUtil->getTokenFromRequest($request);
            if (empty($token)) {
                throw new UnAuthorizedException("空 token");
            }
            if (!$jwtUtil->validateToken($token)) {
                throw new UnAuthorizedException("无效 token");
            }
            // 注入已认证的成员信息
            DI::getInstance()->authMember = $jwtUtil->getAuthentication($token);
        }
    }
}
