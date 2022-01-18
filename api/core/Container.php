<?php

namespace PHPSeed\Core;

use Closure;

/**
 * IoC 容器
 */
class Container
{
    use Singleton;

    protected $bindings = [];

    private function __construct()
    {
    }

    /**
     * 绑定[接口实例/键值对数组]
     */
    public function bind($key, $value)
    {
        if (isset($this->bindings[$key])) {
            return;
        }
        $this->bindings[$key] = $value;
    }

    /**
     * 从容器中解析绑定的内容
     */
    public function resolve($key)
    {
        $value = $this->bindings[$key];
        if ($value instanceof Closure) {
            // 实例
            return call_user_func($value);
        } else {
            // 值
            return $value;
        }
    }
}
