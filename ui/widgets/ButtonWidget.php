<?php
use traits\WidgetTrait;
use traits\GridTrait;

/**
 * ButtonWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/10/22 15:25
 */
class ButtonWidget
{
    use WidgetTrait, GridTrait;

    protected $type;
    protected $basedTag = '';
    protected $attr = [];
    protected $attrClass = [];
    protected $dropMenuAttr = [];
    protected $dropMenuAttrClass = [];
    protected $hashMenu = false;
    protected $menuHtml = '';
    protected $splitHtml = '';
    protected $caret = '<span class="caret"></span>';
    protected $hasSplit = false;
    protected $icon = '';
    protected $iconAfter = '';
    protected $config = [];

    /**
     * ButtonWidget constructor.
     * @param null $name
     * @param array $config
     */
    public function __construct($name = null, $config = [])
    {
        $this->init();
        if ($name && is_string($name)) {
            $this->name($name);
        } else if ($name && is_array($name)) {
            $this->config = $name;
        } else if (is_array($config) && $config) {
            $this->config = $config;
        }
        // 解析配置数据
        $this->parseConfig();
    }

    /**
     * 初始化数据
     */
    public function init()
    {
        $this->attr = [
            'class' => 'btn',
            'type' => 'button'
        ];
        $this->dropMenuAttr = [
            'class' => 'btn-group',
            'role' => 'group',
        ];
        $this->basedTag = 'button';
        $this->attrClass = [
            'theme' => 'btn-default'
        ];
        // 名称
        $this->name = null;
        // 下拉菜单
        $this->hashMenu = false;
        $this->menuHtml = '';
        // 分裂样式
        $this->hasSplit = false;
        $this->splitHtml = '';
        // 图标
        $this->icon = '';
        $this->iconAfter = '';
        //
        $this->config = [];
    }

    /**
     * 设置按钮名称
     * @param $name
     * @return $this
     */
    public function name($name)
    {
        // input 标签设置属性value的值
        if ($this->basedTag == 'input') {
            $this->attr['value'] = $name;
        } else {
            $this->name = $name;
        }
        return $this;
    }

    /**
     * 返回是否有下拉菜单
     * @return bool
     */
    public function hasDropMenu()
    {
        return $this->hashMenu;
    }

    /**
     * 下拉菜单栏目
     * @param $menus
     * @return $this
     */
    public function dropMenu($menus)
    {
        if ($menus) {
            $this->hashMenu = true;
            $itemHtml = '';
            foreach ($menus as $item) {
                switch ($item['type']) {
                    case 'ele':
                        $name = JvnHtml::tag('a', $item['name'], ['href' => '#']);
                        $attr = isset($item['attr']) ? $item['attr'] : [];
                        break;
                    case 'header':
                        $name = $item['name'];
                        $attr = ['class' => 'dropdown-header'];
                        break;
                    case 'divider':
                        $name = '';
                        $attr = [
                            'class' => 'divider',
                            'role' => 'separator'
                        ];
                        break;
                }
                $itemHtml .= JvnHtml::tag('li', $name, $attr);
            }
            $this->menuHtml = JvnHtml::tag('ul', $itemHtml, ['class' => 'dropdown-menu']);
        }
        return $this;
    }

    /**
     * 下拉菜单尺寸
     * @param string $size
     * @return $this
     */
    public function dropMenuSize($size = '')
    {
        if ($size) {
            $this->dropMenuAttrClass[__FUNCTION__] = "btn-group-{$size}";
        }
        return $this;
    }

    /**
     * 按钮尺寸
     * @param string $size 'lg', 'sm' and 'xs'
     */
    public function size($size = '')
    {
        if ($size) {
            $this->attrClass[__FUNCTION__] = "btn-{$size}";
        }
        return $this;
    }

    /**
     * 设置按钮名字的前置图标
     * @param string $icon
     * @return $this
     */
    public function icon($icon = '', $attr = [])
    {
        if ($icon) {
            $this->icon = Bootstrap::icon($icon, $attr)->render();
        }
        return $this;
    }

    /**
     * 设置按钮名字的后置图标
     * @param string $icon
     * @return $this
     */
    public function iconAfter($icon = '', $attr = [])
    {
        if ($icon) {
            $this->iconAfter = Bootstrap::icon($icon, $attr)->render();
        }
        return $this;
    }

