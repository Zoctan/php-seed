<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Core\Result\Result;

/**
 * FakeController
 */
class FakeController extends BaseController
{
    /**
     * Get fake name
     * 
     * @return Result
     */
    public function getName()
    {
        $name = \App\DI()->faker->name;
        return Result::success($name);
    }
}
