<?php
namespace traits;
/**
 * GridTrait.php
 * @desc
 * @author: Sven
 * @since: 2018/12/13 10:33
 */
trait GridTrait
{
    public $gridCol;
    public $gridRow;
    public $gutter = 0;

    function col()
    {
        $args = func_get_args();
        $argsNum = func_num_args();
        if (isset($args[0]) && $args[0] && is_object($args[0])) {
            $this->gridCol = $args[0];
        } else {
            $argStr = "";
            $aot = '';
            foreach ($args as $key => $argument) {
                $argStr .= "{$aot}\$args[{$key}]";
                $aot = ',';
            }
            eval("\$this->gridCol = Bootstrap::col($argStr);");
        }
        return $this;
    }

    function offset($md = 0, $xs = 0, $sm = 0, $lg = 0)
    {
        if ($this->gridCol && is_object($this->gridCol)) {
            $this->gridCol->offset($md, $xs, $sm, $lg);
        }
        return $this;
    }

    public function gutter($num)
    {
        if ($this->gridCol && is_object($this->gridCol)) {
            $this->gridCol->gutter($num);
        }
        $this->gutter = $num;
        return $this;
    }

    /**
     * 渲染col的html代码
     * @return string
     */
    public function renderGridCol($html)
    {
        if ($this->gridCol) {
            $html = $this->gridCol->render($html);
        }
        return $html;
    }
}
