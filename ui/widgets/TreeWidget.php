<?php
use traits\WidgetTrait;

/**
 * TreeWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/11/2 17:32
 */
class TreeWidget
{
    use WidgetTrait;

    protected $treeData;
    protected $setting;
    protected $config;
    protected $id;
    protected $attr = [];

    /**
     *
     */
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->id = '';
        $this->attr = [
            'class' => 'ztree'
        ];
        $this->config = [];
        $this->setting = [];
    }

    public function id($data)
    {
        $this->id = $data;
        $this->attr['id'] = $data;
        return $this;
    }

    public function config($data)
    {
        $this->config = $data;
        $this->parseConfig($data);
        return $this;
    }

    private function parseConfig($config)
    {
        // treeNodes 数据
        if (isset($config['treeData']) && $config['treeData']) {
            $this->treeData = $config['treeData'];
            unset($config['treeData']);
        }
        // 使用简单数据格式
        if (isset($config['simpleData']) && $config['simpleData'] === true) {
            $this->setting['data']['simpleData'] = [
                'enable' => true
            ];
        }
        // 节点前添加 Checkbox 复选框，并且判断是否关联选择
        if (isset($config['checkable']) && $config['checkable'] === true) {
            $ckstri = $config['checkStrictly']??false;
            $this->mergeSettingData('check', [
                'enable' => true,
                'chkboxType' => $ckstri ? ['Y' => '', 'N' => ''] : ['Y' => 'ps', 'N' => 'ps']
            ]);
        }
        // 是否可多选
        if (isset($config['multiple']) && $config['multiple'] === true) {
            $this->mergeSettingData('view', [
                'selectedMulti' => true
            ]);
        }
        // 设置节点可拖拽
        if (isset($config['draggable']) && $config['draggable'] === true) {
            $this->mergeSettingData('edit', [
                'enable' => true,
                'showRemoveBtn' => false,
                'showRenameBtn' => false
            ]);
        }
        // 是否展示连接线
        if (isset($config['showLine'])) {
            $this->mergeSettingData('view', [
                'showLine' => $config['showLine']
            ]);
        }
        // 是否展示图标
        if (isset($config['showIcon'])) {
            $this->mergeSettingData('view', [
                'showIcon' => $config['showIcon']
            ]);
        }
        // 异步加载设置
        if (isset($config['url']) && $config['url']) {
            $async = [
                'enable' => true,
                'url' => $config['url'],
                'autoParam' => $config['urlParam']??[],
                'otherParam' => $config['urlExtraParam']??[],
                'dataFilterCallback' => $config['dataFilter']??null,
            ];
            $this->mergeSettingData('async', $async);
        }

        // 设置触发事件
        $callback = [];
        foreach ($config as $key => $value) {
            if (strpos($key, 'Callback') !== false) {
                $callback[$key] = $value;
            }
        }
        $this->callback($callback);
    }

    public function treeData($data)
    {
        $this->treeData = $data;
        return $this;
    }

    public function async($data)
    {
        $this->mergeSettingData('async', $data);
        return $this;
    }

    public function callback($data)
    {
        $this->mergeSettingData('callback', $data);
        return $this;
    }

    public function check($data)
    {
        $this->mergeSettingData('check', $data);
        return $this;
    }

    public function data($data)
    {
        $this->mergeSettingData('data', $data);
        return $this;
    }

    private function mergeSettingData($name, $value)
    {
        $defData = $this->setting[$name]??[];
        $this->setting[$name] = array_merge($defData, $value);
        return $this;
    }

    public function edit($data)
    {
        $this->mergeSettingData('edit', $data);
        return $this;
    }

    public function view($data)
    {
        $this->mergeSettingData('view', $data);
        return $this;
    }

    public function render()
    {
        // 设置id
        $this->id || $this->id(mt_rand(10000, 99999));
        // 生成html相关内容
        $html = $this->getSearchWrapper();
        $html .= JvnHtml::tag('ul', null, $this->attr);
        $html .= $this->makeJSCode();
        $treeBoxAttr = [
            'class' => 'jvn-ztree-wrapper',
            'id' => "jvn-ztree-{$this->id}"
        ];
        $treeBox = JvnHtml::tag('div', $html, $treeBoxAttr);
        return $treeBox;
    }

    protected function getSearchWrapper()
    {
        $searchable = $this->config['searchable']??false;
        $html = '';
        if ($searchable) {
            $html = <<<HTML
            <div class="form-group jvn-ztree-search">
                <input type="text" id="input-search-{$this->id}" class="form-control" placeholder="Search">
                <span class="glyphicon glyphicon-search jvn-inner-glyphicon" aria-hidden="true"></span>
            </div>
HTML;
        }
        return $html;
    }

    protected function makeJSCode()
    {
        // 配置
        $zSetting = json_encode($this->setting);
        $zSetting = $this->convertCallback2JsCode($zSetting);
        // 数据
        $zNodes = json_encode($this->treeData);
        // 对象名称
        $varName = "zTreeObj_{$this->id}";
        //
        $jsCode = '';
        $checkable = $this->config['checkable']??false;
        $checkedKeys = $this->config['checkedKeys']??[];
        $selectedKeys = $this->config['selectedKeys']??[];
        $expandedKeys = $this->config['expandedKeys']??[];
        $expandParent = $this->config['expandParent']??false;
        $disabledKeys = $this->config['disabledKeys']??[];
        $searchable = $this->config['searchable']??false;
        // check选中
        if ($checkable && $checkedKeys) {
            $checkedKeys = json_encode($checkedKeys);
            $jsCode .= "treeWidget.checkNodes({$varName}, {$checkedKeys});";
        }
        // select选中
        if ($selectedKeys) {
            $selectedKeys = json_encode($selectedKeys);
            $jsCode .= "treeWidget.selectNodes({$varName}, {$selectedKeys});";
        }
        // 展开节点
        if ($expandedKeys) {
            $expandedKeys = json_encode($expandedKeys);
            $jsCode .= "treeWidget.expandNodes({$varName}, {$expandedKeys});";
        }
        // 禁用节点
        if ($disabledKeys) {
            $disabledKeys = json_encode($disabledKeys);
            $jsCode .= "treeWidget.disableNodes({$varName}, {$disabledKeys});";
        }
        // 展开所有
        $expandParent && $jsCode .= "{$varName}.expandAll(true);";
        if ($searchable) {
            $jsCode .= "treeWidget.fuzzySearch({$varName},'#input-search-{$this->id}');";
        }
        // 禁用树
        $disable = $this->config['disabled'];
        // js代码
        $js = <<<JS
         <script type="text/javascript">
            \$(function () {
                var {$varName} = \$.fn.zTree.init(\$("#{$this->id}"), {$zSetting}, {$zNodes});
                {$jsCode}
            });
         </script>
JS;
        return $js;
    }
}