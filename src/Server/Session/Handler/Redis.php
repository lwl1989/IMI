<?php
namespace Imi\Server\Session\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Pool\PoolManager;
use Imi\App;

/**
 * @Bean("SessionRedis")
 */
class Redis extends Base
{
    /**
     * Redis连接池名称
     * @var string
     */
    protected $poolName;

    /**
     * Redis中存储的key前缀，可以用于多系统session的分离
     * @var string
     */
    protected $keyPrefix;

    public function __init()
    {
        parent::__init();
        if(null === $this->keyPrefix)
        {
            $this->keyPrefix = 'imi:' . App::getNamespace() . ':';
        }
    }

    /**
     * 销毁session数据
     * @param string $sessionID
     * @return void
     */
    public function destroy($sessionID)
    {
        PoolManager::use($this->poolName, function($resource, \Imi\Redis\RedisHandler $redis) use($sessionID){
            $redis->del($this->getKey($sessionID));
        });
    }

    /**
     * 垃圾回收
     * @param int $maxLifeTime 最大存活时间，单位：秒
     * @return void
     */
    public function gc($maxLifeTime)
    {
        // 用redis数据自动过期，这里什么都不需要做
    }

    /**
     * 读取session
     * @param string $sessionID
     * @return mixed
     */
    public function read($sessionID)
    {
        return PoolManager::use($this->poolName, function($resource, \Imi\Redis\RedisHandler $redis) use($sessionID){
            return $redis->get($this->getKey($sessionID));
        });
    }

    /**
     * 写入session
     * @param string $sessionID
     * @param string $sessionData
     * @return void
     */
    public function write($sessionID, $sessionData)
    {
        PoolManager::use($this->poolName, function($resource, \Imi\Redis\RedisHandler $redis) use($sessionID, $sessionData){
            $redis->set($this->getKey($sessionID), $sessionData, $this->getMaxLifeTime());
        });
    }

    /**
     * 获取在Redis中存储的key
     * @param string $sessionID
     * @return string
     */
    public function getKey($sessionID)
    {
        return $this->keyPrefix . $sessionID;
    }

    /**
     * @return int
     */
    public function getMaxLifeTime(): int
    {
        return $this->maxLifeTime;
    }
}