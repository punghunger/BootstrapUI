<?php

/**
 * treeSelectWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/11/2 16:32
 */
class TreeSelectWidget extends SelectWidget
{
    public function __construct()
    {
        parent::__construct();
        $this->isTreeSelect = true;
    }

    public function treeData()
    {

    }
}