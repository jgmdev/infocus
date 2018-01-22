<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license MIT
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace Utils\CommandLine\Command;

/**
 * Action taken when help is called.
 */
class HelpAction extends \Utils\CommandLine\Action
{

    public function OnCall(\Utils\CommandLine\Command $command)
    {
        $parser = $this->command->parser;

        if(strlen($command->value) > 0)
        {
            if($help_command = $parser->GetCommand($command->value))
            {
                $this->PrintHelp($help_command);
            }
            else
            {
                \Utils\CommandLine\Error::Show(
                    \Utils\CommandLine\Parser::t("Invalid command supplied.")
                );
            }
        }
        else
        {
            $parser->PrintHelp();
        }
    }

    /**
     * Prints help for a specific command.
     * $param \Peg\Custom\CommandLine\Command $command
     */
    public function PrintHelp(\Utils\CommandLine\Command $command)
    {
        // Store the lenght of longest command name
        $max_command_len = 0;

        // Store the lenght of longest option name
        $max_option_len = 0;

        $parser = $this->command->parser;

        print \Utils\CommandLine\Parser::t("Description:") . "\n";

        $line = "   " . \Utils\CommandLine\Parser::t($command->description);
        $line = wordwrap($line, 80);
        $line_array = explode("\n", $line);

        print $line_array[0] . "\n";
        unset($line_array[0]);

        if(count($line_array) > 0)
        {
            foreach($line_array as $line)
            {
                print str_pad(
                    $line,
                    strlen($line) + ($max_command_len + 3),
                    " ",
                    STR_PAD_LEFT
                ) . "\n";
            }
        }

        print "\n";

        print \Utils\CommandLine\Parser::t("Usage:") . "\n";

        if(strlen($command->name) > $max_command_len)
            $max_command_len = strlen($command->name);

        if(count($command->options) > 0)
        {
            foreach($command->options as $option)
            {
                if(strlen($option->long_name) > $max_option_len)
                    $max_option_len = strlen($option->long_name);
            }

            print "   {$parser->application_name} {$command->name} "
                . \Utils\CommandLine\Parser::t("[options]") . "\n"
            ;
        }
        else
        {
            print "   {$parser->application_name} {$command->name}\n";
        }

        if(count($command->options) > 0)
        {
            print "\n";
            print \Utils\CommandLine\Parser::t("Options:") . "\n";
            foreach($command->options as $option)
            {
                $line = "   " .
                    str_pad(
                        "-" . $option->short_name . "  --" . $option->long_name,
                        $max_option_len + 8
                    ) .
                    \Utils\CommandLine\Parser::t($option->description)
                ;

                $line = wordwrap($line, 80);
                $line_array = explode("\n", $line);

                print $line_array[0] . "\n";
                unset($line_array[0]);

                if(count($line_array) > 0)
                {
                    foreach($line_array as $line)
                    {
                        print str_pad(
                            $line,
                            strlen($line) + ($max_option_len + 10),
                            " ",
                            STR_PAD_LEFT
                        ) . "\n";
                    }
                }
            }
        }

        print "\n";
    }

}