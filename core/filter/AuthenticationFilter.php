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
        \App\debug('routes', $this->routes);

        // fixme暂时这样处理upload接口
        if (strpos($uri, '/upload/') === 0) {
            $auth = false;
        } else {
            $auth = $this->routes[$uri]->auth;
        }
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
            $needPermissionList = $this->routes[$uri]->permission;
            $authMember = $jwtUtil->getAuthMember($token);
            if (!$authMember->checkPermission($needPermissionList)) {
                throw new AccessTokenException('no permission to visit this route');
                return false;
            }
            // 注入已认证的成员信息
            \App\DI()->authMember = $authMember;
        }
        return true;
    }
}
