<?php
namespace Imi\Model\Relation\Struct;

use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Bean\BeanFactory;
use Imi\Model\ModelManager;
use Imi\Model\Annotation\Relation\JoinTo;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Model\Annotation\Relation\JoinFrom;

trait TLeftAndRight
{
    /**
     * 左侧表字段
     *
     * @var string
     */
    private $leftField;
    
    /**
     * 右侧表字段
     *
     * @var string
     */
    private $rightField;

    /**
     * 右侧模型类
     *
     * @var string
     */
    private $rightModel;

    /**
     * 初始化左右关联
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Model\Annotation\Relation\OneToOne $annotation
     * @return void
     */
    public function initLeftAndRight($className, $propertyName, $annotation)
    {
        if(class_exists($annotation->model))
        {
            $this->rightModel = $annotation->model;
        }
        else
        {
            $this->rightModel = Imi::getClassNamespace($className) . '\\' . $annotation->model;
        }
        
        $joinFrom = AnnotationManager::getPropertyAnnotations($className, $propertyName, JoinFrom::class)[0] ?? null;
        $joinTo = AnnotationManager::getPropertyAnnotations($className, $propertyName, JoinTo::class)[0] ?? null;

        if($joinFrom)
        {
            $this->leftField = $joinFrom->field;
        }
        else
        {
            $this->leftField = $className::__getMeta()->getFirstId();
        }

        if($joinTo)
        {
            $this->rightField = $joinTo->field;
        }
        else
        {
            $this->rightField = Text::toUnderScoreCase(Imi::getClassShortName($className)) . '_id';
        }
    }

    /**
     * Get the value of leftField
     */ 
    public function getLeftField()
    {
        return $this->leftField;
    }

    /**
     * Get the value of rightField
     */ 
    public function getRightField()
    {
        return $this->rightField;
    }

    /**
     * Get 右侧模型类
     *
     * @return  string
     */ 
    public function getRightModel()
    {
        return $this->rightModel;
    }
}