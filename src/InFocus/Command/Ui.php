<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus\Command;

/**
 * Dummy command to let user know he can invoke the interface with the 'ui'
 * command. The real work is performed by the infocus shell script that should
 * get installed on /usr/bin/
 */
class Ui extends \Utils\CommandLine\Command
{
    public function __construct()
    {
        parent::__construct("ui");

        $this->description = "Launches the graphical user interface. Needs chromium or firefox installed for this to work.";

        $this->AddOption(new \Utils\CommandLine\Option(
            array(
                "long_name" => "port",
                "short_name" => "p",
                "type" => \Utils\CommandLine\OptionType::INTEGER,
                "default_value" => 8080,
                "required" => false,
                "description" => "Port on which to start the server."
            )
        ));
    }
}