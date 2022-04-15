<?php

namespace App\Controller;

use App\Model\SystemModel;
use App\Core\BaseController;
use App\Core\Result\Result;

/**
 * SystemController
 */
class SystemController extends BaseController
{
    /**
     * @var SystemModel
     */
    private $systemModel;

    public function __construct(SystemModel $systemModel)
    {
        parent::__construct();
        $this->systemModel = $systemModel;
    }

    /**
     * Get Value by key
     * 
     * @param string key
     */
    public function getValue()
    {
        $key = strval($this->request->get('key'));
        if (empty($key)) {
            return Result::error('Key does not exist');
        }
        $result = $this->systemModel->getValue($key);
        return Result::success($result);
    }

    /**
     * Add system
     * 
     * @param object system
     */
    public function add()
    {
        $system = $this->request->get('system');
        if (empty($system)) {
            return Result::error('System does not exist');
        }
        $this->systemModel->insert($system);
        return Result::success();
    }

    /**
     * Update system
     * 
     * @param object system
     */
    public function update()
    {
        $system = $this->request->get('system');
        if (empty($system)) {
            return Result::error('System does not exist');
        }
        $this->systemModel->updateById($system, $system['id']);
        return Result::success();
    }

    /**
     * Delete system by id
     * 
     * @param int systemId
     */
    public function delete()
    {
        $systemId = intval($this->request->get('id'));
        if (empty($systemId)) {
            return Result::error('System id does not exist');
        }
        $this->systemModel->deleteById($systemId);
        return Result::success();
    }
}
