<?php

/**
 * RadioWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/12/19 15:49
 */
class RadioWidget extends CheckboxWidget
{
    /**
     * RadioWidget constructor.
     * @param null $name
     * @param array $options
     * @param string $layout
     */
    function __construct($name, array $options, $layout)
    {
        parent::__construct($name, $options, $layout);
        // 设置类型
        $this->widgetType = 'radio';
    }
}