<?php
use traits\WidgetTrait;

/**
 * RowWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/12/12 15:51
 */
class RowWidget
{
    use WidgetTrait;

    protected $attr = [];
    protected $attrClass = [];
    protected $gutter = 0;
    protected $cols = [];

    /**
     * RowWidget constructor.
     * @param $cols         列（col）的内容
     * @param int $gutter 列的间隔，推荐使用 (16+8n)px 作为栅格间隔。(n 是自然数)
     */
    public function __construct($cols = [], $gutter = 0)
    {
        $this->cols = $cols;
        $this->gutter($gutter);
        $this->init();
    }

    public function init()
    {
        $this->attrClass = [
            'class' => 'row',
        ];
    }

    public function gutter($num = 0)
    {
        if ($num) {
            $avg = round($num / 2, 2);
            $this->attr['style'] = "margin-left: -{$avg}px; margin-right: -{$avg}px;";
            $this->gutter = $num;
        }
        return $this;
    }

    public function col($md = 24, $xs = 0, $sm = 0, $lg = 0)
    {

    }

    public function render($html = '')
    {
        $html || $html = $this->renderCol();
        // 合并input框样式类名
        $this->joinAttrClass($this->attr['class'], $this->attrClass);
        $html = JvnHtml::tag('div', $html, $this->attr);
        $this->init();
        return $html;
    }

    /**
     * 渲染col的html代码
     * @return string
     */
    public function renderCol()
    {
        $html = '';
        foreach ($this->cols as $col) {
            if (is_object($col)) {
                $html .= $col->gutter($this->gutter)->render();
            }
        }
        return $html;
    }
}
