<?php

namespace App\Core;

/**
 *  Dependency Injection 依赖注入容器
 *  
 * - 调用的方式有：set/get函数、魔法方法setX/getX、类变量$fdi->X、数组$fdi['X]
 * - 初始化的途径：直接赋值、类名、匿名函数
 *
 *  示例：
 *       $di = new DI();
 *      
 *       // 用的方式有：set/get函数  魔法方法setX/getX、类属性$di->X、数组$di['X']
 *       $di->key = 'value';
 *       $di['key'] = 'value';
 *       $di->set('key', 'value');
 *       $di->setKey('value');
 *      
 *       echo $di->key;
 *       echo $di['key'];
 *       echo $di->get('key');
 *       echo $di->getKey();
 *      
 *       // 初始化的途径：直接赋值、类名(会回调onInitialize函数)、匿名函数
 *       $di->simpleKey = array('value');
 *       $di->classKey = 'DependenceInjection';
 *       $di->closureKey = function () {
 *            return 'sth heavy ...';
 *       };
 */

class DependencyInjection implements \ArrayAccess
{

    use Singleton;

    // 服务命中的次数
    protected $hitTimes = [];

    // 服务池
    protected $data = [];

    private function __construct()
    {
    }

    /**
     * setter
     */
    public function set($key, $value)
    {
        $this->hitTimes[$key] = 0;

        $this->data[$key] = $value;

        return $this;
    }

    /**
     * getter
     */
    public function get($key, $default = null)
    {
        if (!isset($this->data[$key])) {
            $this->data[$key] = $default;
        }

        // 内联操作，减少函数调用，提升性能
        if (!isset($this->hitTimes[$key])) {
            $this->hitTimes[$key] = 0;
        }
        $this->hitTimes[$key]++;

        if ($this->hitTimes[$key] == 1) {
            $this->data[$key] = $this->initService($this->data[$key]);
        }

        return $this->data[$key];
    }

    /**
     * 是否存在值
     */
    public function hasValue($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * 是否存在实例
     */
    public function hasInstance($className)
    {
        foreach ($this->data as $key => $value) {
            if (class_exists($value) && get_class($value) == $className) {
                return true;
            }
        }
        return false;
    }

    /**
     * 类   => 初始化
     * 方法 => 调用
     * 值   => 调用
     */
    protected function initService($value)
    {
        $rs = null;

        if ($value instanceof \Closure) {
            $rs = $value();
        } elseif (is_string($value) && class_exists($value)) {
            $rs = new $value();
        } else {
            $rs = $value;
        }

        return $rs;
    }

    private function resolveClassDependancy(\ReflectionClass $dependancyClass)
    {
        $dependancyClassName = $dependancyClass->getName();

        if ($this->hasInstance($dependancyClassName)) {
            return $this->get($dependancyClassName);
        }

        // try to match by interfaces
        $interfaces = $dependancyClass->getInterfaces();
        foreach ($interfaces as $interface) {
            $resolvedService = $this->resolveClassDependancy($interface);
            if (null !== $resolvedService) {
                return $resolvedService;
            }
        }

        // fallback to parent class
        if ($parentClass = $dependancyClass->getParentClass()) {
            return $this->resolveClassDependancy($parentClass);
        }
    }

    /** ------------------ 魔法方法 ------------------ **/

    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) == 'set') {
            $key = lcfirst(substr($name, 3));
            return $this->set($key, isset($arguments[0]) ? $arguments[0] : null);
        } else if (substr($name, 0, 3) == 'get') {
            $key = lcfirst(substr($name, 3));
            return $this->get($key, isset($arguments[0]) ? $arguments[0] : null);
        }

        throw new \Exception("Call to undefined method DI::$name()");
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __get($name)
    {
        return $this->get($name, null);
    }

    /** ------------------ 实现 ArrayAccess ------------------ **/

    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetGet($offset): mixed
    {
        return $this->get($offset, null);
    }

    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }
}
