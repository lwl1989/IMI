<?php
namespace Imi\Util;

if('cli' !== PHP_SAPI && !class_exists(\Swoole\Coroutine::class))
{
    class Coroutine
    {
        
    }
    return;
}

abstract class Coroutine extends \Swoole\Coroutine
{
    /**
     * 判断当前是否在协程中运行
     * @return boolean
     */
    public static function isIn()
    {
        return static::getuid() > -1;
    }
}