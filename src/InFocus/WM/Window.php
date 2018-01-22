<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus\WM;

/**
 * Defines a window that is known as an activity or application.
 */
class Window
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $title;

    /**
     * @var int
     */
    public $process_id;

    /**
     * @var string
     */
    public $process_name;

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
    public $desktop_number;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->id = "";
        $this->name = "";
        $this->title = "";
        $this->process_id = 0;
        $this->process_name = "";
        $this->icon_name = "";
        $this->icon_path = "";
        $this->desktop_number = -1;
    }

    /**
     * Populates the window data from parsing a line result returned by
     * the 'wmctrl -lpx' command.
     * @param string $line
     * @return \InFocus\WM\Window
     */
    public function setWindowFromWmCtrl(string $line):Window
    {
        if(trim($line) == "")
        {
            throw new \Exception("Wrong window");
        }

        $hostname = gethostname() . " ";

        if(count(explode($hostname, $line, 2)) > 1)
        {
            $elements_raw = explode($hostname, $line, 2)[0];
            $elements = preg_split("/\s+/", $elements_raw);
        }
        else
        {
            $hostname = "N/A ";
            $elements_raw = explode($hostname, $line, 2)[0];
            $elements = preg_split("/\s+/", $elements_raw);
        }

        $this->id = $elements[0];

        $this->name = explode(".", $elements_raw)[1];

        $this->title = trim(explode($hostname, $line, 2)[1]);

        $this->process_id = $elements[2];

        $this->process_name = preg_split(
            "/\s+/",
            trim(exec("ps -p {$this->process_id} -o command"))
        )[0];

        $this->process_name = end(explode("/", $this->process_name));

        $current_icon_theme = "";
        if($_SERVER["DESKTOP_SESSION"] == "xfce")
        {
            $current_icon_theme = trim(
                exec("xfconf-query -c xsettings -p /Net/IconThemeName")
            );
        }
        else
        {
            $current_icon_theme = trim(
                exec("gsettings get org.gnome.desktop.interface icon-theme")
            );
        }

        $current_icon_theme = trim($current_icon_theme, "'");

        $this->setIconFromTheme($current_icon_theme);

        if($this->icon_path == "" && $current_icon_theme != "hicolor")
        {
            $this->setIconFromTheme("hicolor");
        }

        // If icon not found try finding one with the window name.
        if($this->icon_path == "")
        {
            $previus_process_name = $this->process_name;

            $this->process_name = strtolower($this->name);

            $this->setIconFromTheme($current_icon_theme);

            if($this->icon_path == "" && $current_icon_theme != "hicolor")
            {
                $this->setIconFromTheme("hicolor");
            }

            if($this->icon_path == "" && trim($previus_process_name) != "")
            {
                $this->process_name = $previus_process_name;
            }
        }

        $this->desktop_number = $elements[1];

        return $this;
    }

    public function setIconFromTheme(string $theme):Window
    {
        if(
            file_exists(
                "/usr/share/applications/".$this->process_name.".desktop"
            )
        )
        {
            $icon = exec(
                "cat /usr/share/applications/\"{$this->process_name}\".desktop "
                . "| grep Icon="
            );

            $this->icon_name = trim(explode("Icon=", $icon)[1]);

            $icon_path = exec(
                "find "
                . "/usr/share/icons/'$theme' "
                . "-name \"{$this->icon_name}.*\" "
                . "| sort -h "
            );

            $this->icon_path = $icon_path ?? "";
        }

        return $this;
    }
}