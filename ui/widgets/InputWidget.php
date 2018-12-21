<?php
use traits\WidgetTrait;
use traits\AddonTrait;
use traits\GridTrait;
use traits\ValidationTrait;

/**
 * InputWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/10/24 16:17
 */
class InputWidget
{
    use WidgetTrait, AddonTrait, GridTrait, ValidationTrait;

    protected $attr = [];
    protected $attrClass = [];

    /**
     * InputWidget constructor.
     * @param null $name
     * @param null $value
     */
    public function __construct($name = null, $value = null)
    {
        $this->init();
        $this->addonInit();
        // 根据参数调用对应设置方法
        $params = compact("name", "value");
        $this->autoCallMethod($params);
    }

    /**
     * 初始化默认值
     */
    protected function init()
    {
        $this->attr = [
            'class' => 'form-control',
            'type' => 'text',
        ];
        $this->groupAttr = [
            'class' => 'input-group',
        ];
        $this->groupAttrClass = [];
        $this->isGroup = false;
        $this->addons = [
            'before' => [],
            'after' => [],
        ];
    }

    /**
     * 设置值
     * @param $data
     * @return $this
     */
    public function value($data, $extra = null)
    {
        $this->attr['value'] = $data;
        return $this;
    }

    /**
     * 设置状态
     * @param $status
     * @return $this
     */
    public function status($status)
    {
        switch ($status) {
            case 'readonly':
            case 'disabled':
                $this->attr[$status] = $status;
                break;
        }
        return $this;
    }

    /**
     * 设置尺寸
     * @param string $size lg, sm
     * @return $this
     */
    public function size($size = '')
    {
        if ($size) {
            $this->attrClass[__FUNCTION__] = "input-{$size}";
        }
        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        // 合并input框样式类名
        $this->joinAttrClass($this->attr['class'], $this->attrClass);
        // 添加验证属性
        $this->combineValidateAttr($this->attr);
        //
        $html = JvnHtml::tag('input', null, $this->attr);
        if ($this->hasAddon) {
            $html = $this->renderWithAddon($html);
        }
        // 渲染col的html
        $html = $this->renderGridCol($html);
        // 重置数据
        $this->init();
        return $html;
    }
}
