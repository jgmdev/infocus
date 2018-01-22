<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus;

class Settings
{
    /**
     * @var array
     */
    public $settings;

    /**
     * @var string
     */
    public $home_directory;

    /**
     * @var string
     */
    public $settings_file;

    /**
     * @var string
     */
    public $application_name;

    /**
     * Constructor.
     * @param string $application_name
     */
    public function __construct(string $application_name)
    {
        $this->settings = array();
        $this->home_directory = "";
        $this->settings_file = "";
        $this->application_name = "";

        $this->load($application_name);
    }

    public function load(string $application_name)
    {
        $this->application_name = trim($application_name);

        $this->settings = array();

        if(isset($_SERVER["LOCALAPPDATA"])) //Windows
        {
            $this->home_directory = $_SERVER["HOME"]
                . "/.config/" . $this->application_name
            ;
        }
        elseif(isset($_SERVER["HOME"])) //Unix
        {
            $this->home_directory = $_SERVER["HOME"]
                . "/.config/" . $this->application_name
            ;
        }
        else
        {
            $username = posix_getpwuid(posix_geteuid())["name"];

            $this->home_directory = "/home/" . $username
                . "/.config/" . $this->application_name
            ;
        }

        if(!file_exists($this->home_directory))
        {
            mkdir($this->home_directory);
        }

        $this->settings_file = $this->home_directory . "/settings.ini";

        if(file_exists($this->settings_file))
        {
            $this->settings = parse_ini_file($this->settings_file, true);
        }
    }

    public function get(string $valueName, string $default="")
    {
        if(isset($this->settings[$valueName]))
        {
            return $this->settings[$valueName];
        }

        return $default;
    }

    public function getSectionValue(
        string $sectionName, string $valueName, string $default=""
    )
    {
        if(isset($this->settings[$sectionName]))
        {
            if(isset($this->settings[$sectionName][$valueName]))
            {
                return $this->settings[$sectionName][$valueName];
            }
        }

        return $default;
    }

    public function set(string $valueName, $value)
    {
        $this->settings[$valueName] = $value;

        $this->WriteINI();
    }

    public function setSectionValue(string $sectionName, string $valueName, $value)
    {
        $this->settings[$sectionName][$valueName] = $value;

        $this->WriteINI();
    }

    public function writeINI()
    {
        $content = "";

        foreach($this->settings as $key=>$data)
        {
            if(is_array($data))
            {
                $is_section = true;

                foreach($data as $dataKey=>$dataValues)
                {
                    if(is_long($dataKey))
                    {
                        $is_section = false;
                        break;
                    }
                }

                $content .= "\n";

                //Write global array value
                if(!$is_section)
                {
                    foreach($data as $dataKey=>$dataValue)
                    {
                        $content .= $key . '[] = "' . $dataValue . '"' . "\n";
                    }
                }

                //Write section
                else
                {
                    $content .= "[" . $key . "]\n";

                    foreach($data as $dataKey=>$dataValue)
                    {
                        if(is_array($dataValue))
                        {
                            foreach($dataValue as $dataInnerValue)
                            {
                                $content .= $dataKey . '[] = "' . $dataInnerValue . '"' . "\n";
                            }
                        }
                        else
                        {
                            $content .= $dataKey . ' = "' . $dataValue . '"' . "\n";
                        }
                    }
                }

                $content .= "\n";
            }

            //Write global value
            else
            {
                $content .= $key . ' = "' . $data . '"' . "\n";
            }
        }

        file_put_contents($this->settings_file, $content);
    }
}