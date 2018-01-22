<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license MIT
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace Utils\CommandLine;

/**
 * Functions to throw error messages.
 */
class Error
{

    /**
     * Displays a message and exits the application with error status code.
     * @param string $message The message to display before exiting the application.
     */
    public static function Show(string $message)
    {
        fwrite(STDERR, Parser::t("Error:") . " " . $message . "\n");
        exit(1);
    }

}