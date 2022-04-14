<?php

namespace App\Core\Response;

/**
 * enum result code
 * 
 * business error from 2***
 * BaseException extension from 4***
 */
class ResultCode
{
    public const SUCCEED = [0, 'success'];

    public const FAILED = [1, 'failed'];

    public const UNKNOWN_FAILED = [2, 'unknown failed'];

    public const SUCCEED_REQUEST_FAILED_RESULT = [1000, 'succeed request, but failed result'];

    public const FIND_FAILED = [2000, 'find failed'];

    public const SAVE_FAILED = [2001, 'save failed'];

    public const UPDATE_FAILED = [2002, 'update failed'];

    public const DELETE_FAILED = [2003, 'delete failed'];

    public const DUPLICATE_NAME = [2004, 'duplicate name'];

    public const TOKEN_EXCEPTION = [4001, 'token exception'];

    public const ACCESS_TOKEN_EXCEPTION = [4002, 'access token exception'];

    public const REFRESH_TOKEN_EXCEPTION = [4003, 'refresh token exception'];

    public const DATABASE_EXCEPTION = [4004, 'datebase exception'];

    public const VIOLATION_EXCEPTION = [4005, 'violation exception'];

    public const ROUTER_EXCEPTION = [4006, 'router exception'];
}
