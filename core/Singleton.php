<?php

namespace App\Core;

/** 
 * Singleton
 * 
 * add below code to class inside, eg.
 * 
 * class Demo {
 *   use Singleton;
 * }
 */
trait Singleton
{

    private static $instance = null;

    private function __construct()
    {
        parent::__construct();
    }

    private function __clone()
    {
    }

    public function __sleep()
    {
        // rewrite __sleep function, return empty, protect from serialize create new instance
        return [];
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            // do not use new self()
            self::$instance = new static();
        }
        return self::$instance;
    }
}
