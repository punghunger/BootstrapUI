<?php
/**
 * TemplateTrait.php
 * @desc
 * @author: Sven
 * @since: 2018/12/19 14:53
 */

namespace traits;


trait TemplateTrait
{
    private $_templates = [];

    public function setTemplates($templates = [])
    {
        if (is_array($templates)) {
            $this->_templates = $templates;
        }
    }

    public function parseTemplate($name, $data)
    {
        // 获取模板内容
        $template = $this->_templates[$name]??'';
        // 填充模板数据
        $template = $this->fillTemplate($template, $data[$name]??[]);
        // 解析包含的其它模板内容
        $html = preg_replace_callback('/\{\{tpl:(.*?)\}\}/i', function ($match) use ($data) {
            // 模板名称
            $tplName = $match[1];
            // 解析模板内容
            $template = $this->parseTemplate($tplName, $data);
            return $template;
        }, $template);
        // 返回
        return $html;
    }

    public function fillTemplate($template, $data)
    {
        $search = ['{{attrs}}', '{{content}}'];
        $attrs = $data['attrs']??[];
        $className = '';
        // 获取class样式名，假如设置了要新增的className，%s为替换标识
        if (isset($attrs['class']) && strpos($template, '%s') !== false) {
            $className = $attrs['class'];
            unset($attrs['class']);
        }
        $replace = [
            'attrs' => $attrs ? ' ' . $this->joinAttr($attrs) : '',
            'content' => $data['content']??''
        ];
        $result = str_replace($search, $replace, $template);
        // 增加样式名
        $result = sprintf($result, $className ? ' ' . $className : '');
        return $result;
    }
}