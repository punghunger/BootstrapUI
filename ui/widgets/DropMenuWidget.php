<?php

/**
 * DropMenuWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/10/23 17:49
 */
class DropMenuWidget extends ButtonWidget
{
    public function __construct($name = null, $config = [])
    {
        parent::__construct($name, $config);
    }

    public function size($size = '')
    {
        parent::dropMenuSize($size);
        return $this;
    }

    public function split($flag = '1')
    {
        if ($flag === true || $flag == 1 || $flag == 'true') {
            $this->hasSplit = true;
        }
        return $this;
    }

    public function dropup($flag = '1')
    {
        if ($flag === true || $flag == 1 || $flag == 'true') {
            $this->dropMenuAttrClass[__FUNCTION__] = "dropup";
        }
        return $this;
    }
}