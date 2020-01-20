<?php
namespace Imi\Db;

use Imi\RequestContext;
use Imi\Bean\BeanFactory;
use Imi\Pool\PoolManager;
use Imi\Db\Interfaces\IDb;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Query;
use Imi\Main\Helper;
use Imi\App;
use Imi\Db\Query\QueryType;
use Imi\Config;

abstract class Db
{
    /**
     * 获取新的数据库连接实例
     * @param string $poolName 连接池名称
     * @param int $queryType 查询类型
     * @return \Imi\Db\Interfaces\IDb
     */
    public static function getNewInstance($poolName = null, $queryType = QueryType::WRITE): IDb
    {
        return PoolManager::getResource(static::parsePoolName($poolName, $queryType))->getInstance();
    }

    /**
     * 获取数据库连接实例，每个RequestContext中共用一个
     * @param string $poolName 连接池名称
     * @param int $queryType 查询类型
     * @return \Imi\Db\Interfaces\IDb|null
     */
    public static function getInstance($poolName = null, $queryType = QueryType::WRITE): IDb
    {
        return PoolManager::getRequestContextResource(static::parsePoolName($poolName, $queryType))->getInstance();
    }

    /**
     * 释放数据库连接实例
     * @param \Imi\Db\Interfaces\IDb $db
     * @return void
     */
    public static function release($db)
    {
        $resource = RequestContext::get('poolResources.' . spl_object_hash($db));
        if(null !== $resource)
        {
            PoolManager::releaseResource($resource);
        }
    }

    /**
     * 返回一个查询器
     * @param string $poolName
     * @param string $modelClass
     * @param int $queryType
     * @return IQuery
     */
    public static function query($poolName = null, $modelClass = null, $queryType = null): IQuery
    {
        return BeanFactory::newInstance(Query::class, null, $modelClass, $poolName, $queryType);
    }

    /**
     * 处理连接池 名称
     *
     * @param string $poolName
     * @param int $queryType
     * @return string
     */
    private static function parsePoolName($poolName = null, $queryType = QueryType::WRITE)
    {
        if(null === $poolName)
        {
            $poolName = static::getDefaultPoolName($queryType);
        }
        else
        {
            switch($queryType)
            {
                case QueryType::READ:
                    $newPoolName = $poolName . '.slave';
                    if(PoolManager::exists($newPoolName))
                    {
                        $poolName = $newPoolName;
                    }
                    break;
                case QueryType::WRITE:
                default:
                    // 保持原样不做任何处理
            }
        }
        return $poolName;
    }

    /**
     * 获取默认池子名称
     * @param int $queryType 查询类型
     * @return string
     */
    public static function getDefaultPoolName($queryType = QueryType::WRITE)
    {
        $poolName = Config::get('@currentServer.db.defaultPool');
        if(null !== $poolName)
        {
            $poolName = static::parsePoolName($poolName, $queryType);
        }
        return $poolName;
    }

    
    /**
     * 使用回调来使用池子中的资源，无需手动释放
     * 回调有 1 个参数：$instance(操作实例对象)
     * 本方法返回值为回调的返回值
     *
     * @param callable $callable
     * @param string $poolName
     * @param int $queryType
     * @return mixed
     */
    public static function use($callable, $poolName = null, $queryType = QueryType::WRITE)
    {
        return PoolManager::use(static::parsePoolName($poolName, $queryType), function($resource, $db) use($callable) {
            return $callable($db);
        });
    }

    /**
     * 使用回调来使用池子中的资源，无需手动释放，自动开启/提交/回滚事务
     * 回调有 1 个参数：$instance(操作实例对象)
     * 本方法返回值为回调的返回值
     *
     * @param callable $callable
     * @param string $poolName
     * @param int $queryType
     * @return mixed
     */
    public static function transUse($callable, $poolName = null, $queryType = QueryType::WRITE)
    {
        return PoolManager::use(static::parsePoolName($poolName, $queryType), function($resource, IDb $db) use($callable) {
            return static::trans($db, $callable);
        });
    }

    /**
     * 使用回调来使用当前上下文中的资源，无需手动释放，自动开启/提交/回滚事务
     * 回调有 1 个参数：$instance(操作实例对象)
     * 本方法返回值为回调的返回值
     *
     * @param callable $callable
     * @param string $poolName
     * @param int $queryType
     * @return mixed
     */
    public static function transContext($callable, $poolName = null, $queryType = QueryType::WRITE)
    {
        $db = static::getInstance($poolName, $queryType);
        return static::trans($db, $callable);
    }

    /**
     * 事务处理，自动开启/提交/回滚事务
     *
     * @param IDb $db
     * @param callable $callable
     * @return mixed
     */
    public static function trans(IDb $db, $callable)
    {
        try {
            $db->beginTransaction();
            $result = $callable($db); // 调用回调
            $db->commit();
            return $result;
        } catch(\Throwable $th) {
            // 回滚事务
            if($db->inTransaction())
            {
                $db->rollBack();
            }
            throw $th;
        }
    }

}