<?php
namespace Imi\Server\Route\Listener;

use Imi\Config;
use Imi\Main\Helper;
use Imi\ServerManage;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Route\TMiddleware;
use Imi\Server\Route\RouteCallable;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Server\Route\Parser\WSControllerParser;
use Imi\Server\Route\Annotation\WebSocket\WSRoute;
use Imi\Server\Route\Annotation\WebSocket\WSAction;
use Imi\Server\Route\Annotation\WebSocket\WSMiddleware;

/**
 * WebSocket 服务器路由初始化
 * @Listener("IMI.MAIN_SERVER.WORKER.START")
 */
class WSRouteInit implements IEventListener
{
    use TMiddleware;

    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        $this->parseAnnotations($e);
        $this->parseConfigs();
    }

    /**
     * 处理注解路由
     * @return void
     */
    private function parseAnnotations(EventParam $e)
    {
        $controllerParser = WSControllerParser::getInstance();
        foreach(ServerManage::getServers() as $name => $server)
        {
            if(!$server instanceof \Imi\Server\WebSocket\Server)
            {
                continue;
            }
            /** @var \Imi\Server\WebSocket\Route\WSRoute $route*/
            $route = $server->getBean('WSRoute');
            foreach($controllerParser->getByServer($name) as $className => $classItem)
            {
                /** @var \Imi\Server\Route\Annotation\WebSocket\WSController $classAnnotation */
                $classAnnotation = $classItem->getAnnotation();
                // 类中间件
                $classMiddlewares = [];
                foreach(AnnotationManager::getClassAnnotations($className, WSMiddleware::class) ?? [] as $middleware)
                {
                    $classMiddlewares = array_merge($classMiddlewares, $this->getMiddlewares($middleware->middlewares, $name));
                }
                foreach(AnnotationManager::getMethodsAnnotations($className, WSAction::class) as $methodName => $actionAnnotations)
                {
                    /** @var \Imi\Server\Route\Annotation\WebSocket\WSRoute[] $routes */
                    $routes = AnnotationManager::getMethodAnnotations($className, $methodName, WSRoute::class);
                    if(!isset($routes[0]))
                    {
                        throw new \RuntimeException(sprintf('%s->%s method has no route', $className, $methodName));
                    }
                    // 方法中间件
                    $methodMiddlewares = [];
                    foreach(AnnotationManager::getMethodAnnotations($className, $methodName, WSMiddleware::class) ?? [] as $middleware)
                    {
                        $methodMiddlewares = array_merge($methodMiddlewares, $this->getMiddlewares($middleware->middlewares, $name));
                    }
                    // 最终中间件
                    $middlewares = array_values(array_unique(array_merge($classMiddlewares, $methodMiddlewares)));
                    
                    foreach($routes as $routeItem)
                    {
                        $routeItem = clone $routeItem;
                        // 方法上的 @WSRoute 未设置 route，则使用 @WSController 中的
                        if(null === $routeItem->route)
                        {
                            $routeItem->route = $classAnnotation->route;
                        }
                        $route->addRuleAnnotation($routeItem, new RouteCallable($server, $className, $methodName), [
                            'middlewares' => $middlewares,
                            'singleton'   => null === $classAnnotation->singleton ? Config::get('@server.' . $name . '.controller.singleton', false) : $classAnnotation->singleton,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * 处理配置文件路由
     * @return void
     */
    private function parseConfigs()
    {
        foreach(ServerManage::getServers() as $server)
        {
            if(!$server instanceof \Imi\Server\WebSocket\Server)
            {
                continue;
            }
            $route = $server->getBean('WSRoute');
            foreach(Helper::getMain($server->getConfig()['namespace'])->getConfig()['route'] ?? [] as $routeOption)
            {
                $routeAnnotation = new WSRoute($routeOption['route'] ?? []);
                if(isset($routeOption['callback']))
                {
                    $callable = $routeOption['callback'];
                }
                else
                {
                    $callable = new RouteCallable($server, $routeOption['controller'], $routeOption['method']);
                }
                $route->addRuleAnnotation($routeAnnotation, $callable, [
                    'middlewares' => $routeOption['middlewares'],
                ]);
            }
        }
    }
}