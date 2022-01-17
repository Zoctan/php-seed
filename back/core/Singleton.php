<?php

/** 
 * Trait Singleton 单例模式
 * 
 * 在需要单例的 class 下加上：
 * use Singleton;
 */

trait Singleton
{

    private static $instance = null;

    private function __construct()
    {
        parent::__construct();
        // 私有化构造方法
    }

    private function __clone()
    {
        // 私有化克隆方法
    }

    public function __sleep()
    {
        //重写__sleep方法，将返回置空，防止序列化反序列化获得新的对象
        return [];
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            //这里不能new self()
            self::$instance = new static();
        }
        return self::$instance;
    }
}
