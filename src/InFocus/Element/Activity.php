<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus\Element;

/**
 * Represents an activity or application.
 */
class Activity extends \InFocus\ActivityDB
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $binary

    /**
     * @var string
     */;
    public $description;

    /**
     * @var int
     */
    public $type;

    /**
     * @var string
     */
    public $icon_name;

    /**
     * @var string
     */
    public $icon_path;

    /**
     * @var int
     */
    protected $previous_type;

    /**
     * Tries to load the activity from a process name.
     * @param string $binary_name
     */
    public function __construct(string $binary_name="")
    {
        parent::__construct();

        $this->id = 0;

        if($binary_name)
            $this->loadFromBinaryName($binary_name);
    }

    /**
     * Load activity with the given id.
     * @param int $id
     * @return bool
     */
    public function loadFromId(int $id):bool
    {
        $statement = $this->database->prepare(
            "select * from activities where id = ?"
        );

        $statement->execute(array($id));

        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        if($data)
        {
            foreach($data as $name=>$value)
            {
                $this->$name = $value;
            }

            $this->previous_type = $this->type;

            return true;
        }

        return false;
    }

    /**
     * Load activity with the given binary name.
     * @param string $binary_name
     * @return bool
     */
    public function loadFromBinaryName(string $binary_name):bool
    {
        $statement = $this->database->prepare(
            "select * from activities where binary = ?"
        );

        $statement->execute(array($binary_name));

        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        if($data)
        {
            foreach($data as $name=>$value)
            {
                $this->$name = $value;
            }

            $this->previous_type = $this->type;

            return true;
        }

        return false;
    }

    /**
     * Updates the activity based on the value of its current properties.
     * @return bool
     */
    public function update():bool
    {
        if($this->id == 0)
            return false;

        $statement = $this->database->prepare(
            "update activities set "
            . "name = ?,"
            . "binary = ?,"
            . "description = ?,"
            . "type = ?,"
            . "icon_name = ?,"
            . "icon_path = ? "
            . "where id = ?"
        );

        if(
            $statement->execute(
                array(
                    $this->name,
                    $this->binary,
                    $this->description,
                    $this->type,
                    $this->icon_name,
                    $this->icon_path,
                    $this->id
                )
            )
        )
        {
            if($this->previous_type == 1)
            {
                $statement = $this->database->prepare(
                    "update activity_log set "
                    . "type = ? "
                    . "where application_name = ? and "
                    . "type = ? "
                );

                $return = $statement->execute(
                    array($this->type, $this->binary, $this->previous_type)
                );

                $this->previous_type = $this->type;

                return $return;
            }
        }

        return false;
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
            "delete from activities where id = ?"
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