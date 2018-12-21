<?php

/**
 * DatePickerWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/12/19 16:42
 */
class DatePickerWidget extends InputWidget
{
    private $locate;
    private $input;
    private $formatMapping = [
        'y' => 'yy',    // 4位数字完整表示的年份 例如：1999 或 2003
        'Y' => 'yyyy',  // 2位数字表示的年份
        'n' => 'm',     // 月份，没有前导零
        'm' => 'mm',    // 月份，有前导零
        'j' => 'd',     // 月份中的第几天，没有前导零
        'd' => 'dd',    // 月份中的第几天，有前导零的 2 位数字
        'g' => 'H',     // g 小时，12 小时格式，没有前导零 1 到 12
        'h' => 'HH',    // h 小时，12 小时格式，有前导零 01 到 12
        'G' => 'h',     // G 小时，24 小时格式，没有前导零 0 到 23
        'H' => 'hh',    // H 小时，24 小时格式，有前导零 00 到 23
        'i' => 'ii',    // 有前导零的分钟数
        's' => 'ss',    // 秒，有前导 0 的 2 位数字
        'a' => 'p',     // 上午还是下午，2 位小写字符 am 或 pm
        'A' => 'P',     // 上午还是下午，2 位大写字符 AM 或 PM
    ];
    private $config = [
        'language' => 'zh-CN',
        'orgFormat' => "Y-m-d",
        'format' => "Y-m-d",
        'weekStart' => 0,
        'autoclose' => true,
        'todayBtn' => true,
        'todayHighlight' => true,
        'forceParse' => true,
        'showMeridian' => false,
        'minuteStep' => 5,
        'pickerPosition' => 'bottom-right',
        /**
         * 0 or 'hour' for the hour view
         * 1 or 'day' for the day view
         * 2 or 'month' for month view (the default)
         * 3 or 'year' for the 12-month overview
         * 4 or 'decade' for the 10-year overview
         */
        'startView' => 'month',
        'minView' => 'month',
        'maxView' => 'decade',
    ];

    function __construct($name = null, $value = null, $config = null, $locate = 'after')
    {
        $this->locate = $locate;
        $this->config($config);
        // 默认id
        $id = 'datepicker-' . mt_rand(10000, 99999);
        $this->id($id, false);
        // 默认值设置，若为时间戳，自动格式化
        if ($value && is_numeric($value)) {
            $value = date($this->config['format'], $value);
        }
        // 实例父类（InputWidget）
        parent::__construct($name, $value);

    }

    public function config($config)
    {
        // 为字符串表示日期格式
        if (is_string($config)) {
            $this->config['format'] = $config;
        } else if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
        return $this;
    }

    public function render()
    {
        // 日期图标
        $icon = Bootstrap::icon('glyphicon-calendar');
        // 设置图标显示
        $this->locate == 'after' ? $this->addonAfter($icon) : $this->addonBefore($icon);
        // 设置特定样式
        $this->inputGroupAttr([
            'class' => 'jvn-datepicker-wrapper date',
            'id' => $this->id
        ]);
        // 生成html
        $html = parent::render();
        // 创建js代码
        $html .= $this->makeJs();
        // 返回
        return $html;
    }

    /**
     * 生成js代码
     * @return string
     */
    protected function makeJs()
    {
        $config = $this->getConfig();
        $js = <<<JS
         <script type="text/javascript">
            var {$this->jsVar};
            \$(function () {
                {$this->jsVar} = \$("#{$this->id}").datetimepicker({$config});
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
        // 日期格式
        $format = $this->config['format'];
        // 判断是否只是年月日格式
        if ($format == 'Y' || $format == 'y') {
            // 只选择年
            $this->config['startView'] = 'decade';
            $this->config['minView'] = 'decade';
            $this->config['todayBtn'] = false;
        } else if (!preg_match('/j|d|g|h|G|H|i|s|a|A/', $format, $match)) {
            // 只选择年-月
            $this->config['startView'] = 'year';
            $this->config['minView'] = 'year';
            $this->config['todayBtn'] = false;
        } else if (preg_match('/g|h|G|H|i|s|a|A/', $format, $match)) {
            // 只选择年-月-日
            $this->config['minView'] = 'hour';
            $this->config['showMeridian'] = true;
        }
        $this->config['orgFormat'] = $format;
        // 替换格式
        $this->config['format'] = strtr($format, $this->formatMapping);
        // 根据图标的位置，设置日期选择容器显示的位置
        if ($this->locate == 'after') {
            $this->config['pickerPosition'] = 'bottom-left';
        }
        // json格式化
        $config = json_encode($this->config);
        // 将带有Callback的字段转换为可读的js代码
        $config = $this->convertCallback2JsCode($config);
        return $config;
    }
}