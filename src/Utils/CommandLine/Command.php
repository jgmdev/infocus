<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license MIT
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace Utils\CommandLine;

/**
 * A sub command processed by the application. Example peg init
 */
class Command
{
    /**
     * Reference to parent parser.
     * @var \Utils\CommandLine\Parser
     */
    public $parser;

    /**
     * Name of the command.
     * @var string
     */
    public $name;

    /**
     * Description of the command displayed on help.
     * @var string
     */
    public $description;

    /**
     * Array of Options processed by the command.
     * @var \Utils\CommandLine\Option[]
     */
    public $options;

    /**
     * In case the command supports values. Example: peg init something
     * where 'init' is the command and 'something' is the value.
     * @var string
     */
    public $value;

    /**
     * Flag that indicates if a value is required for the command.
     * @var boolean
     */
    public $value_required;

    /**
     * Array of Actions called if this command gets executed.
     * @var \Utils\CommandLine\Action[]
     */
    public $actions;

    /**
     * Initialize the command.
     * @param string $name Name of the Sub-command
     * @param \Utils\CommandLine\Option[] $options List of options
     * @param \Utils\CommandLine\Action[] $actions List of actions
     */
    public function __construct($name, $options = array(), $actions = array())
    {
        $this->name = $name;
        $this->options = $options;
        $this->value = "";

        $this->actions = $actions;
    }

    /**
     * Define a new option accepted by the command.
     * @param \Utils\CommandLine\Option $option
     */
    public function AddOption(Option $option)
    {
        $option->parser = $this->parser;
        $option->command = $this;

        $this->options[] = $option;

        return $this;
    }

    /**
     * Gets an option by its long or short name.
     * @param type $name
     * @return null|\Utils\CommandLine\Option
     */
    public function GetOption($name)
    {
        foreach($this->options as $option)
        {
            if($option->long_name == $name || $option->short_name == $name)
            {
                return $option;
            }
        }

        return null;
    }

    /**
     * Execute each action associated to the command.
     */
    public function Execute()
    {
        foreach($this->actions as $action)
        {
            $action->OnCall($this);
        }

        return $this;
    }

    /**
     * Register actions that get call when command is executed.
     * @param \Utils\CommandLine\Action $action
     */
    public function RegisterAction(Action $action)
    {
        $action->command = $this;

        $this->actions[] = $action;

        return $this;
    }

}