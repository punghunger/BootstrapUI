<?php
use traits\WidgetTrait;

/**
 * ColWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/12/12 15:52
 */
class ColWidget
{
    use WidgetTrait;
    protected $attr = [];
    protected $attrClass = [];
    protected $content = '';
    protected $gutter = 0;


    public function __construct($md = 24, $xs = 0, $sm = 0, $lg = 0)
    {
        $class = "col-md-{$md}";
        $xs && $class .= " col-xs-{$xs}";
        $sm && $class .= " col-sm-{$sm}";
        $lg && $class .= " col-lg-{$lg}";
        $this->attrClass = $class;
        return $this;
    }

    public function gutter($num = 0)
    {
        if ($num) {
            $avg = round($num / 2, 2);
            $this->attr['style'] = "padding-left: {$avg}px; padding-right: {$avg}px;";
        }
        return $this;
    }

    public function setHtml()
    {

    }

    public function offset($md = 0, $xs = 0, $sm = 0, $lg = 0)
    {
        $md && $this->attrClass .= " col-md-offset-{$md}";
        $xs && $this->attrClass .= " col-xs-offset-{$xs}";
        $sm && $this->attrClass .= " col-sm-offset-{$sm}";
        $lg && $this->attrClass .= " col-lg-offset-{$lg}";
        return $this;
    }

    public function render($html = '')
    {
        $this->attr['class'] = $this->attrClass;
        $html = JvnHtml::tag('div', $html, $this->attr);
        return $html;
    }
}
