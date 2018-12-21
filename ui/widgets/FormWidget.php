<?php
use traits\WidgetTrait;
use traits\GridTrait;
use traits\ValidationTrait;

/**
 * FormWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/12/12 10:41
 */
class FormWidget
{
    use WidgetTrait, GridTrait, ValidationTrait;

    protected $id;
    protected $attr = [];
    protected $attrClass = [];
    protected $validate = false;

    protected $templates = [
        'formItem' => '<div class="form-group %s"{{attrs}}>{{tpl:label}}{{tpl:control}}</div>',
        'label' => '<label{{attrs}}>{{content}}</label>',
        'control' => '<div{{attrs}}>{{content}}{{tpl:extra}}</div>',
        'help' => '<span class="help-block"{{attrs}}>{{content}}</span>',
        'extra' => '<span class="form-extra">{{content}}</span>',
        'row' => '<div class="row form-row"{{attrs}}>{{content}}</div>',
        'col' => '<div{{attrs}}>{{content}}</div>',
    ];
    protected $formItems = [];
    protected $layout;

    public function __construct($formItems = [])
    {
        $this->init();
        if (is_array($formItems)) {
            $this->formItems = $formItems;
        }
    }

    public function init()
    {
        $this->attr = [
            'class' => 'jvn-form'
        ];
    }

    // horizontal | inline | vertical
    function layout($layout = 'vertical')
    {
        $this->layout = $layout;
        $this->attrClass['layout'] = "form-{$layout}";
        return $this;
    }


    //
    public function render()
    {
        $html = '';
        foreach ($this->formItems as $key => $formItem) {
            if ($formItem && is_object($formItem)) {
                // 使用formRow时，当排列方式为inline时，自动转为horizontal
                if ($formItem instanceof FormRowWidget) {
                    if ($this->layout == 'inline') {
                        $this->layout('horizontal');
                    }
                }
                $html .= $formItem->layout($this->layout)->render();
            }
        }
        // 合并样式类名
        $this->joinAttrClass($this->attr['class'], $this->attrClass);
        // 添加验证属性
        $this->combineValidateAttr($this->attr);
        //
        $html = JvnHtml::tag('form', $html, $this->attr);
        return $html;
    }


}
