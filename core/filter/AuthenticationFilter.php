<?php

namespace App\Core\Filter;

use App\Core\Filter;
use App\Util\JwtUtil;
use App\Core\Exception\AccessTokenException;

/**
 * Authentication filter
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
        // \App\debug('uri', $uri);

        // check authentication
        $auth = $this->routeList[$uri]->auth;
        if ($auth) {
            $jwtUtil = JwtUtil::getInstance();
            $token = $jwtUtil->getTokenFromRequest();
            if (empty($token)) {
                throw new AccessTokenException('empty token');
                return false;
            }
            if (!$jwtUtil->validateTokenRedis($token)) {
                throw new AccessTokenException('invalid token');
                return false;
            }
            // check permission
            $needPermissionList = $this->routeList[$uri]->permission;
            $authMember = $jwtUtil->getAuthMember($token);
            if (!$authMember->checkPermission($needPermissionList)) {
                throw new AccessTokenException('no permission to visit this route');
                return false;
            }
            // inject authentication member
            \App\DI()->authMember = $authMember;
        }
        return true;
    }
}
