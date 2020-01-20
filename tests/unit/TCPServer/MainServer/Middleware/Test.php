<?php
namespace Imi\Test\TCPServer\MainServer\Middleware;

use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Server\TcpServer\IReceiveHandler;
use Imi\Server\TcpServer\Message\IReceiveData;
use Imi\Server\TcpServer\Middleware\IMiddleware;

/**
 * @Bean
 */
class Test implements IMiddleware
{
    public function process(IReceiveData $data, IReceiveHandler $handler)
    {
        RequestContext::set('middlewareData', 'imi');
        return $handler->handle($data, $handler);
    }
}