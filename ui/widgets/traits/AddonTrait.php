<?php
namespace traits;
/**
 * AddonTrait.php
 * @desc
 * @author: Sven
 * @since: 2018/10/26 15:20
 */
trait AddonTrait
{
    protected $addonAttr = [];
    protected $addonAttrClass = [];
    protected $hasAddon = false;
    /**
     * 额外元素，分前before，后after
     * @var array
     */
    protected $addons = [];
    protected $beforeAddon = null;
    protected $afterAddon = null;

    /**
     * 初始化数据
     */
    public function addonInit()
    {
        $this->addonAttr = [
            'class' => 'input-group',
        ];
        $this->addonAttrClass = [];
        $this->hasAddon = false;
        $this->addons = [
            'before' => [],
            'after' => [],
        ];
    }

    /**
     * 设置前额外元素
     * @param null $items 字符串或者对象
     * @return $this
     */
    public function addonBefore($items = null)
    {
        if ($items) {
            $this->hasAddon = true;
            $this->beforeAddon = $items;
        }
        return $this;
    }

    /**
     * 设置后额外元素
     * @param null $items 字符串或者对象
     * @return $this
     */
    public function addonAfter($items = null)
    {
        if ($items) {
            $this->hasAddon = true;
            $this->afterAddon = $items;
        }
        return $this;
    }

    /**
     * 设置前额外元素，可添加多个addon，该方法废弃
     * @param $type 类型, text:普通文本, icon:图标, btn:按钮, dropMenu:下拉菜单
     * @param null $addon 额外元素内容
     * @return $this
     */
    public function addonsBefore($type, $addon = null)
    {
        if ($addon) {
            $this->hasAddon = true;
            $this->addAddon('before', $type, $addon);
        }
        return $this;
    }

    /**
     * 设置后额外元素，该方法废弃
     * @param $type 类型, text:普通文本, icon:图标, btn:按钮, dropMenu:下拉菜单
     * @param null $addon 额外元素内容
     * @return $this
     */
    public function addonsAfter($type, $addon = null)
    {
        if ($addon) {
            $this->hasAddon = true;
            $this->addAddon('after', $type, $addon);
        }
        return $this;
    }

    /**
     * 设置input-group容器的属性
     * @param array $attr
     * @return $this
     */
    public function inputGroupAttr($attr = [])
    {
        if (is_array($attr)) {
            // 设置input-group属性
            $this->addonAttr = $this->combineAttr($this->addonAttr, $attr);
        }
        return $this;
    }

    /**
     * 输出内容带额外元素
     * @param $input
     * @return type
     */
    protected function renderWithAddon($input)
    {
        // 获取额外元素内容
        $content = $this->getAddonHtml();
        $content .= $input;
        $content .= $this->getAddonHtml('after');
        // 设置尺寸
        $this->setAddonSize();
        // 拼接className
        $this->joinAttrClass($this->addonAttr['class'], $this->addonAttrClass);
        // 生成html
        $html = \JvnHtml::tag('div', $content, $this->addonAttr);
        // 初始化数据
        $this->addonInit();
        // 返回
        return $html;
    }

    /**
     * 根据设置的输入框尺寸，来设置附加额外元素后的容器尺寸
     * 一般会使用输入框对象中$attrClass属性的size元素的值
     * @return bool
     */
    protected function setAddonSize()
    {
        $size = $this->attrClass['size']??'';
        if ($size) {
            $this->addonAttrClass['size'] = str_replace('input-', 'input-group-', $size);
            unset($this->attrClass['size']);
        }
        return true;
    }

    /**
     * 添加额外元素
     * @param $post
     * @param $type
     * @param $addon
     * @return bool
     */
    protected function addAddon($post, $type, $addon)
    {
        $this->addons[$post][$type][] = $addon;
        return true;
    }

    /**
     * 获取额外的元素，输入框前面或者后面
     * @return array
     */
    protected function getAddonHtml($locate = 'before')
    {

        $html = '';
        $addonName = "{$locate}Addon";
        $addon = $this->$addonName;
        if (!$addon) {
            return $html;
        }
        // 设置标签，属于按钮组类型的元素，使用div标签，普通文本使用span
        $tag = 'span';
        // 样式类名设置
        $className = 'input-group-addon';
        // 和按钮组类型比较，判断是否有属于按钮组类型的元素
        if (is_string($addon)) {
            $html = $addon;
        } else if (is_object($addon)) {
            if ($addon instanceof \ButtonWidget || $addon instanceof \DropMenuWidget) {
                $tag = 'div';
                $className = 'input-group-btn';
            }
            $html = $addon->render();
            // 是下拉菜单按钮，去除按钮组外层div标签
            if ($addon instanceof \DropMenuWidget) {
                $html = $this->removeGroupWrapper($html);
            }
        }
        // 生成额外元素的html
        $html = \JvnHtml::tag($tag, $html, ['class' => $className]);
        return $html;
    }

    /**
     * 获取额外的元素，输入框前面或者后面，该方法废弃
     * @return array
     */
    protected function getAddonsHtml()
    {
        $result = ['before' => '', 'after' => ''];
        // 属于按钮组的元素类型，用于生成对应的标签内容
        $btnTypes = ['btn', 'dropMenu'];
        // 循环额外元素
        foreach ($this->addons as $key => $addons) {
            if (!$addons) {
                continue;
            }
            $addon = '';
            // 和按钮组类型比较，判断是否有属于按钮组类型的元素
            $intsRes = array_intersect($btnTypes, array_keys($addons));
            // 设置标签，属于按钮组类型的元素，使用div标签，普通文本使用span
            $tag = $intsRes ? 'div' : 'span';
            // 样式类名设置
            $className = $intsRes ? 'input-group-btn' : 'input-group-addon';
            // 按元素类型循环元素内容
            foreach ($addons as $type => $item) {
                // 拼接元素
                $joinItem = implode('', $item);
                // 是下拉菜单按钮，去除按钮组外层div标签
                if ($type == 'dropMenu') {
                    $joinItem = $this->removeGroupWrapper($joinItem);
                }
                $addon .= $joinItem;
            }
            // 生成额外元素的html
            $html = \JvnHtml::tag($tag, $addon, ['class' => $className]);
            // 前和后分开存储
            $result[$key] = $html;
        }
        return $result;
    }

    /**
     * 去除按钮组外层div标签
     * @param $data
     * @return mixed
     */
    protected function removeGroupWrapper($data)
    {
        $pattern = [
            '/^<div class="col-.*?".*?>(.*?)<\/div>$/', // 若设置了col，则要去除
            '/<div class="btn-group.*?".*?>(.*?)<\/div>/',
        ];
        $replacement = ['${1}', '${1}'];
        // 正则替换
        return preg_replace($pattern, $replacement, $data);
    }
}