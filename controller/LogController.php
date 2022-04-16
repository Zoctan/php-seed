<?php

namespace App\Controller;

use App\Model\LogModel;
use App\Core\BaseController;
use App\Core\Result\Result;

/**
 * LogController
 */
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

    /**
     * List log
     * 
     * @param int currentPage
     * @param int pageSize
     * @param int level
     * @param string content
     * @param string created_at
     * @return Result
     */
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
        return Result::success($result);
    }

    /**
     * Delete log
     * 
     * @param int id
     * @return Result
     */
    public function delete()
    {
        $logId = intval($this->request->get('id'));
        if (empty($logId)) {
            return Result::error('Log id does not exist');
        }
        $this->logModel->deleteById($logId);
        return Result::success();
    }
}
