<?php
namespace Imi\NoSwoole\HttpMessage;

use Imi\Util\Uri;


class Request extends \Imi\Server\Http\Message\Request
{
    public function __construct()
    {
        // $this->swooleRequest = $request;
        // $this->serverInstance = $server;
        // $body = $request->rawContent();
        // if(false === $body)
        // {
        //     $body = '';
        // }
        // parent::__construct($this->getRequestUri(), $request->header, $body, strtoupper($request->server['request_method']), $this->getRequestProtocol(), $request->server, $request->cookie ?? [], $request->get ?? [], $request->post ?? [], $request->files ?? []);
        $this->uri = new Uri('http://127.0.0.1:12580/');
        $this->server['path_info'] = $_SERVER['REQUEST_URI'];
    }
}