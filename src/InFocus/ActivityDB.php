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
class ActivityDB
{
    /**
     * @var \PDO
     */
    public $database;

    /**
     * @var \InFocus\Settings
     */
    public $settings;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->settings = new Settings("InFocus");

        $db_name = "activity_log.db";

        if(!file_exists($this->settings->home_directory . "/$db_name"))
        {
            $this->database = new \PDO(
                'sqlite:' . $this->settings->home_directory . "/$db_name"
            );

            $this->database->setAttribute(\PDO::ATTR_TIMEOUT, 30);

            $this->database->query("PRAGMA journal_mode=WAL");

            // applications table
            $this->database->exec(
                "create table activities ("
                . "id integer primary key autoincrement,"
                . "name text,"
                . "binary text,"
                . "description text,"
                . "type int,"
                . "icon_name text,"
                . "icon_path text"
                . ")"
            );

            $this->database->exec(
                "create index activities_index on activities ("
                . "id desc,"
                . "name desc,"
                . "binary desc,"
                . "type desc"
                . ")"
            );

            // activity_log table
            $this->database->exec(
                "create table activity_log ("
                . "id integer primary key autoincrement,"
                . "application_name text,"
                . "window_title text,"
                . "type int,"
                . "seconds int,"
                . "day int,"
                . "month int,"
                . "year int,"
                . "start_timestamp text,"
                . "last_activity_timestamp text"
                . ")"
            );

            $this->database->exec(
                "create index activity_log_index on activity_log ("
                . "id desc,"
                . "application_name desc,"
                . "window_title desc,"
                . "type desc,"
                . "seconds desc,"
                . "day desc,"
                . "month desc,"
                . "year desc,"
                . "start_timestamp desc,"
                . "last_activity_timestamp desc"
                . ")"
            );

            // activity_type table
            $this->database->exec(
                "create table activity_type ("
                . "id integer primary key autoincrement,"
                . "name text, "
                . "description text, "
                . "tags"
                . ")"
            );

            $this->database->exec(
                "create index activity_type_index on activity_type ("
                . "id desc,"
                . "name desc"
                . ")"
            );

            foreach(
                array(
                    "Unknown",
                    "Work",
                    "Research",
                    "Recreational"
                )
                as
                $type
            )
            {
                $this->database->exec(
                    "insert into activity_type "
                    . "(name) "
                    . "values ('$type')"
                );
            }

            // inactivity_log table
            $this->database->exec(
                "create table inactivity_log ("
                . "id integer primary key autoincrement,"
                . "idle int default 0,"
                . "seconds int,"
                . "day_from int,"
                . "month_from int,"
                . "year_from int,"
                . "day_to int,"
                . "month_to int,"
                . "year_to int,"
                . "from_timestamp text,"
                . "to_timestamp text"
                . ")"
            );

            $this->database->exec(
                "create index inactivity_log_index on inactivity_log ("
                . "id desc,"
                . "idle desc,"
                . "seconds desc,"
                . "day_from desc,"
                . "month_from desc,"
                . "year_from desc,"
                . "day_to desc,"
                . "month_to desc,"
                . "year_to desc,"
                . "from_timestamp desc,"
                . "to_timestamp desc"
                . ")"
            );
        }
        else
        {
            $this->database = new \PDO(
                'sqlite:' . $this->settings->home_directory . "/$db_name"
            );

            $this->database->setAttribute(\PDO::ATTR_TIMEOUT, 30);
        }

        // Set current running version in case we need
        // to update the database scheme on future releases.
        $this->settings->set("version", Info::$version);
    }
}