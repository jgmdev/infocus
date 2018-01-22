<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace Utils;

/**
 * Helper functions for handling dates.
 */
class Date
{

/**
 * static function that returns a days of the month array ready
 * for selects on generate form functions.
 */
static function getDays()
{
    $dates = array();

    for($i = 1; $i <= 31; $i++)
    {
        $dates[$i] = $i;
    }

    return $dates;
}

/**
 * static function that returns a months array ready for selects
 * on generate form functions.
 */
static function getMonths()
{
    $months = array(
        "January" => 1,
        "February" => 2,
        "March" => 3,
        "April" => 4,
        "May" => 5,
        "June" => 6,
        "July" => 7,
        "August" => 8,
        "September" => 9,
        "October" => 10,
        "November" => 11,
        "December" => 12
    );

    return $months;
}

/**
 * static function that returns a years array ready for
 * selects on generate form functions.
 * @param int $additional_years
 */
static function getYears($additional_years=0)
{
    $current_year = date("Y", time());
    $current_year += $additional_years;

    $years = array();

    for($i = 1900; $i <= $current_year; $i++)
    {
        $years[$i] = $i;
    }

    arsort($years);

    return $years;
}

/**
 * Get the amount of time in a easy to read human format.
 * @param int $fromtimestamp Should be lower number than $totimestamp
 * @param int $totimestamp Should be higher number than $fromtimestamp
 * @return string
 */
static function getElapsedTime($fromtimestamp, $totimestamp=0)
{
    if($totimestamp == 0)
        $totimestamp = time();

    $etime = $totimestamp - $fromtimestamp;

    if($etime < 1)
    {
        return '0 seconds';
    }

    $a = array(
        12 * 30 * 24 * 60 * 60 => array('year', 'years'),
        30 * 24 * 60 * 60 => array('month', 'months'),
        24 * 60 * 60 => array('day', 'days'),
        60 * 60 => array('hour', 'hours'),
        60 => array('minute', 'minutes'),
        1 => array('second', 'seconds')
    );

    foreach($a as $secs => $labels)
    {
        $d = $etime / $secs;

        if($d >= 1)
        {
            $time = round($d);

            if($time > 1)
                $period = $labels[1];
            else
                $period = $labels[0];

            return str_replace(
                array("{time}", "{period}"),
                array($time, $period),
                '{time} {period} ago'
            );
        }
    }
}

/**
 * Get the amount of time in a easy to read human format.
 * @param int $fromtimestamp Should be lower number than $totimestamp
 * @param int $totimestamp Should be higher number than $fromtimestamp
 * @return string
 */
static function getHumanTime($fromtimestamp)
{
    if(intval($fromtimestamp) == 0)
    {
        return "0 seconds";
    }

    $a = array(
        12 * 30 * 24 * 60 * 60 => array('year', 'years'),
        30 * 24 * 60 * 60 => array('month', 'months'),
        24 * 60 * 60 => array('day', 'days'),
        60 * 60 => array('hour', 'hours'),
        60 => array('minute', 'minutes'),
        1 => array('second', 'seconds')
    );

    foreach($a as $secs => $labels)
    {
        $d = $fromtimestamp / $secs;

        if($d >= 1)
        {
            $time = round($d);

            if($time > 1)
                $period = $labels[1];
            else
                $period = $labels[0];

            return str_replace(
                array("{time}", "{period}"),
                array($time, $period),
                '{time} {period}'
            );
        }
    }
}

/**
 * Get the amount of days from one timestamp to the other.
 * @param int $fromtimestamp Should be lower number than $totimestamp
 * @param int $totimestamp Should be higher number than $fromtimestamp
 * @return int
 */
static function getElapsedDays($fromtimestamp, $totimestamp=0)
{
    if($totimestamp == 0)
        $totimestamp = time();

    $etime = $totimestamp - $fromtimestamp;

    if($etime < 1)
    {
        return 0;
    }

    $days = $etime / (24 * 60 * 60);

    if($days >= 1)
    {
        return round($days);
    }

    return 0;
}

}