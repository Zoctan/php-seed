<?php

namespace App;

use App\Core\DependencyInjection;
use App\Core\HTTP\Response;

function DI()
{
    return DependencyInjection::getInstance();
}

function debug($key, $value)
{
    return DI()->get('response', new Response())->appendDebug($key, $value);
}
