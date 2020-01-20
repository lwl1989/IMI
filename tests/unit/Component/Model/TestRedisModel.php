<?php
namespace Imi\Test\Component\Model;

use Imi\Model\RedisModel;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\RedisEntity;

/**
 * Test
 * @Entity
 * @RedisEntity(key="{id}-{name}")
 */
class TestRedisModel extends RedisModel
{
    /**
     * id
     * @Column(name="id")
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
     * name
     * @Column(name="name")
     * @var string
     */
    protected $name;
 
    /**
     * 获取 name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
 
    /**
     * 赋值 name
     * @param string $name name
     * @return static
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
 
    /**
     * age
     * @Column(name="age")
     * @var string
     */
    protected $age;
 
    /**
     * 获取 age
     *
     * @return string
     */
    public function getAge()
    {
        return $this->age;
    }
 
    /**
     * 赋值 age
     * @param string $age age
     * @return static
     */
    public function setAge($age)
    {
        $this->age = $age;
        return $this;
    }
}
