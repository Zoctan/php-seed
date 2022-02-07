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
    /**
     * @var array
     */
    private $routes;
    private $request;
    private $response;

    public function __construct($routes)
    {
        $this->routes = $routes;
        $this->request = \App\DI()->request;
        $this->response = \App\DI()->response;
    }

    public function doFilter()
    {

        // 需要认证的路由才检查
        $uri = $this->request->getPath();
        $authOperate = $this->routes[$uri]->authOperate;
        if ($authOperate) {
            $jwtUtil = JwtUtil::getInstance();
            $token = $jwtUtil->getTokenFromRequest($this->request);
            if (empty($token)) {
                throw new UnAuthorizedException("empty token");
                return false;
            }
            if (!$jwtUtil->validateTokenRedis($token)) {
                throw new UnAuthorizedException("invalid token");
                return false;
            }
            $authMember = $jwtUtil->getAuthMember($token);
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
