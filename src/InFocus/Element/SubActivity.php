<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus\Element;

/**
 * Represents an activity that is/was exerted inside an application.
 */
class SubActivity extends \InFocus\ActivityDB
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $application_name;

    /**
     * @var string
     */
    public $window_title;

    /**
     * @var int
     */
    public $type;

    /**
     * @var int
     */
    public $seconds;

    /**
     * @var int
     */
    public $day;

    /**
     * @var int
     */
    public $month;

    /**
     * @var int
     */
    public $year;

    /**
     * @var int
     */
    public $start_timestamp;

    /**
     * @var int
     */
    public $last_activity_timestamp;

    /**
     * Tries to load the sub-activity with the given binary name.
     * @param string $binary_name
     * @return
     */
    public function __construct(string $binary_name="")
    {
        parent::__construct();

        $this->id = 0;

        if($binary_name)
            $this->loadFromBinaryName($binary_name);
    }

    /**
     * Load sub-activity with the given id.
     * @param int $id
     * @return bool
     */
    public function loadFromId($id):bool
    {
        $statement = $this->database->prepare(
            "select * from activity_log where id = ?"
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
     * Load sub-activity with the given process name and window title.
     * @param string $activity_name
     * @param string $window_title
     * @return bool
     */
    public function loadFromNameAndTitle(
        string $activity_name, string $window_title
    ):bool
    {
        $statement = $this->database->prepare(
            "select * from activity_log "
            . "where application_name = ? and window_title = ?"
        );

        $statement->execute(array($activity_name, $window_title));

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
     * Updates the sub-activity based on the value of its current properties.
     * @return bool
     */
    public function update():bool
    {
        if($this->id == 0)
            return false;

        $statement = $this->database->prepare(
            "update activity_log set "
            . "type = ? "
            . "where "
            . "application_name = ? and "
            . "window_title = ?"
        );

        return $statement->execute(
            array(
                $this->type,
                $this->application_name,
                $this->window_title
            )
        );
    }

    /**
     * Deletes the current sub-activity.
     * @return bool
     */
    public function delete():bool
    {
        if($this->id == 0)
            return false;

        $statement = $this->database->prepare(
            "delete from activity_log where id = ?"
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