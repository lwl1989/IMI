<?php

use Imi\Log\LogLevel;
return [
    'configs'    =>    [
    ],
    // bean扫描目录
    'beanScan'    =>    [
        'Imi\Test\HttpServer\SessionTestServer\Controller',
    ],
    'beans'    =>    [
        'SessionManager'    =>    [
            'handlerClass'    =>    \Imi\Server\Session\Handler\File::class,
        ],
        'SessionFile'    =>    [
            'savePath'    =>    dirname(__DIR__, 2) . '/.runtime/.session2',
        ],
        'SessionConfig'    =>    [

        ],
        'SessionCookie'    =>    [
            'lifetime'    =>    86400 * 30,
        ],
        'HttpDispatcher'    =>    [
            'middlewares'    =>    [
                \Imi\Test\HttpServer\Middleware\RequestLogMiddleware::class,
                \Imi\Server\Session\Middleware\HttpSessionMiddleware::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'HtmlView'    =>    [
            'templatePath'    =>    dirname(__DIR__) . '/template/',
            // 支持的模版文件扩展名，优先级按先后顺序
            'fileSuffixs'        =>    [
                'tpl',
                'html',
                'php'
            ],
        ],
        \Imi\Server\Session\Middleware\HttpSessionMiddleware::class => [
            'sessionIdHandler'  =>  function(\Imi\Server\Http\Message\Request $request){
                return $request->getHeaderLine('X-Session-ID');
            },
        ],
    ],
];