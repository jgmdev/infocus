<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus\Element;

/**
 * Represents inactive time.
 */
class Inactivity extends \InFocus\ActivityDB
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $idle;

    /**
     * @var int
     */
    public $seconds;

    /**
     * @var int
     */
    public $day_from;

    /**
     * @var int
     */
    public $month_from;

    /**
     * @var int
     */
    public $year_from;

    /**
     * @var int
     */
    public $day_to;

    /**
     * @var int
     */
    public $month_to;

    /**
     * @var int
     */
    public $year_to;

    /**
     * @var string
     */
    public $from_timestamp;

    /**
     * @var string
     */
    public $to_timestamp;


    /**
     * Tries to load the inactivity from a given id.
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        parent::__construct();

        $this->id = $id;

        if($id)
        {
            $this->loadFromId($id);
        }
    }

    /**
     * Load activity with the given id.
     * @param int $id
     * @return bool
     */
    public function loadFromId(int $id):bool
    {
        $statement = $this->database->prepare(
            "select * from inactivity_log where id = ?"
        );

        $statement->execute(array($id));

        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        if($data)
        {
            foreach($data as $name=>$value)
            {
                $this->$name = $value;
            }

            return true;
        }

        return false;
    }

    /**
     * Load activity with the given date and type.
     * @param int $id
     * @return bool
     */
    public function loadFromDate(
        int $day, int $month, int $year, int $idle=0
    ):bool
    {
        $idle_where = "";

        if($idle)
        {
            $idle_where .= "and idle > 0 ";
        }

        $statement = $this->database->prepare(
            "select * from inactivity_log "
            . "where "
            . "day = ? and "
            . "month = ? and "
            . "and year = ? "
            , $idle_where
        );

        $statement->execute(array($day, $month, $year));

        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        if($data)
        {
            foreach($data as $name=>$value)
            {
                $this->$name = $value;
            }

            return true;
        }

        return false;
    }

    /**
     * Updates the inactivity based on the value of its current properties.
     * @return bool
     */
    public function update():bool
    {
        if($this->id == 0)
            return false;

        $statement = $this->database->prepare(
            "update inactivity_log set "
            . "idle = ?,"
            . "seconds = ?,"
            . "day_from = ?,"
            . "month_from = ?,"
            . "year_from = ?,"
            . "day_to = ?,"
            . "month_to = ?,"
            . "year_to = ?,"
            . "from_timestamp = ?,"
            . "to_timestamp = ? "
            . "where id = ?"
        );

        return $statement->execute(
            array(
                $this->idle,
                $this->seconds,
                $this->day_from,
                $this->month_from,
                $this->year_from,
                $this->day_to,
                $this->month_to,
                $this->year_to,
                $this->from_timestamp,
                $this->to_timestamp,
                $this->id
            )
        );
    }

    /**
     * Deletes the current activity/application.
     * @return bool
     */
    public function delete():bool
    {
        if($this->id == 0)
            return false;

        $statement = $this->database->prepare(
            "delete from inactivity_log where id = ?"
        );

        $return = $statement->execute(
            array(
                $this->id
            )
        );

        $this->id = 0;

        return $return;
    }
}