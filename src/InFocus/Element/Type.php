<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus\Element;

/**
 * Represents an application or activity type.
 */
class Type extends \InFocus\ActivityDB
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
    public $tags;

    /**
     * @var string
     */
    public $description;

    /**
     * Tries to load a type from the given id.
     * @param int $id
     */
    public function __construct(int $id=0)
    {
        parent::__construct();

        $this->id = 0;

        if($id)
            $this->loadFromId($id);
    }

    /**
     * Load type with the given id.
     * @param int $id
     * @return bool
     */
    public function loadFromId($id):bool
    {
        $statement = $this->database->prepare(
            "select * from activity_type where id = ?"
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
     * Load type with the given name.
     * @param string $name
     * @return bool
     */
    public function loadFromName(string $name):bool
    {
        $statement = $this->database->prepare(
            "select * from activity_type where name = ?"
        );

        $statement->execute(array($name));

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
     * Updates the type based on the value of its current properties.
     * @return bool
     */
    public function update():bool
    {
        if($this->id == 0)
            return false;

        $statement = $this->database->prepare(
            "update activity_type set "
            . "name = ?, "
            . "description = ?, "
            . "tags = ? "
            . "where id = ?"
        );

        return $statement->execute(
            array(
                $this->name,
                $this->description,
                $this->tags,
                $this->id
            )
        );
    }

    /**
     * Deletes the current type.
     * @return bool
     */
    public function delete():bool
    {
        if($this->id == 0)
            return false;

        $statement = $this->database->prepare(
            "delete from activity_type where id = ?"
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