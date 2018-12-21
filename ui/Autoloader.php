<?php
define("WIDGET_DIR", dirname(__FILE__) . "/widgets/");

/**
 * Autoloader.php
 * @desc
 * @author: Sven
 * @since: 2018/10/22 16:50
 */
class Autoloader
{
    const namespace_PREFIX = '';

    /**
     * 向PHP注册在自动载入函数
     */
    public static function register()
    {
        spl_autoload_register(array(new self, 'autoload'));
    }

    /**
     * 根据类名载入所在文件
     */
    public static function autoload($className)
    {
        $namespacePrefixStrlen = strlen(self::namespace_PREFIX);
        if (strncmp(self::namespace_PREFIX, $className, $namespacePrefixStrlen) === 0) {
            $className = str_replace('\\', '/', strtolower($className));
            $filePath = str_replace('\\', DIRECTORY_SEPARATOR, substr($className, $namespacePrefixStrlen));
            $filePath = WIDGET_DIR . (empty($filePath) ? '' : DIRECTORY_SEPARATOR) . $className . '.php';
            //$filePath = realpath(WIDGET_DIR . (empty($filePath) ? '' : DIRECTORY_SEPARATOR) . $filePath . '.lib.php');
            if (file_exists($filePath)) {
                require_once $filePath;
            } else {
                echo $filePath;
            }
        }
    }

}