<?php
namespace Imi\NoSwoole;

use Imi\App;
use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Server\Route\RouteCallable;
use Imi\NoSwoole\HttpMessage\Request;
use Imi\NoSwoole\HttpMessage\Response;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\Action;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Annotation\Middleware;
use Imi\Server\Route\Parser\ControllerParser;


abstract class AppBaseMain extends \Imi\Main\AppBaseMain
{
    public function __construct(string $moduleName)
    {
        parent::__construct($moduleName);
        Event::on('IMI.INITED', \Closure::fromCallable([$this, 'onImiInit']));
    }

    private function onImiInit($e)
    {
        RequestContext::create();
        $this->parseRoute();
        // 中间件
        $dispatcher = App::getBean('HttpDispatcher');
        $request = new Request('/');
        $response = new Response();
        RequestContext::set('request', $request);
        RequestContext::set('response', $response);
        $dispatcher->dispatch($request, $response);
        RequestContext::destroy();
    }

    /**
     * 处理注解路由
     * @return void
     */
    private function parseRoute()
    {
        $route = App::getBean('HttpRoute');
        foreach(AnnotationManager::getAnnotationPoints(Controller::class, 'Class') as $classItem)
        {
            $className = $classItem['class'];
            $classAnnotation = $classItem['annotation'];
            // 类中间件
            $classMiddlewares = [];
            foreach(AnnotationManager::getClassAnnotations($className, Middleware::class) ?? [] as $middleware)
            {
                if(is_array($middleware->middlewares))
                {
                    $classMiddlewares = array_merge($classMiddlewares, $middleware->middlewares);
                }
                else
                {
                    $classMiddlewares[] = $middleware->middlewares;
                }
            }
            foreach(AnnotationManager::getMethodsAnnotations($className, Action::class) as $methodName => $actionAnnotations)
            {
                $routeAnnotations = AnnotationManager::getMethodAnnotations($className, $methodName, Route::class);
                if(isset($routeAnnotations[0]))
                {
                    $routes = $routeAnnotations;
                }
                else
                {
                    $routes = [
                        new Route([
                            'url' => $methodName,
                        ])
                    ];
                }
                // 方法中间件
                $methodMiddlewares = [];
                foreach(AnnotationManager::getMethodAnnotations($className, $methodName, Middleware::class) ?? [] as $middleware)
                {
                    if(is_array($middleware->middlewares))
                    {
                        $methodMiddlewares = array_merge($methodMiddlewares, $middleware->middlewares);
                    }
                    else
                    {
                        $methodMiddlewares[] = $middleware->middlewares;
                    }
                }
                // 最终中间件
                $middlewares = array_values(array_unique(array_merge($classMiddlewares, $methodMiddlewares)));
                
                foreach($routes as $routeItem)
                {
                    if(null === $routeItem->url)
                    {
                        $routeItem->url = $methodName;
                    }
                    if((!isset($routeItem->url[0]) || '/' !== $routeItem->url[0]) && '' != $classAnnotation->prefix)
                    {
                        $routeItem->url = $classAnnotation->prefix . $routeItem->url;
                    }
                    $route->addRuleAnnotation($routeItem, new RouteCallable($className, $methodName), [
                        'middlewares'   => $middlewares,
                        'wsConfig'      => null,
                    ]);
                }
            }
        }

    }
}