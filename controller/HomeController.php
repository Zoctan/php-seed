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
     * Home
     * do not visit this api
     */
    public function home()
    {
        return Result::error('Please see the api document to use!');
    }

    /**
     * example for response in different mime type
     */
    public function exampleDifferentMimeType()
    {
        // set mime type when add route in router.php
        // or like this:
        // $this->response->setContentType(MimeType::XML);
        // $this->response->setContentType(MimeType::JSON);
        return Result::success('test xml');
    }
}
