<?php
namespace Imi\Test\Component\Model\Base;

use Imi\Model\Model;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;

/**
 * PerformanceBase
 * @Entity
 * @Table(name="tb_performance", id={"id"})
 * @property int $id 
 * @property string $value 
 */
abstract class PerformanceBase extends Model
{
    /**
     * id
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true)
     * @var int
     */
    protected $id;

    /**
     * 获取 id
     *
     * @return int
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * 赋值 id
     * @param int $id id
     * @return static
     */ 
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * value
     * @Column(name="value", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $value;

    /**
     * 获取 value
     *
     * @return string
     */ 
    public function getValue()
    {
        return $this->value;
    }

    /**
     * 赋值 value
     * @param string $value value
     * @return static
     */ 
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

}
