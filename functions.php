<?php

namespace App;

use App\Core\DependencyInjection;

function DI()
{
    return DependencyInjection::getInstance();
}

function debug($key, $value)
{
    return DI()->response->appendDebug($key, $value);
}
