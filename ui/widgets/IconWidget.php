<?php
use traits\WidgetTrait;

/**
 * Icon.php
 * @desc
 * @author: Sven
 * @since: 2018/10/22 15:32
 */
class IconWidget
{
    use WidgetTrait;

    public function __construct($value = null, $attr = [])
    {
        $this->init();
        // 根据参数调用对应设置方法
        $params = compact("value", "attr");
        $this->autoCallMethod($params);
    }

    protected function init()
    {
        $this->attr = [
            'class' => 'glyphicon',
            'aria-hidden' => 'true'
        ];
    }

    public function value($value)
    {
        if ($value) {
            $this->attr['class'] .= " {$value}";
        }
        return $this;
    }

    public function render()
    {
        $html = JvnHtml::tag('span', '', $this->attr);
        $this->init();
        return $html;
    }

}