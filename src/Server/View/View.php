<?php
namespace Imi\Server\View;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\Message\Response;
use \Imi\Server\View\Annotation\View as ViewAnnotation;
use Imi\App;
use Imi\RequestContext;

/**
 * 视图类
 * @Bean("View")
 */
class View
{
    /**
     * 核心处理器
     * @var array
     */
    protected $coreHandlers = [
        'html'  => \Imi\Server\View\Handler\Html::class,
        'json'  => \Imi\Server\View\Handler\Json::class,
        'xml'   => \Imi\Server\View\Handler\Xml::class,
    ];

    /**
     * 扩展处理器
     * @var array
     */
    protected $exHandlers = [];

    /**
     * 传入视图处理器的数据
     * @var array
     */
    protected $data = [];

    /**
     * 视图处理器对象列表
     *
     * @var \Imi\Server\View\Handler\IHandler[]
     */
    protected $handlers;

    public function __init()
    {
        foreach([$this->coreHandlers, $this->exHandlers] as $list)
        {
            foreach($list as $name => $class)
            {
                $this->handlers[$name] = RequestContext::getServerBean($class);
            }
        }
    }

    public function render($renderType, $data, $options, Response $response = null): Response
    {
        if(isset($this->handlers[$renderType]))
        {
            if($this->data && is_array($data))
            {
                $data = array_merge($this->data, $data);
            }
            if(null === $response)
            {
                $response = RequestContext::get('response');
            }
            return $this->handlers[$renderType]->handle($data, $options, $response);
        }
        else
        {
            throw new \RuntimeException('Unsupport View renderType: ' . $renderType);
        }
    }

}