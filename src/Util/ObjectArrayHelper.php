<?php
namespace Imi\Util;

/**
 * 对象及数组帮助类
 * 智能识别数组和对象，支持对a.b.c这样的name属性进行操作
 */
abstract class ObjectArrayHelper
{
    /**
     * 获取值
     *
     * @param array|object $object
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function get(&$object, $name, $default = null)
    {
        $names = explode('.', $name);
        $result = &$object;
        foreach ($names as $nameItem)
        {
            if(is_array($result))
            {
                // 数组
                if (isset($result[$nameItem]))
                {
                    $result = &$result[$nameItem];
                }
                else
                {
                    return $default;
                }
            }
            else if(is_object($result))
            {
                // 对象
                if (isset($result->$nameItem))
                {
                    $result = &$result->$nameItem;
                }
                else
                {
                    return $default;
                }
            }
            else
            {
                return $default;
            }
        }
        if (isset($names[0]))
        {
            return $result;
        }
        else
        {
            return $default;
        }
    }

    /**
     * 设置值
     *
     * @param array|object $object
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public static function set(&$object, $name, $value)
    {
        $names = explode('.', $name);
        $lastName = array_pop($names);
        $data = &$object;
        foreach ($names as $nameItem)
        {
            if(is_array($data))
            {
                if (!isset($data[$nameItem]))
                {
                    $data[$nameItem] = [];
                }
                $data = &$data[$nameItem];
            }
            else if(is_object($data))
            {
                if (!isset($data->$nameItem))
                {
                    $data->$nameItem = new stdClass;
                }
                $data = &$data->$nameItem;
            }
        }
        if(is_array($data))
        {
            $data[$lastName] = $value;
        }
        else if(is_object($data))
        {
            $data->$lastName = $value;
        }
    }

    /**
     * 移除值
     *
     * @param array|object $object
     * @param string $name
     * @return void
     */
    public static function remove(&$object, $name)
    {
        $names = explode('.', $name);
        $lastName = array_pop($names);
        $data = &$object;
        foreach ($names as $nameItem)
        {
            if(is_array($data))
            {
                if (!isset($data[$nameItem]))
                {
                    $data[$nameItem] = [];
                }
                $data = &$data[$nameItem];
            }
            else if(is_object($data))
            {
                if (!isset($data->$nameItem))
                {
                    $data->$nameItem = new stdClass;
                }
                $data = &$data->$nameItem;
            }
        }
        if(is_array($data))
        {
            unset($data[$lastName]);
        }
        else if(is_object($data))
        {
            unset($data->$lastName);
        }
    }

    /**
     * 值是否存在
     *
     * @param array|object $object
     * @param string $name
     * @return boolean
     */
    public static function exists($object, $name)
    {
        return null !== static::get($object, $name);
    }

    /**
     * 将第二纬某字段值放入到一个数组中
     * 功能类似array_column，这个方法也支持对象
     *
     * @param array $array
     * @param string $columnName
     * @return array
     */
    public static function column($array, $columnName)
    {
        $result = [];
        foreach($array as $row)
        {
            if(is_object($row))
            {
                $result[] = $row->$columnName;
            }
            else
            {
                $result[] = $row[$columnName];
            }
        }
        return $result;
    }

    /**
     * 过滤属性
     * 
     * $mode只允许取值为：allow/deny
     *
     * @param array|object $object
     * @param array $fields
     * @param string $mode
     * @return void
     */
    public static function filter(&$object, array $fields, $mode = 'allow')
    {
        if('allow' === $mode)
        {
            $unsetKeys = [];
            foreach($object as $field => $value)
            {
                if(!in_array($field, $fields))
                {
                    $unsetKeys[] = $field;
                }
            }
            foreach($unsetKeys as $key)
            {
                static::remove($object, $key);
            }
        }
        else if('deny' === $mode)
        {
            foreach($fields as $field)
            {
                static::remove($object, $field);
            }
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('Unknow mode %s', $mode));
        }
    }
}