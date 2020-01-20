# 中间件

IMI 框架遵循 PSR-7、PSR-15 标准，使用中间件来实现路由。

中间件可以对整个请求和响应过程进行自定义处理

imi 的路由匹配、执行动作、响应输出，都是依赖中间件实现，必要的时候你甚至可以把 imi 内置实现替换掉

> 注意！最好不要在中间件中使用类属性，可能会造成冲突！

### 定义中间件

实现接口：`Psr\Http\Server\MiddlewareInterface`

方法：`public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface`

先执行其它中间件：`$response = $handler->handle($request);`


```php
use Imi\Bean\Annotation\Bean;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @Bean
 */
class TestMiddleware implements MiddlewareInterface
{
    /**
     * 处理方法
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 前置处理
        
        // 先执行其它中间件
        $response = $handler->handle($request);
        
        // 后置处理
        
        return $response;
    }
}
```

### 全局中间件

```php
return [
    'beans'	=>	[
        // 中间件
        'HttpDispatcher'	=>	[
            'middlewares'	=>	[
                // 中间件
                \Imi\Server\Session\Middleware\HttpSessionMiddleware::class,
            ],
        ],
    ],
];
```

### 局部中间件

#### 注解使用

```php
class Index extends HttpController
{
    /**
     * PHP 原生模版引擎演示
     * 访问：http://127.0.0.1:8080/
     * 
     * @Action
     * @Route(url="/")
     * @View(template="index")
     * 
     * 单个中间件，浏览器 F12 看Response Header：
     * @Middleware(\ImiDemo\HttpDemo\Middlewares\PoweredBy::class)
     * @return void
     */
    public function index()
    {
        return [
            'title'		=>	'hello imi',
            'content'	=>	'imi is very six',
        ];
    }

    /**
     * 无@View注解，不用写代码，也可以渲染模版
     * 访问：http://127.0.0.1:8080/Index/test
     * 
     * @Action
     * 
     * 多个中间件，浏览器 F12 看Response Header：
     * @Middleware({
     * \ImiDemo\HttpDemo\Middlewares\PoweredBy::class,
     * \ImiDemo\HttpDemo\Middlewares\Test::class
     * })
     * 
     * @return void
     */
    public function test()
    {

    }
}
```

如上代码，`index()`方法中的`@Middleware`是设置单个。`test()`方法中的是设置多个中间件。具体请看`imi-demo`项目代码。

#### 配置路由使用

任何类型的路由配置都是加`middlewares`节来实现指定局部中间件。

```php
[
    'route'	=>	[
        'url'	=>	'/callback2',
    ],
    'callback'	=>	new RouteCallable('\Test', 'abc'),
    'middlewares'	=>	[
        \ImiDemo\HttpDemo\Middlewares\PoweredBy::class,
    ],
],
```

#### 中间件分组

服务器 config.php：

```php
return [
    'middleware'    =>  [
        'groups'    =>  [
            // 组名
            'test'  =>  [
                // 中间件列表
                \Imi\Test\HttpServer\ApiServer\Middleware\Middleware4::class,
            ],
        ],
    ],
];
```

使用：`@Middleware("@test")`

### 注入修改核心动作中间件

服务器 config.php:

```php
[
    'beans' =>  [
        'ActionWrapMiddleware'  =>  [
            'actionMiddleware'  =>  '自定义',
        ],
    ],
]
```
