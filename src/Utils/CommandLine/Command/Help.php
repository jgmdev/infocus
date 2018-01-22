<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license MIT
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace Utils\CommandLine\Command;

/**
 * Class that represents an action executed when a specific command is called.
 */
class Help extends \Utils\CommandLine\Command
{
    public function __construct(\Utils\CommandLine\Parser $parser)
    {
        parent::__construct("help");

        $this->description = \Utils\CommandLine\Parser::t("Display a help message for a specific command.");

        $this->description .= "\n"
            . \Utils\CommandLine\Parser::t("Example:")
            . " "
            . $parser->application_name
            . \Utils\CommandLine\Parser::t(" help <command>")
        ;

        $this->RegisterAction(new \Utils\CommandLine\Command\HelpAction());
    }
}