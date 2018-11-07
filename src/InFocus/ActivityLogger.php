<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus;

/**
 * Logs active applications time usage and inactivity time.
 */
class ActivityLogger extends ActivityDB
{
    private $is_idle;
    private $idle_timestamp_startpoint;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Logs current application activity as destected inactivity.
     */
    public function logActivity():void
    {
        try
        {
            $window = WM\Manager::getActiveWindow();

            if(!$this->registerIdleInactivity())
            {
                $this->registerShutdownInactivity();

                $this->registerApplication($window);

                $this->registerActivity($window);
            }
        }
        catch(\Exception $e)
        {
            print $e->getMessage() . "\n";
        }
    }

    /**
     * Returns true if user is inactive.
     * @return bool
     */
    private function registerIdleInactivity():bool
    {
        $xprintidle_found = exec("command -v xprintidle");

        if($xprintidle_found)
        {
            $milliseconds = intval(exec("xprintidle"));

            $seconds = 0;

            if($milliseconds > 1000)
            {
                $seconds = $milliseconds / 1000;
            }

            if($seconds >= 60 && !$this->is_idle)
            {
                $this->is_idle = true;
                $this->idle_timestamp_startpoint = time() - 60;
            }
            elseif($seconds < 60 && $this->is_idle)
            {
                $statement = $this->database->prepare(
                    "insert into inactivity_log "
                    . "("
                    . "idle,"
                    . "seconds,"
                    . "day_from,"
                    . "month_from,"
                    . "year_from,"
                    . "day_to,"
                    . "month_to,"
                    . "year_to,"
                    . "from_timestamp,"
                    . "to_timestamp"
                    . ") "
                    . "values("
                    . "?, ?, ?, ?, ?, ?, ?, ?, ?, ?"
                    . ")"
                );

                $idle_timestamp_endpoint = time();
                $inactive_seconds = $idle_timestamp_endpoint
                    -
                    $this->idle_timestamp_startpoint
                ;

                $statement->execute(
                    array(
                        1,
                        $inactive_seconds,
                        date("j", $this->idle_timestamp_startpoint),
                        date("n", $this->idle_timestamp_startpoint),
                        date("Y", $this->idle_timestamp_startpoint),
                        date("j", $idle_timestamp_endpoint),
                        date("n", $idle_timestamp_endpoint),
                        date("Y", $idle_timestamp_endpoint),
                        $this->idle_timestamp_startpoint,
                        $idle_timestamp_endpoint
                    )
                );

                $this->is_idle = false;
            }

            if($seconds >= 60)
            {
                return true;
            }
        }

        return false;
    }

    private function registerShutdownInactivity():void
    {
        $last_activity_time = $this->settings->home_directory
            . "/last_activity_time"
        ;

        $last_activity = "";

        if(file_exists($last_activity_time))
        {
            $last_activity = filemtime($last_activity_time);
        }

        if($last_activity != "")
        {
            $current_time = time();
            $inactive_seconds = $current_time - $last_activity;

            if($inactive_seconds >= 10)
            {
                $statement = $this->database->prepare(
                    "insert into inactivity_log "
                    . "("
                    . "idle,"
                    . "seconds,"
                    . "day_from,"
                    . "month_from,"
                    . "year_from,"
                    . "day_to,"
                    . "month_to,"
                    . "year_to,"
                    . "from_timestamp,"
                    . "to_timestamp"
                    . ") "
                    . "values("
                    . "?, ?, ?, ?, ?, ?, ?, ?, ?, ?"
                    . ")"
                );

                $statement->execute(
                    array(
                        0,
                        $inactive_seconds,
                        date("j", $last_activity),
                        date("n", $last_activity),
                        date("Y", $last_activity),
                        date("j"),
                        date("n"),
                        date("Y"),
                        $last_activity,
                        $current_time
                    )
                );
            }
        }

        touch($last_activity_time);
    }

    private function registerApplication(WM\Window $window):void
    {
        // Register application if not registered yet
        $statement = $this->database->prepare(
            "select * from activities where binary = ?"
        );

        $statement->execute(array($window->process_name));

        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        if(!$data)
        {
            $statement = $this->database->prepare(
                "insert into activities "
                . "("
                . "name,"
                . "description,"
                . "binary,"
                . "type,"
                . "icon_name,"
                . "icon_path"
                . ") "
                . "values("
                . "?, ?, ?, ?, ?, ?"
                . ")"
            );

            $types = new Lists\Types;

            $statement->execute(
                array(
                    $window->name,
                    "",
                    $window->process_name,
                    $types->getBestMatch($window, false),
                    $window->icon_name,
                    $window->icon_path
                )
            );
        }
    }

    private function registerActivity(WM\Window $window):void
    {
        // Register application activity
        $statement = $this->database->prepare(
            "select * from activity_log "
            . "where "
            . "application_name = ? and "
            . "window_title = ? and "
            . "day = ? and "
            . "month = ? and "
            . "year = ?"
        );

        $statement->execute(
            array(
                $window->process_name,
                $window->title,
                date("j"),
                date("n"),
                date("Y"),
            )
        );

        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        if(!$data)
        {
            $statement = $this->database->prepare(
                "insert into activity_log "
                . "("
                . "application_name,"
                . "window_title,"
                . "type,"
                . "seconds,"
                . "day,"
                . "month,"
                . "year,"
                . "start_timestamp, "
                . "last_activity_timestamp"
                . ") "
                . "values("
                . "?, ?, ?, ?, ?, ?, ?, ?, ?"
                . ")"
            );

            $activity = new Element\Activity($window->process_name);

            $types = new Lists\Types;

            $statement->execute(
                array(
                    $window->process_name,
                    $window->title,
                    $types->getBestMatch($window),
                    1,
                    date("j"),
                    date("n"),
                    date("Y"),
                    time(),
                    time()
                )
            );
        }
        else
        {
            $statement = $this->database->prepare(
                "update activity_log "
                . "set "
                . "seconds = seconds+1, "
                . "last_activity_timestamp = ? "
                . "where "
                . "application_name = ? and "
                . "window_title = ? and "
                . "day = ? and "
                . "month = ? and "
                . "year = ?"
            );

            $statement->execute(
                array(
                    time(),
                    $window->process_name,
                    $window->title,
                    date("j"),
                    date("n"),
                    date("Y")
                )
            );
        }
    }
}
