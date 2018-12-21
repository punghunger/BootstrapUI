<?php
use traits\WidgetTrait;
use traits\AddonTrait;
use traits\GridTrait;
use traits\ValidationTrait;

/**
 * SelectWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/10/25 10:54
 */
class SelectWidget
{
    use WidgetTrait, AddonTrait, GridTrait, ValidationTrait;

    protected $attr = [];
    protected $attrClass = [];
    protected $option = [];
    protected $config = [];
    protected $dataSource = [];
    protected $defaultValue = null;
    protected $ajaxConfig = [];
    protected $isTreeSelect = false;

    /**
     * SelectWidget constructor.
     * @param null $name
     * @param null $value
     * @param array $option
     * @param array $config
     */
    public function __construct($name = null, $value = null, $option = [], $config = [])
    {
        // 初始化数据
        $this->init();
        $this->addonInit();
        $params = [];
        // 获取参数
        if ($value && is_array($value)) {
            // 是设置dataSource的值
            $params = [
                'name' => $name,
                'dataSource' => $value,
                'config' => $option
            ];
        } else if ($value && $option && is_array($option)) {
            $params = compact("name", "value", "option", "config");
        }
        //
        $params && $this->autoCallMethod($params);
    }

    public function init()
    {
        // 初始化默认值
        $this->attr = [
            'class' => 'form-control',
        ];
        $this->dataSource = [];
        $this->config = [
            'allowClear' => true,
            'multiple' => false,
            'placeholder' => ['id' => '-1', 'text' => '请选择'],
        ];
        $this->option = [];
        $this->ajaxConfig = [];
        $this->isTreeSelect = false;
        // 默认id
        $id = 'select-' . mt_rand(10000, 99999);
        $this->id($id);
    }

    /**
     * 尺寸
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

    public function value($data)
    {
        $this->defaultValue = $data;
        return $this;
    }

    public function option($data, $kv = '', $kk = '')
    {
        $args = func_get_args();
        if ($kv && $kk) {
            $data = JvnHtml::listData($data, $kv, $kk);
        }
        $this->option = $data;
        return $this;
    }

    public function multiple($flag = '1')
    {
        if ($flag === true || $flag == 1 || $flag == 'true') {
            $this->attr['multiple'] = "multiple";
        }
        return $this;
    }

    /**
     * 数据
     * @param array $data
     * @param bool $referKey 重新指向字段，默认text
     * @return $this
     */
    public function dataSource($data = [], $referKey = false)
    {
        if ($referKey !== false) {
            $this->referDataKey($data, $referKey);
        }
        $this->dataSource = $data;
        return $this;
    }

    /**
     * 将对应的字段值指定到text
     * @param $data 数据
     * @param $referKey 被指定的字段名
     */
    protected function referDataKey(&$data, $referKey)
    {
        foreach ($data as $key => $item) {
            $data[$key]['text'] = isset($item[$referKey]) ? $item[$referKey] : '';
            if (isset($item['children']) && $item['children']) {
                $this->referDataKey($data[$key]['children'], $referKey);
            }
        }
    }

    public function config($config = [])
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }


    public function ajax($data = [])
    {
        // 标签设置属性
        $this->ajaxConfig = $data;
        return $this;
    }

    public function render()
    {
        // 合并input框样式类名
        $this->joinAttrClass($this->attr['class'], $this->attrClass);
        // 添加验证属性
        $this->combineValidateAttr($this->attr);
        // 生成下拉框html
        $html = JvnHtml::dropDownList('select', $this->defaultValue, $this->option, $this->attr);
        if ($this->hasAddon) {
            $html = $this->renderWithAddon($html);
        }
        // 渲染col的html
        $html = $this->renderGridCol($html);
        // 创建js代码
        $html .= $this->makeJs();
        $this->init();
        return $html;
    }

    protected function makeJs()
    {
        $config = $this->getConfig();
        $method = $this->isTreeSelect ? 'select2comboTree' : 'select2';
        $js = <<<JS
         <script type="text/javascript">
            var {$this->jsVar};
            \$(function () {
                {$this->jsVar} = \$("#{$this->id}").{$method}({$config});
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
        // 数据源
        if ($this->dataSource) {
            $this->config['data'] = $this->dataSource;
        }
        // ajax配置
        if ($this->ajaxConfig) {
            $this->config['ajax'] = $this->ajaxConfig;
        }
        // json格式化
        $config = json_encode($this->config);
        // 将带有Callback的字段转换为可读的js代码
        $config = $this->convertCallback2JsCode($config);
        return $config;
    }

}