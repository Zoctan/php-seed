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
    private $routes;
    /**
     * @var Request
     */
    private $request;

    public function __construct()
    {
        $this->routes = \App\DI()->router->getRoutes();
        $this->request = \App\DI()->request;
    }

    public function doFilter()
    {
        // 需要认证的路由才检查
        $uri = $this->request->uri;
        \App\debug('uri', $uri);

        // fixme暂时这样处理upload接口
        if (strpos($uri, '/upload') === 0) {
            $requiresAuth = false;
        } else {
            $requiresAuth = $this->routes[$uri]->requiresAuth;
        }
        if ($requiresAuth) {
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
            $needPermissionList = $this->routes[$uri]->auth;
            $authMember = $jwtUtil->getAuthMember($token);
            if (!$authMember->has($needPermissionList)) {
                throw new AccessTokenException('no permission to visit this route');
                return false;
            }
            // 注入已认证的成员信息
            \App\DI()->authMember = $authMember;
        }
        return true;
    }
}
