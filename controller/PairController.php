<?php

namespace App\Controller;

use App\Model\LogModel;
use App\Model\PairModel;
use App\Core\BaseController;
use App\Core\Result\Result;

/**
 * PairController
 */
class PairController extends BaseController
{
    /**
     * @var PairModel
     */
    private $pairModel;
    /**
     * @var LogModel
     */
    private $logModel;

    public function __construct(PairModel $pairModel, LogModel $logModel)
    {
        parent::__construct();
        $this->pairModel = $pairModel;
        $this->logModel = $logModel;
    }

    /**
     * List pair
     * 
     * @param int currentPage
     * @param int pageSize
     * @param object pair {description:string, key:string, value:string}
     * @return Result
     */
    public function list()
    {
        $currentPage = intval($this->request->get('currentPage', 0));
        $pageSize = intval($this->request->get('pageSize', 20));

        $pair = $this->request->get('pair');

        $where = [];
        if ($pair) {
            if (isset($pair['description'])) {
                $where['description[~]'] = $pair['description'];
            }
            if (isset($pair['key'])) {
                $where['key[~]'] = $pair['key'];
            }
            if (isset($pair['value'])) {
                $where['value[~]'] = $pair['value'];
            }
        }
        $result = $this->pairModel->page($currentPage, $pageSize, $this->pairModel->getColumns(), $where);
        return Result::success($result);
    }

    /**
     * Get Value by key
     * 
     * @param string|array key
     * @return Result
     */
    public function getValue()
    {
        $key = $this->request->get('key');
        if (empty($key)) {
            return Result::error('Key does not exist.');
        }
        $result = $this->pairModel->getValue($key);
        return Result::success($result);
    }

    /**
     * Add pair
     * 
     * @param object pair
     * @return Result
     */
    public function add()
    {
        $pair = $this->request->get('pair');
        if (empty($pair)) {
            return Result::error('Pair does not exist.');
        }
        $pairId = $this->pairModel->insert($pair);
        $this->logModel->asInfo(sprintf('Add pair: [id:%d][key:%s][value:%s].', $pairId, $pair['key'], $pair['value']));
        return Result::success();
    }

    /**
     * Update pair
     * 
     * @param object pair
     * @return Result
     */
    public function update()
    {
        $pair = $this->request->get('pair');
        if (empty($pair)) {
            return Result::error('Pair does not exist.');
        }
        $this->pairModel->updateById($pair, $pair['id']);
        $this->logModel->asInfo(sprintf('Update pair: [id:%d][key:%s][value:%s].', $pair['id'], $pair['key'], $pair['value']));
        return Result::success();
    }

    /**
     * Remove pair by id
     * 
     * @param int pairId
     * @return Result
     */
    public function remove()
    {
        $pairId = intval($this->request->get('id'));
        if (empty($pairId)) {
            return Result::error('Pair id does not exist.');
        }
        $this->pairModel->deleteById($pairId);
        $this->logModel->asInfo(sprintf('Remove pair: [id:%d].', $pairId));
        return Result::success();
    }
}
