<?php
namespace Imi\Server\Http;

use Imi\App;
use Imi\RequestContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandler implements RequestHandlerInterface
{
    /**
     * 中间件数组
     * @var string[]
     */
    protected $middlewares = [];

    /**
     * 当前执行第几个
     * @var int
     */
    protected $index = 0;

    /**
     * 构造方法
     * @param string[] $middlewares 中间件数组
     */
    public function __construct(array $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * Handle the request and return a response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if(isset($this->middlewares[$this->index]))
        {
            $middleware = $this->middlewares[$this->index];
            if(is_object($middleware))
            {
                $requestHandler = $middleware;
            }
            else
            {
                $requestHandler = RequestContext::getServerBean($middleware);
            }
        }
        else
        {
            return RequestContext::get('response');
        }
        return $requestHandler->process($request, $this->next());
    }

    /**
     * 获取下一个RequestHandler对象
     * @return static
     */
    protected function next()
    {
        ++$this->index;
        return $this;
    }

    /**
     * 是否是最后一个
     * @return boolean
     */
    public function isLast()
    {
        return !isset($this->middlewares[$this->index]);
    }
    
}