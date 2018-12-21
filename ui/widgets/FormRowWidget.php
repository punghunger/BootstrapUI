<?php
use traits\GridTrait;

/**
 * FormWidget.php
 * @desc
 * @author: Sven
 * @since: 2018/12/12 10:41
 */
class FormRowWidget extends FormItemWidget
{

    protected $id;
    protected $attr = [];
    protected $formItems = [];

    public function __construct($formItems = [])
    {
        $this->init();
        $this->formItems = $formItems;
    }

    public function init()
    {
        $this->attr = [
            'class' => 'form-row form-group'
        ];
    }


    //
    public function render()
    {
        $html = '';
        foreach ($this->formItems as $key => $formItem) {
            if ($formItem && is_object($formItem)) {
                $formItem->hasFormRow = true;
                $html .= $formItem->layout($this->layout)->gutter($this->gutter)->render();
            }
        }
        $html = Bootstrap::row()->attr($this->attr)->gutter($this->gutter)->render($html);
        return $html;
    }


}
