<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus;

/**
 * Autoloader for InFocus.
 */
class Autoloader
{
    /**
     * The system class autoloader.
     * @param string $class_name
     */
    static function load($class_name)
    {
        $file = str_replace("\\", "/", $class_name) . ".php";

        if(file_exists(__DIR__ . "/" . $file))
            include(__DIR__ . "/" . $file);
    }

    /**
     * Provides an easy way to register the autoloader for you.
     */
    static function register()
    {
        spl_autoload_register(array('InFocus\Autoloader', 'load'));
    }
}