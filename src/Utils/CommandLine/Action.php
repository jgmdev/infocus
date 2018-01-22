<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license MIT
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace Utils\CommandLine;

/**
 * Class that represents an action executed when a specific command is called.
 */
abstract class Action
{
    /**
     * A reference to the parent command.
     * @var \Utils\CommandLine\Command
     */
    public $command;

    /**
     * Method called by the command if it was executed.
     */
    abstract public function OnCall(Command $command);
}