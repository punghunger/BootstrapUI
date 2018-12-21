<?php
namespace traits;
/**
 * ValidationTrait.php
 * @desc
 * @author: Sven
 * @since: 2018/12/13 10:33
 */
trait ValidationTrait
{
    /**
     * html属性名前缀
     * @var string
     */
    protected $namespace = 'data-parsley-';

    /**
     * 验证属性
     * @var array
     */
    protected $validateAttr = [
        'required' => 'true'
    ];

    /**
     * 表单元素的验证配置项
     * @var array
     */
    protected $fieldOptions = [
        'value',
        'group',
        'multiple',
        'validate-if-empty',
        'whitespace',
        'ui-enabled',
        'errors-messages-disabled',
        'excluded',
        'debounce',
        'trigger',
        'trigger-after-failure',
        'no-focus',
        'validation-threshold',
        'class-handler',
        'errors-container',
        'error-message',
        '%-message'     // required, validators,
    ];

    /**
     * 表单的验证配置项
     * @var array
     */
    protected $formOptions = [
        'namespace',
        'validate',
        'priority-enabled',
        'inputs',
        'excluded',
        'ui-enabled',
        'focus',
        'errors-messages-disabled',
    ];

    /**
     * 验证器
     * @var array
     */
    protected $validators = [
        'required',
        'type' => [
            'email',
            'number',
            'integer',
            'digits',
            'alphanum',
            'url',
        ],
        'minlength',
        'length',   // "[6, 10]"
        'min',
        'max',
        'range',   // "[6, 10]"
        'pattern',
        'mincheck',
        'maxcheck',
        'check',    // "[1, 3]"
        'equalto',  // "#anotherfield"
    ];

    /**
     * 设置验证数据
     * @param $data
     * @return $this
     */
    function validate($data = null)
    {
        // 参数判断
        if (is_bool($data) && $data === false) {
            // 不验证数据
            unset($this->validateAttr['required']);
        } else if (is_array($data)) {
            // 验证属性
            foreach ($data as $key => $item) {
                if (is_numeric($key)) {
                    $item = $this->namespace . $item;
                } else {
                    $key = $this->namespace . $key;
                }
                $this->validateAttr[$key] = $item;
            }
        }
        return $this;
    }

    /**
     * 合并验证属性
     * @param $attr
     * @return bool
     */
    function combineValidateAttr(&$attr)
    {
        if ($this->validateAttr) {
            $attr = $this->combineAttr($attr, $this->validateAttr);
        }
        return true;
    }


}
