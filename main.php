<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

chdir(__DIR__);

// Register application autoloader
require "src/Autoloader.php";
InFocus\Autoloader::register();

$settings = new InFocus\Settings("InFocus");
date_default_timezone_set($settings->get("timezone", exec("date +%Z")));

// Main application point
$cli = new Utils\CommandLine\Parser();

$cli->application_name = "infocus";
$cli->application_version = "0.6";
$cli->application_description = "A time tracker application that monitors your "
    . "activity so you can evaluate how \nyou are spending your time on the "
    . "computer."
;

$cli->RegisterCommand(new InFocus\Command\Log());
$cli->RegisterCommand(new InFocus\Command\Ui());

$cli->Start($argc, $argv);
