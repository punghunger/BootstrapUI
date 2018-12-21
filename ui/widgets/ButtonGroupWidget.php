<?php
use traits\WidgetTrait;

/**
 * ButtonGroupWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/10/22 18:10
 */
class ButtonGroupWidget
{
    use WidgetTrait;

    protected $elements = [];
    protected $attr = [];
    protected $attrClass = [];
    protected $layout = 'btn-group';

    public function __construct($elements = [])
    {
        $this->init();
        // 设置按钮组包含的按钮元素
        if (is_array($elements)) {
            $this->elements = $elements;
        }
    }

    public function init()
    {
        $this->attr = [
            'class' => '',
            'role' => 'group',
        ];
        $this->attrClass = [
            'layout' => 'btn-group'
        ];
    }

    public function size($size = '')
    {
        if ($size) {
            $this->attrClass[__FUNCTION__] = "btn-group-{$size}";
        }
        return $this;
    }

    public function vertical()
    {
        $this->attrClass['layout'] = "btn-group-vertical";
        return $this;
    }

    public function add($element)
    {
        if ($element) {
            $this->elements[] = $element;
        }
        return $this;
    }

    public function render()
    {
        $elementHtml = $this->getGroupElement();
        // 合并样式
        $this->joinAttrClass($this->attr['class'], $this->attrClass);
        // 生成html
        $html = JvnHtml::tag('div', $elementHtml, $this->attr);
        $this->init();
        return $html;
    }

    protected function getGroupElement($elements = [])
    {
        $html = '';
        if ($elements) {
            $this->elements = $elements;
        }
        $size = $this->attrClass['size']??'';
        $size = str_replace('btn-group-', '', $size);
        foreach ($this->elements as $item) {
            // 数据类型判断
            if (is_object($item)) {
                // 对象形式
                if ($item->hasDropMenu() && $size) {
                    // 设置下拉菜单按钮尺寸
                    $item->dropMenuSize($size);
                }
                $html .= $item->render();
            } else if (is_array($item)) {
                // 数组形式
                // 实例化按钮对象
                $button = new ButtonWidget($item);
                if (isset($item['dropMenu']) && $size) {
                    // 设置下拉菜单按钮尺寸
                    $button->dropMenuSize($size);
                }
                $html .= $button->render();
            } else if (is_string($item)) {
                $html .= $item;
            }
        }
        return $html;
    }
}