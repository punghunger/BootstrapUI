<?php

/**
 * DateRangePickerWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/12/20 14:58
 */
class DateRangePickerWidget extends InputWidget
{
    private $locate;
    private $format = 'Y-m-d';
    /**
     * php日期格式对应的js的日期处理类(moment.js)日期格式
     * 参照 https://stackoverflow.com/questions/30186611/php-dateformat-to-moment-js-format
     * @var array
     */
    private $formatMapping = [
        'd' => 'DD',
        'D' => 'ddd',
        'j' => 'D',
        'l' => 'dddd',
        'N' => 'E',
        'S' => 'o',
        'w' => 'e',
        'z' => 'DDD',
        'W' => 'W',
        'F' => 'MMMM',
        'm' => 'MM',
        'M' => 'MMM',
        'n' => 'M',
        't' => '', // no equivalent
        'L' => '', // no equivalent
        'o' => 'YYYY',
        'Y' => 'YYYY',
        'y' => 'YY',
        'a' => 'a',
        'A' => 'A',
        'B' => '', // no equivalent
        'g' => 'h',
        'G' => 'H',
        'h' => 'hh',
        'H' => 'HH',
        'i' => 'mm',
        's' => 'ss',
        'u' => 'SSS',
        'e' => 'zz', // deprecated since version 1.6.0 of moment.js
        'I' => '', // no equivalent
        'O' => '', // no equivalent
        'P' => '', // no equivalent
        'T' => '', // no equivalent
        'Z' => '', // no equivalent
        'c' => '', // no equivalent
        'r' => '', // no equivalent
        'U' => 'X',
    ];

    private $config = [
        'singleDatePicker' => false,
        // 显示年，月下拉选择框
        'showDropdowns' => false,
        'showWeekNumbers' => false,
        'showISOWeekNumbers' => false,
        // 选择时间
        'timePicker' => false,
        'timePicker24Hour' => true,
        // 选择秒
        'timePickerSeconds' => true,
        // 每秒之间间隔，默认1
        'timePickerIncrement' => 1,
        'autoApply' => true,
        // 限定可选日期范围
        'dateLimit' => false,
        'locale' => [
            'direction' => 'ltr',
            'format' => 'YYYY-MM-DD',
            'separator' => ' - ',
            'applyLabel' => '确定',
            'cancelLabel' => '取消',
            'fromLabel' => '开始时间',
            'toLabel' => '结束时间',
            'customRangeLabel' => '自定义',
            // 星期名称, false 表示使用默认数据
            'daysOfWeek' => false,
            // 月份名称
            'monthNames' => false,
            'firstDay' => 1
        ],
        'alwaysShowCalendars' => true,
        'startDate' => false,
        'endDate' => false,
        'opens' => 'right',
        'drops' => 'down',
        'buttonClasses' => 'btn btn-sm',
        'applyClass' => 'btn-success',
        'cancelClass' => 'btn-default'
    ];

    function __construct($name = null, $startDate = null, $endDate = null, $config = null, $locate = 'after')
    {
        // 实例父类（InputWidget）
        parent::__construct($name);
        // 默认值设置
        $this->value($startDate, $endDate);
        // 配置数据
        $this->config($config);
        // 图标位置
        $this->locate = $locate;
        // 默认id
        $id = 'daterangepicker-' . mt_rand(10000, 99999);
        $this->id($id);
        $this->getDefaultRanges();
    }

    /**
     * 设置默认值
     * @param null $startDate
     * @param null $endDate
     * @return $this
     */
    public function value($startDate = null, $endDate = null)
    {
        // 默认值设置，若为时间戳，自动格式化
        if ($startDate) {
            if (is_numeric($startDate)) {
                $startDate = date($this->format, $startDate);
            }
            $this->config['startDate'] = $startDate;
        }
        if ($endDate) {
            if (is_numeric($endDate)) {
                $endDate = date($this->format, $endDate);
            }
            $this->config['endDate'] = $endDate;
        }
        return $this;
    }

    /**
     * 设置配置数据
     * @param $config
     * @return $this
     */
    public function config($config)
    {
        // 为字符串表示日期格式
        if (is_string($config)) {
            $this->format = $config;
        } else if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
        return $this;
    }

    public function ranges($ranges = [])
    {
        $ranges || $ranges = $this->getDefaultRanges();
        $this->config['ranges'] = $ranges;
        return $this;
    }

    public function getDayRange($offset = 0)
    {
        list($sd, $ed) = $this->getDayRangeOffset($offset);
        $range = [
            date('c', strtotime(date('Y-m-d 00:00:00') . " {$sd} day")),
            date('c', strtotime(date('Y-m-d 00:00:00') . " {$ed} day")),
        ];
        return $range;
    }

    public function getMonthRange($offset = 0)
    {
        list($sd, $ed) = $this->getDayRangeOffset($offset);
        $range = [
            date('c', strtotime(date('Y-m-01') . " {$sd} month")),
            date('c', strtotime(date('Y-m-01') . " {$ed} month")),
        ];
        return $range;
    }

    /**
     * 根据日期偏移量，获取开始与结束偏移量
     * @param int $offset
     * @return array
     */
    public function getDayRangeOffset($offset = 0)
    {
        if ($offset < 0) {
            $sd = $offset;
            $ed = 0;
        } else if ($offset == 0) {
            $sd = 0;
            $ed = +1;
        } else if ($offset >= 0) {
            $sd = +1;
            $ed = $offset + 1;
        }
        return [$sd, $ed];
    }

    protected function getDefaultRanges()
    {
        $ranges = [
            '今日' => $this->getDayRange(),
            '昨日' => $this->getDayRange(-1),
            '最近7日' => $this->getDayRange(-7),
            '最近30日' => $this->getDayRange(-30),
            '本月' => $this->getMonthRange(),
            '上月' => $this->getMonthRange(-1),
        ];
        return $ranges;
    }

    public function render()
    {
        // 日期图标
        $icon = Bootstrap::icon('glyphicon-calendar');
        // 设置图标显示
        $this->locate == 'after' ? $this->addonAfter($icon) : $this->addonBefore($icon);
        // 设置特定样式
        $this->inputGroupAttr([
            'class' => 'jvn-daterangepicker-wrapper',
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
    public function makeJs()
    {
        $config = $this->getConfig();
        $js = <<<JS
         <script type="text/javascript">
            var {$this->jsVar};
            \$(function () {
                {$this->jsVar} = \$("#{$this->id}").daterangepicker({$config});
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
        // 根据日期格式，判断是否设置显示时间选择器
        if (preg_match('/g|h|G|H|i|s|a|A/', $this->format, $match)) {
            $this->config['timePicker'] = true;
        }
        // 替换格式
        $this->config['locale']['format'] = strtr($this->format, $this->formatMapping);
        // json格式化
        $config = json_encode($this->config);
        // 将带有Callback的字段转换为可读的js代码
        $config = $this->convertCallback2JsCode($config);
        return $config;
    }
}
