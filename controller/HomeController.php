<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Core\Http\MimeType;
use App\Core\Result\Result;

/**
 * HomeController
 */
class HomeController extends BaseController
{
    /**
     * Home (do not visit this api)
     * 
     * @return Result
     */
    public function home()
    {
        return Result::error('Please see the api document to use!');
    }

    public function demo1()
    {
        return Result::success('demo1');
    }

    public function demo2()
    {
        return Result::success('demo2');
    }

    /**
     * Demo for response in different mime type
     * 
     * @return Result
     */
    public function demoDifferentMimeType()
    {
        // set mime type when add route in router.php
        // or like this:
        // $this->response->setMimeType(MimeType::XML);
        // $this->response->setMimeType(MimeType::JSON);
        return Result::success(['test' => 'demo xml']);
    }
}
