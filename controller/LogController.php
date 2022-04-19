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
     * @param object log {level:int, content:string, created_at:string}
     * @return Result
     */
    public function list()
    {
        $currentPage = intval($this->request->get('currentPage', 0));
        $pageSize = intval($this->request->get('pageSize', 20));

        $log = $this->request->get('log');

        $where = [];
        if ($log) {
            if (isset($log['level']) && is_numeric($log['level'])) {
                $where['level'] = $log['level'];
            }
            if (isset($log['content'])) {
                $where['content[~]'] = $log['content'];
            }
            if (isset($log['created_at'])) {
                $where['created_at'] = $log['created_at'];
            }
        }
        $result = $this->logModel->page($currentPage, $pageSize, $this->logModel->getColumns(), $where);
        return Result::success($result);
    }

    /**
     * Remove log
     * 
     * @param int id
     * @return Result
     */
    public function remove()
    {
        $logId = intval($this->request->get('id'));
        if (empty($logId)) {
            return Result::error('Log id does not exist.');
        }
        $this->logModel->deleteById($logId);
        return Result::success();
    }
}
