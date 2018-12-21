<?php

/**
 * AutoCallProperty.php
 * @desc
 * @author: Sven
 * @since: 2018/12/12 16:55
 */
trait AutoCallProperty
{

    /**
     * 设置组件属性
     * @param $name
     * @param $arguments
     * @return $this
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if (isset(static::$propsRule[$name])) {
            if (static::$propsRule[$name] == '') {
                $this->props[$name] = $arguments[0];
            }
            return $this;
        } else {
            throw new \Exception($name . '方法不存在');
        }
    }
}