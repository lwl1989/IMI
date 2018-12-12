<?php
namespace Imi\NoSwoole\HttpMessage;

class Response extends \Imi\Server\Http\Message\Response
{
    public function __construct()
    {
        parent::__construct(null, null);
    }

    public function send()
    {
        echo $this->body;
    }
}