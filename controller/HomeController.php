<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Core\Http\Response;
use App\Core\Response\ResultGenerator;

class HomeController extends BaseController
{
    public function home()
    {
        return ResultGenerator::errorWithMsg('Please see the api document to use!');
    }

    public function exampleDifferentTypeResponse()
    {
        // set response type when add route in router.php
        // or like this:
        // $this->response->setResponseType(Response::RESPONSE_TYPE_XML);
        return ResultGenerator::successWithData('test xml');
    }
}
