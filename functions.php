<?php

namespace App;

use App\Core\DependencyInjection;
use App\Core\Http\Response;
use App\Model\LogModel;

function DI()
{
    return DependencyInjection::getInstance();
}

function debug($key, $value)
{
    return DI()
        ->get('response', new Response())
        ->appendDebug($key, $value);
}

function log()
{
    return DI()->get('log', new LogModel());
}
