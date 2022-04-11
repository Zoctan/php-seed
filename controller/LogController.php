<?php

namespace App\Controller;

use App\Model\LogModel;
use App\Core\BaseController;
use App\Core\Response\ResultGenerator;

class LogController extends BaseController
{
    /**
     * @var LogModel
     */
    private $logModel;

    public function __construct(LogModel $logModel)
    {
        parent::__construct();
        $this->logModel = $logModel;
    }

    public function list()
    {
        $currentPage = intval($this->request->get('currentPage', 0));
        $pageSize = intval($this->request->get('pageSize', 20));

        $level = intval($this->request->get('level'));
        $content = strval($this->request->get('content'));
        $created_at = strval($this->request->get('created_at'));

        $where = [];
        if (is_numeric($level)) {
            $where['level'] = $level;
        }
        if (!empty($content)) {
            $where['content[~]'] = $content;
        }
        if (!empty($created_at)) {
            $where['created_at'] = $created_at;
        }

        $result =  $this->logModel->page($currentPage, $pageSize, [
            'id [Int]',
            'member_id [Int]',
            'level [Int]',
            'content',
            'ip',
            'ip_city',
            'extra [JSON]',
            'created_at',
        ], $where);
        return ResultGenerator::successWithData($result);
    }

    public function delete()
    {
        $id = intval($this->request->get('id'));
        $logModel = new LogModel();
        $logModel->deleteById($id);
        return ResultGenerator::success();
    }
}
