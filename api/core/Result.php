<?php

namespace Seed\Core;

class Result
{

    public static function success2jump($data = null, $page = null)
    {
        echo json_encode(["errno" => 0, "data" => $data, "page" => $page]);
    }

    public static function success($data = null)
    {
        echo json_encode(["errno" => 0, "data" => $data]);
    }

    public static function error($msg = null)
    {
        echo json_encode(["errno" => 1, "msg" => $msg]);
    }
}
