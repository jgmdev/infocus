<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus\Lists;

/**
 * Get various list of registered applications.
 */
class Activities extends \InFocus\ActivityDB
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
     * @return \InFocus\Element\Activity[]
     */
    public function getAll()
    {
        $statement = $this->database->query(
            "select * from activities"
        );

        while($data = $statement->fetch(\PDO::FETCH_ASSOC))
        {
            $activity = new \InFocus\Element\Activity();

            foreach($data as $name => $value)
            {
                $activity->$name = $value;
            }

            yield $activity;
        }
    }

    /**
     * Gets a list of all activities accompanied by its time usage:
     * activity->total_time and activity->usage_percent.
     * @param int $day
     * @param int $month
     * @param int $year
     * @return \InFocus\Element\Activity[]
     */
    public function getWithTime(int $day=0, int $month=0, int $year=0, $type=0)
    {
        $activities = array();

        $where = "";
        if($day || $month || $year || $type)
        {
            $where .= "where ";

            if($day)
            {
                $where .= "B.day = " . intval($day) . " ";
            }

            if($month)
            {
                if($where != "where ")
                    $where .= "and ";

                $where .= "B.month = " . intval($month) . " ";
            }

            if($year)
            {
                if($where != "where ")
                    $where .= "and ";

                $where .= "B.year = " . intval($year) . " ";
            }

            if($type)
            {
                if($where != "where ")
                    $where .= "and ";

                $where .= "A.type = " . intval($type) . " ";
            }
        }

        $statement = $this->database->query(
            "select A.id, A.name, A.binary, A.description, "
            . "A.type, A.icon_name, A.icon_path, "
            . "sum(B.seconds) as total_time "
            . "from activities A left join activity_log B "
            . "on A.binary = B.application_name "
            . $where
            . "group by A.binary "
            . "order by total_time desc"
        );

        $first_activity = true;
        $longest_activity = 0;

        while($data = $statement->fetch(\PDO::FETCH_ASSOC))
        {
            $activity = new \InFocus\Element\Activity();

            foreach($data as $name => $value)
            {
                $activity->$name = $value;
            }

            if($first_activity)
            {
                $longest_activity = $activity->total_time;
                $first_activity = false;
            }

            $activity->usage_percent =
                ($activity->total_time / $longest_activity)
                * 100
            ;

            $activities[] = $activity;
        }

        return $activities;
    }

    public function updateActivityIcons()
    {
        /** @var $activity \InFocus\Element\Activity */

        foreach($this->getAll() as $activity)
        {
            $window = new \InFocus\WM\Window();

            $window->setWindowFromActivity($activity);

            $window->setIconFromTheme($window->getCurrentIconTheme());

            if(!file_exists($window->icon_path))
            {
                $window->setIconFromTheme("hicolor");
            }

            $activity->icon_name = $window->icon_name;
            $activity->icon_path = $window->icon_path;

            $activity->update();
        }
    }
}