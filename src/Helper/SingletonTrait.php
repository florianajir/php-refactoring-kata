<?php

trait SingletonTrait
{
    /**
     * @var $this
     */
    protected static $instance = null;

    /**
     * @return $this
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }
}
