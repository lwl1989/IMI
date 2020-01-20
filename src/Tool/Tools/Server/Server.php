<?php
namespace Imi\Tool\Tools\Server;

use Imi\App;
use Imi\Util\Imi;
use Imi\ServerManage;
use Imi\Tool\ArgType;
use Imi\Pool\PoolManager;
use Imi\Cache\CacheManager;
use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Tool;
use Imi\Tool\Annotation\Operation;
use Imi\Tool\Tools\Imi\Imi as ToolImi;

/**
 * @Tool("server")
 */
class Server
{
    /**
     * 开启服务
     * 
     * @Operation(name="start", co=false)
     * @Arg(name="name", type=ArgType::STRING, required=false, comments="要启动的服务器名")
     * @Arg(name="workerNum", type=ArgType::INT, required=false, comments="工作进程数量")
     * 
     * @return void
     */
    public function start($name, $workerNum)
    {
        PoolManager::clearPools();
        CacheManager::clearPools();
        if(null === $name)
        {
            App::createServers();
            ServerManage::getServer('main')->getSwooleServer()->start();
        }
        else
        {
            $server = App::createCoServer($name, $workerNum);
            $server->run();
        }
    }

    /**
     * 停止服务
     * 
     * @Operation("stop")
     * 
     * @return void
     */
    public function stop()
    {
        $result = Imi::stopServer();
        echo $result['cmd'], PHP_EOL;
    }

    /**
     * 重新加载服务
     * 
     * 重启 Worker 进程，不会导致连接断开，可以让项目文件更改生效
     * 
     * @Operation("reload")
     * @Arg(name="runtime", type=ArgType::BOOL, required=false, default=false, comments="是否更新运行时缓存")
     * 
     * @return void
     */
    public function reload($runtime)
    {
        if($runtime)
        {
            $imi = new ToolImi;
            echo 'Building runtime...', PHP_EOL;
            $time = microtime(true);
            $imi->buildRuntime('', null, false);
            $useTime = microtime(true) - $time;
            echo 'Runtime build complete! ', $useTime, 's', PHP_EOL;
        }
        $result = Imi::reloadServer();
        echo $result['cmd'], PHP_EOL;
    }

}