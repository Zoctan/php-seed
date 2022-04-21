<?php

namespace App\Core\Filter;

use App\Core\BaseFilter;
use App\Util\Jwt;
use App\Core\Exception\AccessTokenException;

/**
 * Authentication filter
 */
class AuthenticationFilter extends BaseFilter
{
    public function doFilter()
    {
        $uri = $this->request->uri;
        // \App\debug('uri', $uri);

        // check authentication
        $auth = $this->router->getRoute($uri)->auth;
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
            $needPermissionList = $this->router->getRoute($uri)->permission;
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
