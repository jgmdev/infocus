<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus\Lists;

/**
 * Get various list of sub-activities.
 */
class SubActivities extends \InFocus\ActivityDB
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Gets all the available activities.
     * @return \InFocus\Element\SubActivity[]
     */
    public function getAll()
    {
        $activities = array();

        $statement = $this->database->query(
            "select * from activity_log"
        );

        while($data = $statement->fetch(\PDO::FETCH_ASSOC))
        {
            $activity = new \InFocus\Element\SubActivity();

            foreach($data as $name => $value)
            {
                $activity->$name = $value;
            }

            $activities[] = $activity;
        }

        return $activities;
    }

    /**
     * Gets a list of all activities accompanied by its time usage:
     * activity->total_time and activity->usage_percent.
     * @param int $day
     * @param int $month
     * @param int $year
     * @return \InFocus\Element\SubActivity[]
     */
    public function getWithTime(
        int $day=0,
        int $month=0,
        int $year=0,
        string $app_name="",
        int $type=0
    )
    {
        $activities = array();

        $where = "";
        if($day || $month || $year || $app_name || $type)
        {
            $where .= "where ";

            if($day)
            {
                $where .= "day = " . intval($day) . " ";
            }

            if($month)
            {
                if($where != "where ")
                    $where .= "and ";

                $where .= "month = " . intval($month) . " ";
            }

            if($year)
            {
                if($where != "where ")
                    $where .= "and ";

                $where .= "year = " . intval($year) . " ";
            }

            if($app_name)
            {
                if($where != "where ")
                    $where .= "and ";

                $where .= "application_name = '"
                    . str_replace("'", "''", $app_name)
                    . "' "
                ;
            }

            if($type)
            {
                if($where != "where ")
                    $where .= "and ";

                $where .= "type = ". intval($type) . " "
                ;
            }
        }

        $statement = $this->database->query(
            "select * "
            . "from activity_log "
            . $where
            . "order by seconds desc "
            . "limit 100"
        );

        $first_activity = true;
        $longest_activity = 0;

        while($data = $statement->fetch(\PDO::FETCH_ASSOC))
        {
            $activity = new \InFocus\Element\SubActivity();

            foreach($data as $name => $value)
            {
                $activity->$name = $value;
            }

            if($first_activity)
            {
                $longest_activity = $activity->seconds;
                $first_activity = false;
            }

            $activity->usage_percent =
                ($activity->seconds / $longest_activity)
                * 100
            ;

            $activities[] = $activity;
        }

        return $activities;
    }
}