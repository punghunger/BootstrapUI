<?php
use traits\WidgetTrait;
use traits\GridTrait;
use traits\ValidationTrait;
use traits\TemplateTrait;

/**
 * CheckboxWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/12/19 14:08
 */
class CheckboxWidget
{
    use WidgetTrait, GridTrait, ValidationTrait, TemplateTrait;

    protected $attr = [];
    protected $attrClass = [];
    protected $options = [];
    protected $layout = '';
    protected $skin = 'flat';
    protected $color = 'blue';
    protected $config = [];
    /**
     * 内容模板
     * @var array
     */
    protected $templates = [
        'block' => '<div{{attrs}}><label>{{tpl:input}} {{tpl:label}}</label></div>',
        'inline' => '<label{{attrs}}>{{tpl:input}} {{tpl:label}}</label>',
        'label' => '<span class="label-text">{{content}}</span>',
        'input' => '{{content}}'
    ];
    protected $widgetType = 'checkbox';


    /**
     * CheckboxWidget constructor.
     * @param array $options
     */
    public function __construct($name = null, $options = [], $layout = 'block')
    {
        // 设置名称
        $name && $this->name($name);
        // 选择项
        is_array($options) && $this->options = $options;
        // 排列
        $this->layout = $layout;
        // 设置显示模板
        $this->setTemplates($this->templates);
        //
        $id = 'icheck-' . mt_rand(10000, 99999);
        $this->id($id, false);
    }

    public function theme($skin, $color)
    {

    }

    public function render()
    {
        // 生成多选/单选html
        $html = '';
        foreach ($this->options as $key => $label) {
            $html .= $this->getCheckbox($label, $key);
        }
        // 渲染col的html
        $html = $this->renderGridCol($html);
        // 创建js代码
        $html .= $this->makeJs();
        // 返回
        return $html;
    }

    /**
     * 获取多选/单选内容
     * @param $label
     * @param $value
     * @return mixed
     */
    public function getCheckbox($label, $value)
    {
        static $setValid;
        // 设置属性
        $attr = [
            'type' => $this->widgetType,
            'name' => $this->name,
            'value' => $value,
            'class' => $this->id
        ];
        // 只在第一个选项设置验证属性
        if (is_null($setValid)) {
            // 添加验证属性
            $this->combineValidateAttr($attr);
            // 更新状态
            $setValid = true;
        }
        // 选中值设置
        if ($this->value &&
            (
                ($this->widgetType == 'radio' && $value === $this->value) ||
                ($this->widgetType == 'checkbox' && in_array($value, $this->value))
            )
        ) {
            $attr['checked'] = 'checked';
        }
        // 多选/单选内容
        $input = JvnHtml::tag('input', null, $attr);
        // 模板数据
        $tplData = [
            'input' => [
                'content' => $input,
            ],
            'label' => [
                'content' => $label,
            ],
            'block' => [
                'attrs' => [
                    'class' => $this->widgetType
                ]
            ],
            'inline' => [
                'attrs' => [
                    'class' => "{$this->widgetType}-inline"
                ]
            ]
        ];
        $html = $this->parseTemplate($this->layout, $tplData);
        return $html;
    }

    protected function makeJs()
    {
        $config = $this->getConfig();
        $js = <<<JS
         <script type="text/javascript">
            var {$this->jsVar};
            \$(function () {
                {$this->jsVar} = \$(".{$this->id}").iCheck({$config});
            });
         </script>
JS;
        return $js;

    }

    /**
     * 获取配置
     * @return mixed|string
     */
    protected function getConfig()
    {
        $this->config = [
            'checkboxClass' => "icheckbox_{$this->skin}-{$this->color}",
            'radioClass' => "iradio_{$this->skin}-{$this->color}",
        ];
        // json格式化
        $config = json_encode($this->config);
        // 将带有Callback的字段转换为可读的js代码
        $config = $this->convertCallback2JsCode($config);
        return $config;
    }
}