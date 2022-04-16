<?php

namespace App\Core\Result;

/**
 * enum result code
 * 
 * business error from 2***
 * BaseException extension from 4***
 */
class ResultCode
{
    public const SUCCEED = [0, 'Success'];
    public const FAILED = [1, 'Failed'];
    public const UNKNOWN_FAILED = [2, 'Unknown failed'];

    public const SUCCEED_REQUEST_FAILED_RESULT = [2001, 'Succeed request, but failed result'];
    public const FIND_FAILED = [2002, 'Find failed'];
    public const SAVE_FAILED = [2003, 'Save failed'];
    public const UPDATE_FAILED = [2004, 'Update failed'];
    public const DELETE_FAILED = [2005, 'Delete failed'];
    public const DUPLICATE_NAME = [2006, 'Duplicate name'];

    public const TOKEN_EXCEPTION = [4001, 'Token exception'];
    public const ACCESS_TOKEN_EXCEPTION = [4002, 'Access token exception'];
    public const REFRESH_TOKEN_EXCEPTION = [4003, 'Refresh token exception'];
    public const DATABASE_EXCEPTION = [4004, 'Datebase exception'];
    public const ROUTER_EXCEPTION = [4005, 'Router exception'];
    public const VIOLATION_EXCEPTION = [4006, 'Violation exception'];
}
