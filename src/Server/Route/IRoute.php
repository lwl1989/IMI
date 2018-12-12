<?php
namespace Imi\Server\Route;

use Psr\Http\Message\RequestInterface;

interface IRoute
{
    /**
     * 路由解析处理
     * @param Request $request
     * @return array
     */
    public function parse(RequestInterface $request);
}