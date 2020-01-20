<?php
namespace Imi\Server\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 控制器注解
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Server\Route\Parser\ControllerParser")
 */
class Controller extends Base
{
    /**
     * 只传一个参数时的参数名
     * @var string
     */
    protected $defaultFieldName = 'prefix';

    /**
     * 路由前缀
     * @var string
     */
    public $prefix;

    /**
     * 是否为单例控制器
     * 
     * 默认为 null 时取 '@server.服务器名.controller.singleton'
     *
     * @var null|bool
     */
    public $singleton;

}