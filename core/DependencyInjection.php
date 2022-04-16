<?php

namespace App\Core;

/**
 *  Dependency Injection
 *  
 * Call method:
 * - setter/getter: set(key, value), get(key)
 * - magic setter/getter: setKey(value), getKey()
 * - object attribution: ->key
 * - array attribution: [key]
 * 
 * Initializes method:
 * - define value directly
 * - define class name
 * - define closure function
 *
 *  eg.
 *       $di = new DI();
 * 
 *       // Call method:
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
 *       // Initializes method:
 *       $di->simpleKey = array('value');
 *       $di->classKey = 'DependenceInjection';
 *       $di->closureKey = function () {
 *            return 'sth heavy ...';
 *       };
 */

class DependencyInjection implements \ArrayAccess
{

    use Singleton;

    // Service hit times
    protected $hitTimes = [];

    // Data pool
    protected $data = [];

    private function __construct()
    {
    }

    /**
     * Setter
     */
    public function set($key, $value)
    {
        $this->hitTimes[$key] = 0;

        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Getter
     */
    public function get($key, $default = null)
    {
        if (!isset($this->data[$key])) {
            $this->data[$key] = $default;
        }

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
     * class => initial
     * closure function => call
     * value => return
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

    /**
     * New instance using reflection
     */
    public function newInstance(string $class): object
    {
        $reflectionClass = new \ReflectionClass($class);

        if (($constructor = $reflectionClass->getConstructor()) === null) {
            return $reflectionClass->newInstance();
        }

        if (($params = $constructor->getParameters()) === []) {
            return $reflectionClass->newInstance();
        }

        $newInstanceParams = [];
        foreach ($params as $param) {
            $newInstanceParams[] = $param->getType() === null
                ? $param->getDefaultValue()
                : $this->newInstance($param->getType()->getName());
        }

        return $reflectionClass->newInstanceArgs($newInstanceParams);
    }

    /** ------------------ Magic function ------------------ **/

    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) == 'set') {
            $key = lcfirst(substr($name, 3));
            return $this->set($key, isset($arguments[0]) ? $arguments[0] : null);
        } else if (substr($name, 0, 3) == 'get') {
            $key = lcfirst(substr($name, 3));
            return $this->get($key, isset($arguments[0]) ? $arguments[0] : null);
        }

        throw new \Exception('Call to undefined method DI::$name()');
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __get($name)
    {
        return $this->get($name, null);
    }

    /** ------------------ ArrayAccess ------------------ **/

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
