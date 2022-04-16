<?php

namespace App\Controller;

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

    public function __construct(PairModel $pairModel)
    {
        parent::__construct();
        $this->pairModel = $pairModel;
    }

    /**
     * Get Value by key
     * 
     * @param string key
     * @return Result
     */
    public function getValue()
    {
        $key = strval($this->request->get('key'));
        if (empty($key)) {
            return Result::error('Key does not exist');
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
            return Result::error('Pair does not exist');
        }
        $this->pairModel->insert($pair);
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
            return Result::error('Pair does not exist');
        }
        $this->pairModel->updateById($pair, $pair['id']);
        return Result::success();
    }

    /**
     * Delete pair by id
     * 
     * @param int pairId
     * @return Result
     */
    public function delete()
    {
        $pairId = intval($this->request->get('id'));
        if (empty($pairId)) {
            return Result::error('Pair id does not exist');
        }
        $this->pairModel->deleteById($pairId);
        return Result::success();
    }
}
