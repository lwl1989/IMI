<?php
namespace Imi\Db\Pool;

use Imi\Db\Interfaces\IDb;
use Imi\Pool\BasePoolResource;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Db\Statement\StatementManager;

/**
 * 数据库连接池的资源
 */
class DbResource extends BasePoolResource
{
    /**
     * db对象
     * @var IDb
     */
    private $db;

    public function __construct(\Imi\Pool\Interfaces\IPool $pool, IDb $db)
    {
        parent::__construct($pool);
        $this->db = $db;
    }

    /**
     * 打开
     * @return boolean
     */
    public function open()
    {
        $this->db->open();
        return $this->db->isConnected();
    }

    /**
     * 关闭
     * @return void
     */
    public function close()
    {
        $this->db->close();
    }

    /**
     * 获取对象实例
     * @return mixed
     */
    public function getInstance()
    {
        return $this->db;
    }

    /**
     * 重置资源，当资源被使用后重置一些默认的设置
     * @return void
     */
    public function reset()
    {
        // 如果在事务中，则回滚
        if($this->db->inTransaction())
        {
            $this->db->rollBack();
        }
    }
    
    /**
     * 检查资源是否可用
     * @return bool
     */
    public function checkState(): bool
    {
        return $this->db->isConnected();
    }
}