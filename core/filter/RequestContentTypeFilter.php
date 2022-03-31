<?php

namespace App\Core\Filter;

use App\Core\Filter;
use App\Core\Http\Request;

/**
 * 请求内容类型过滤器
 */
class RequestContentTypeFilter implements Filter
{

    public function doFilter()
    {
        $request = \App\DI()->request;

        if (empty($request->getContent())) {
            return true;
        }

        switch ($request->getContentType()) {
            case '':
                return false;
            default:
            case 'json':
                $this->transformJsonBody($request);
                return true;
        }
    }

    private function transformJsonBody(Request &$request)
    {
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON 无法解析');
        }
        if ($data === null) {
            return;
        }
        $request->request->replace($data);
    }
}
