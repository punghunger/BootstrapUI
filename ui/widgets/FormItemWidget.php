<?php
use traits\WidgetTrait;
use traits\GridTrait;
use traits\ValidationTrait;
use traits\TemplateTrait;

/**
 * FormWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/12/12 10:41
 */
class FormItemWidget
{
    use WidgetTrait, GridTrait, ValidationTrait, TemplateTrait;

    protected $id;
    protected $attr = [];
    protected $templates = [
        'formItem' => '<div class="form-group%s"{{attrs}}>{{tpl:label}}{{tpl:control}}</div>',
        'formOperateItem' => '<div class="form-group%s"{{attrs}}>{{tpl:control}}</div>',
        'label' => '<div class="form-item-label%s"{{attrs}}><label>{{content}}</label></div>',
        'control' => '<div class="form-item-control-wrapper%s"{{attrs}}>{{content}}{{tpl:help}}</div>',
        'help' => '<div class="help-block"{{attrs}}>{{content}}</div>',
        'extra' => '<div class="form-extra">{{content}}</div>',
    ];
    protected $label;
    protected $control;
    protected $extra;
    protected $layout;
    /**
     * 是否有row容器
     * @var bool
     */
    protected $hasFormRow = false;

    public function __construct($label, $control, $extra = '')
    {
        $this->init();
        $this->label = $label;
        $this->control = $control;
        $this->extra = $extra;
        if ($this->extra) {
            $this->templates['control'] = '<div class="form-item-control-wrapper%s"{{attrs}}>{{content}}{{tpl:help}}{{tpl:extra}}</div>';
        }
        // 设置显示模板
        $this->setTemplates($this->templates);
    }

    // horizontal | inline | vertical
    function layout($layout = 'vertical')
    {
        $this->layout = $layout;
        return $this;
    }

    public function init()
    {

    }

    function getFormItemHtml()
    {
        $tplData = [
            'formItem' => [
                'attrs' => ['class' => ''],
                'className' => ''
            ],
            'label' => [
                'attrs' => ['class' => ''],
                'content' => $this->label,
            ],
            'control' => [
                'attrs' => [],
                'content' => is_object($this->control) ? $this->control->render() : $this->control
            ],
            'extra' => [
                'content' => $this->extra,
            ]
        ];
        // horizontal'|'vertical'|'inline'
        if ($this->layout == 'horizontal' && !$this->hasFormRow) {
            $tplData['label']['attrs']['class'] = 'control-label col-sm-2';
            $tplData['control']['attrs']['class'] = 'col-sm-8';
        }
        //
        return $this->parseTemplate('formItem', $tplData);
    }

    //
    public function render()
    {
        $html = $this->getFormItemHtml();
        // 渲染col的html
        $html = $this->renderGridCol($html);
        return $html;

    }


}
