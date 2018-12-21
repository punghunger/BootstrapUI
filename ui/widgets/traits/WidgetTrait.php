<?php
namespace traits;
/**
 * WidgetTrait.php
 * @desc
 * @author: Sven
 * @since: 2018/10/23 14:23
 */
trait WidgetTrait
{
    protected $attr = [];
    protected $id = '';
    protected $name = '';
    protected $value = null;
    protected $jsVar = '';

    /**
     * 调用数组中的key对应的方法，方法的值为key对应的值
     * @param $params
     * @return bool
     */
    function autoCallMethod($params)
    {
        foreach ($params as $key => $val) {
            if (method_exists($this, $key) && !is_null($val)) {
                call_user_func_array([$this, $key], is_array($val) ? array($val) : (array)$val);
            }
        }
        return true;
    }

    /**
     * 组件id
     * @param $id
     * @return $this
     */
    public function id($id, $setAttrId = true)
    {
        $this->id = $id;
        $setAttrId && $this->attr['id'] = $id;
        $this->jsVar = $this->converCamelCase($this->id);
        return $this;
    }

    /**
     * 设置组件属性
     * @param $attr
     * @return $this
     */
    function attr($attr = [])
    {
        // 数组格式
        if (is_array($attr)) {
            // input 标签设置属性value的值
            $this->attr = $this->combineAttr($this->attr, $attr);
        }
        return $this;
    }

    /**
     * 设置名称
     * @param $data
     * @return $this
     */
    public function name($data)
    {
        $this->name = $data;
        $this->attr['name'] = $data;
        return $this;
    }

    /**
     * 设置默认值
     * @param $data
     * @return $this
     */
    function value($data)
    {
        $this->value = $data;
        return $this;
    }

    /**
     * 获取组件使用的js变量名称
     * @return string
     */
    public function getJSvar()
    {
        return $this->jsVar;
    }

    /**
     * 拼接样式名属性
     * @param $class
     * @param $classArr
     * @return bool
     */
    function joinAttrClass(&$class, $classArr)
    {
        if ($classArr) {
            $class .= ($class ? ' ' : '') . implode(' ', $classArr);
        }
        return true;
    }

    /**
     * 拼接属性
     * @param $attrs
     * @return string
     */
    function joinAttr($attrs)
    {
        $attr = $space = '';
        foreach ($attrs as $key => $value) {
            $attr .= is_string($key) ? "{$space}{$key}=\"{$value}\"" : "{$space}{$value}";
            $space = ' ';
        }
        return $attr;
    }

    /**
     * 合并属性
     * @param $attr     默认属性
     * @param $extra    新增属性
     * @param array $appends 属性值是追加而不是覆盖的属性名称，默认class
     * @return array
     */
    function combineAttr($attr, $extra, $appends = ['class'])
    {
        $attr || $attr = [];
        if ($extra) {
            foreach ($extra as $key => $item) {
                // class 属性为追加值
                $attr[$key] = in_array($key, $appends) && isset($attr[$key])
                    ? $attr[$key] . ' ' . $item
                    : $item;
            }
        }
        return $attr;
    }

    /**
     * 将带有Callback的字段转换为可读的js代码
     * @param $config   json字符串格式的配置数据
     * @return mixed
     */
    function convertCallback2JsCode($config)
    {
        $pattern = '/Callback":"(.*?)"/';
        $replacement = '":${1}';
        $config = preg_replace($pattern, $replacement, $config);
        return $config;
    }


    /**
     * 将下划线（_）和横线（-）连接的字符串转为峰驼格式的字符串
     *
     * @param $str
     * @param bool $ucfirst
     * @return mixed
     */
    function converCamelCase($str, $ucfirst = false)
    {
        // 替换划线（_）和横线（-）为空格
        $str = ucwords(str_replace(['_', '-'], ' ', $str));
        // 转大写，并去除空格
        $str = str_replace(' ', '', lcfirst($str));
        return $ucfirst ? ucfirst($str) : $str;
    }
}
