<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus\Lists;

/**
 * Get various list of inactivities.
 */
class Inactivities extends \InFocus\ActivityDB
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
     * @return \InFocus\Element\Inactivity[]
     */
    public function getAll()
    {
        $elements = array();

        $statement = $this->database->query(
            "select * from inactivity_log"
        );

        while($data = $statement->fetch(\PDO::FETCH_ASSOC))
        {
            $element = new \InFocus\Element\Inactivity();

            foreach($data as $name => $value)
            {
                $element->$name = $value;
            }

            $elements[] = $element;
        }

        return $elements;
    }

    /**
     * Gets a list of all inactivities by date.
     * @param int $day
     * @param int $month
     * @param int $year
     * @return \InFocus\Element\Inactivity[]
     */
    public function getWithTime(int $day=0, int $month=0, int $year=0, $idle=0)
    {
        $elements = array();

        $where = "";
        if($day || $month || $year || $idle)
        {
            $where .= "where ";

            if($day)
            {
                $where .= "day_from = " . intval($day) . " ";
            }

            if($month)
            {
                if($where != "where ")
                    $where .= "and ";

                $where .= "month_from = " . intval($month) . " ";
            }

            if($year)
            {
                if($where != "where ")
                    $where .= "and ";

                $where .= "year_from = " . intval($year) . " ";
            }

            if($idle)
            {
                if($where != "where ")
                    $where .= "and ";

                $where .= "idle > 0 ";
            }
        }

        $statement = $this->database->query(
            "select * from inactivity_log "
            . $where
            . "order by seconds desc "
            . "limit 100"
        );

        $first_element = true;
        $longest_element = 0;

        while($data = $statement->fetch(\PDO::FETCH_ASSOC))
        {
            $element = new \InFocus\Element\Inactivity();

            foreach($data as $name => $value)
            {
                $element->$name = $value;
            }

            if($first_element)
            {
                $longest_element = $element->seconds;
                $first_element = false;
            }

            $element->usage_percent =
                ($element->seconds / $longest_element)
                * 100
            ;

            $elements[] = $element;
        }

        return $elements;
    }

    /**
     * Gets total time of inactivity for both idle and system.
     * @param int $day
     * @param int $month
     * @param int $year
     * @return array Array in format ["system"=>seconds, "idle"=>seconds]
     */
    public function getTotalTime(int $day=0, int $month=0, int $year=0)
    {
        $elements = array();

        $where = "";
        if($day || $month || $year || $idle)
        {
            $where .= "where ";

            if($day)
            {
                $where .= "day_from = " . intval($day) . " ";
            }

            if($month)
            {
                if($where != "where ")
                    $where .= "and ";

                $where .= "month_from = " . intval($month) . " ";
            }

            if($year)
            {
                if($where != "where ")
                    $where .= "and ";

                $where .= "year_from = " . intval($year) . " ";
            }
        }

        $statement = $this->database->query(
            "select sum(seconds) as total_seconds from inactivity_log "
            . $where
            . ($where ? "and idle <> 1" : "where idle <> 1")
        );

        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        $elements["system"] = $data["total_seconds"] ?? 0;

        $statement = $this->database->query(
            "select idle, sum(seconds) as total_seconds from inactivity_log "
            . $where
            . ($where ? "and idle = 1" : "where idle = 1")
        );

        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        $elements["idle"] = $data["total_seconds"] ?? 0;

        return $elements;
    }
}