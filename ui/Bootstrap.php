<?php
//namespace UI\Bootstrap;
/**
 * bootstrap.php
 * @desc
 * @author: Sven
 * @since: 2018/10/16 17:05
 */
include 'Autoloader.php';
Autoloader::register();

class Bootstrap
{
    private static $instance;

    static public function make()
    {
        // 如果不存在实例，则返回实例
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __call($name, $arguments)
    {
        $className = ucfirst($name) . 'Widget';
        $object = new $className($arguments);
        return $object;
        // 注意: $name 的值区分大小写
        echo "Calling object method '$name' "
            . implode(', ', $arguments) . "\n";
    }

    public static function __callStatic($name, $arguments)
    {
        $className = ucfirst($name) . 'Widget';
        if ($arguments) {
            $args = "";
            $aot = '';
            foreach ($arguments as $key => $argument) {
                $args .= "{$aot}\$arguments[{$key}]";
                $aot = ',';
            }
            eval("\$object = new {$className}($args);");
        } else {
            $object = new $className;
        }
        return $object;
    }

}