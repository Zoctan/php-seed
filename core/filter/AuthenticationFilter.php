<?php

namespace App\Core\Filter;

use App\Core\Filter;
use App\Util\Jwt;
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
            $jwt = Jwt::getInstance();
            $token = $jwt->getTokenFromRequest();
            if (empty($token)) {
                throw new AccessTokenException('Empty token');
                return false;
            }
            if (!$jwt->validateTokenRedis($token)) {
                throw new AccessTokenException('Invalid token');
                return false;
            }
            // check permission
            $needPermissionList = $this->routeList[$uri]->permission;
            $authMember = $jwt->getAuthMember($token);
            if (!$authMember->checkPermission($needPermissionList)) {
                throw new AccessTokenException('No permission to visit this route');
                return false;
            }
            // inject authentication member
            \App\DI()->authMember = $authMember;
        }
        return true;
    }
}
