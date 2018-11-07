<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus\WM;

/**
 * Functions to retrieve active windows.
 */
class Manager
{
    /**
     * Static functions class.
     */
    private function __construct(){}

    /**
     * Gets a list of all opened windows.
     * @return \InFocus\WM\Window[]
     */
    public static function getAllWindows()
    {
        $windows = array();

        $output = array();

        $last_window = exec("wmctrl -lpx", $output);

        if(!$last_window)
            throw new \Exception(
                "Could not retrieve the windows list."
            );

        foreach($output as $window)
        {
            $window_object = new Window;

            $windows[] = $window_object->setWindowFromWmCtrl($window);
        }

        return $windows;
    }

    /**
     * Returns the currently active window.
     * @return \InFocus\WM\Window
     */
    public static function getActiveWindow():Window
    {
        $window = new Window;

        $window_id = exec(
            "xprop -root "
            . "| grep _NET_ACTIVE_WINDOW "
            . "| head -n 1 "
            . "| cut -d\" \" -f5"
        );

        if(!$window_id)
        {
            print "Desktop session not found.\n"
                . "Ending the activity logger.\n"
            ;

            exit(1);
        }

        $window_id = str_replace("0x", "", trim(trim($window_id, ",")));
        $window_id = "0x" . str_pad($window_id, 8, "0", STR_PAD_LEFT);

        $window_string = `wmctrl -lpx | grep $window_id`;

        if(!$window_string)
            throw new \Exception(
                "Could not retrieve the current window."
            );

        $window->setWindowFromWmCtrl($window_string);

        return $window;
    }
}
