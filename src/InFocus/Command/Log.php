<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus\Command;

/**
 * Logs active applications time usage and inactivity time.
 */
class Log extends \Utils\CommandLine\Command
{
    private $exit_now;

    public function __construct()
    {
        parent::__construct("log");

        $this->description = "Start the activity logger.";

        $this->exit_now = false;
    }

    public function Execute()
    {
        $setup_ready = false;

        echo "Waiting for user setup... ";
        while(!$setup_ready)
        {
            $settings = new \InFocus\Settings("InFocus");
            $setup = $settings->get("setup");
            unset($settings);

            if($setup)
            {
                $setup_ready = true;
            }
            else
            {
                sleep(5);
            }
        }
        echo "(done)\n";

        echo "Updating application icons.\n";

        $activities = new \InFocus\Lists\Activities();
        $activities->updateActivityIcons();
        unset($activities);

        echo "Starting activity logger.\n";
        $activity_logger = new \InFocus\ActivityLogger();

        while(true)
        {
            $activity_logger->logActivity();

            sleep(1);
        }
    }
}