<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus\Lists;

/**
 * Get various list of activity types.
 */
class Types extends \InFocus\ActivityDB
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Gets all the available types.
     * @return \InFocus\Element\Type[]
     */
    public function getAll()
    {
        $elements = array();

        $statement = $this->database->query(
            "select * from activity_type"
        );

        while($data = $statement->fetch(\PDO::FETCH_ASSOC))
        {
            $object = new \InFocus\Element\Type();

            foreach($data as $name => $value)
            {
                $object->$name = $value;
            }

            yield $object;
        }
    }

    /**
     * Gets a list of all types accompanied by its time usage:
     * type->total_time and type->usage_percent.
     * @param int $day
     * @param int $month
     * @param int $year
     * @return \InFocus\Element\Type[]
     */
    public function getWithTime(int $day=0, int $month=0, int $year=0):array
    {
        $elements = array();

        $where = "";
        if($day || $month || $year)
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
        }

        $statement = $this->database->query(
            "select A.id, A.name, A.description, A.tags, "
            . "sum(B.seconds) as total_time "
            . "from activity_type A left join activity_log B "
            . "on A.id = B.type "
            . $where
            . "group by A.name "
            . "order by total_time desc"
        );

        $first_element = true;
        $longest_element = 0;

        while($data = $statement->fetch(\PDO::FETCH_ASSOC))
        {
            $element = new \InFocus\Element\Activity();

            foreach($data as $name => $value)
            {
                $element->$name = $value;
            }

            if($first_element)
            {
                $longest_element = $element->total_time;
                $first_element = false;
            }

            $element->usage_percent =
                ($element->total_time / $longest_element)
                * 100
            ;

            $elements[] = $element;
        }

        return $elements;
    }

    /**
     * Get the best type match for a given window.
     * @param \InFocus\WM\Window $window
     * @param bool $try_parent If everything fails try to retrieve the type
     * of the application/activity that owns the window.
     * @return int Id of type.
     */
    public function getBestMatch(
        \InFocus\WM\Window $window,
        bool $try_parent=true
    ):int
    {
        $types = $this->getAll();

        $max_score = 0;
        $type_id = 0;

        foreach($types as $type)
        {
            $score = 0;

            $tags = preg_split("/\s+/", trim($type->tags));

            foreach($tags as $tag)
            {
                if($tag != "")
                {
                    $words_found = substr_count(
                        strtolower($window->title),
                        strtolower($tag)
                    );

                    $score += $words_found;

                    if($words_found)
                    {
                        $percent = 0;

                        similar_text(
                            strtolower($tag),
                            strtolower($window->title),
                            $percent
                        );

                        $score += $percent;
                    }
                }
            }

            if($score > $max_score)
            {
                $type_id = $type->id;
                $max_score = $score;
            }
        }

        if($max_score == 0)
        {
            if($try_parent)
            {
                $activity = new \InFocus\Element\Activity();
                $activity->loadFromBinaryName($window->process_name);

                $type_id = $activity->type;
            }
            else
            {
                $type_id = 1;
            }
        }

        return $type_id;
    }

    /**
     * Adds a new type.
     * @param \InFocus\Element\Type $type
     * @return bool
     */
    public function add(\InFocus\Element\Type $type)
    {
        $statement = $this->database->prepare(
            "insert into activity_type "
            . "("
            . "name, "
            . "description, "
            . "tags "
            . ") "
            . "values(?, ?, ?)"
        );

        return $statement->execute(
            array(
                $type->name,
                $type->description,
                $type->tags
            )
        );
    }
}