    /**
     * 按钮样式
     * @param string $color default, primary, success, info, warning, danger, link
     */
    public function theme($theme = 'default')
    {
        if ($theme) {
            $this->attrClass[__FUNCTION__] = "btn-{$theme}";
        }
        return $this;
    }

    /**
     * 设置状态
     * @param $status   active：激活状态，disable：禁用状态
     * @return $this
     */
    public function status($status)
    {
        switch ($status) {
            case 'active':
                $this->attrClass[__FUNCTION__] = "active";
                break;
            case 'disable':
                $this->attr['disabled'] = "disabled";
                break;
            default:
                break;
        }
        return $this;
    }

    /**
     * 设置为块级元素
     * @param string $flag
     * @return $this
     */
    public function block($flag = '1')
    {
        if ($flag === true || $flag == 1 || $flag == 'true') {
            $this->attrClass[__FUNCTION__] = "btn-block";
        }
        return $this;
    }

    /**
     * 设置生成按钮的标签
     * @param string $type 标签type的值，当$tag为button、input时设置
     * @param $tag  标签名称， a、button、input
     * @return $this
     */
    public function type($type = 'button', $tag = 'button')
    {
        $this->basedTag = $tag;
        if ($tag == 'a') {
            $this->attr['role'] = 'button';
            $this->attrClass['theme'] = 'btn-link';
        } else if ($tag == 'input') {
            $this->attr['value'] = $this->name;
        }
        if ($type) {
            $this->attr['type'] = $type;
        }
        return $this;
    }

    /**
     * 生成html内容
     * @return string
     */
    public function render()
    {
        // 解析配置数据
//        $this->parseConfig();
        // 生成html
        if ($this->hashMenu) {
            $html = $this->dropMenuRender();
        } else {
            $html = $this->buttonRender();
        }
        // 渲染col的html
        $html = $this->renderGridCol($html);
        // 重置数据
        $this->init();
        return $html;
    }

    /**
     * 生成按钮html
     * @return string
     */
    protected function buttonRender()
    {
        // 不是使用input标签，设置图标显示
        if ($this->basedTag != 'input') {
            // 拼接图标
            $this->icon && $this->name = $this->icon . ' ' . $this->name;
            // 拼接后置图标
            $this->iconAfter && $this->name .= ' ' . $this->iconAfter;
        }
        // 合并按钮样式类名
        $this->joinAttrClass($this->attr['class'], $this->attrClass);
        // 渲染按钮html
        $html = JvnHtml::tag($this->basedTag, $this->name, $this->attr);
        return $html;
    }

    /**
     * 生成单按钮下拉菜单html
     * @return string
     */
    protected function dropMenuRender()
    {
        // 下拉菜单加上一个箭头
        if ($this->hasSplit) {
            //
            $this->joinAttrClass($this->attr['class'], $this->attrClass);
            $attr = $this->attr;
            $attr['class'] .= " dropdown-toggle";
            $attr['data-toggle'] = "dropdown";
            // 渲染按钮html
            $this->splitHtml = JvnHtml::tag('button', $this->caret, $attr);
        } else {
            $this->name .= ' ' . $this->caret;
            // 下拉菜单相关像是及属性
            $this->attrClass[__FUNCTION__] = "dropdown-toggle";
            $this->attr['data-toggle'] = "dropdown";
        }
        // 渲染按钮html
        $btnHtml = $this->buttonRender();
        // 合并下拉菜单div样式类名
        $this->joinAttrClass($this->dropMenuAttr['class'], $this->dropMenuAttrClass);
        // 生成html代码
        $menuContent = $btnHtml . $this->splitHtml . $this->menuHtml;
        $html = JvnHtml::tag('div', $menuContent, $this->dropMenuAttr);
        return $html;
    }

    /**
     * 解析配置数据，包含按钮名称、属性相关数据
     * @return bool
     */
    protected function parseConfig()
    {
        if (!$this->config) {
            return false;
        }
        $akeys = [
            'name', 'size', 'type', 'theme', 'block', 'status',
            'split', 'icon', 'iconAfter', 'dropMenu', 'attr'
        ];
        $this->autoCallMethod($this->config);
        return true;
    }
}