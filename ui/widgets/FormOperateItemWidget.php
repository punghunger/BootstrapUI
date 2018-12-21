<?php

/**
 * FormOperateItemWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/12/18 10:39
 */
class FormOperateItemWidget extends FormItemWidget
{

    protected $control;

    public function __construct($control)
    {
        $this->control = $control;
        // 设置显示模板
        $this->setTemplates($this->templates);
    }

    function getControlHtml()
    {
        $html = '';
        if (is_object($this->control)) {
            $html = $this->control->render();
        } else if (is_array($this->control)) {
            foreach ($this->control as $control) {
                if (is_object($control)) {
                    $html .= $control->render();
                }
            }
        }
        return $html;
    }

    function getItemHtml()
    {
        $tplData = [
            'control' => [
                'attrs' => [],
                'content' => $this->getControlHtml()
            ]
        ];
        // horizontal'|'vertical'|'inline'
        if ($this->layout == 'horizontal' && !$this->hasFormRow) {
            $tplData['control']['attrs']['class'] = 'col-sm-offset-2 col-sm-22';
        }
        return $this->parseTemplate('formOperateItem', $tplData);
    }

    public function render()
    {
        $html = $this->getItemHtml();
        // 渲染col的html
        $html = $this->renderGridCol($html);
        return $html;

    }
}