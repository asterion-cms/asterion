<?php
/**
* @class Date
*
* This is a helper class to manage dates in general.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Date {

    /**
    * Return an array with the names of the months.
    */
    public static function arrayMonths() {
        return array(1=>'january', 2=>'february', 3=>'march', 4=>'april', 5=>'may', 6=>'june', 7=>'july', 8=>'august', 9=>'september', 10=>'october', 11=>'november', 12=>'december');
    }

    /**
    * Return the days of a month in a certain year.
    */
    public static function daysMonth($month, $year) {
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    /**
    * Return the next month of a date.
    */
    public static function nextMonth($month, $year) {
        return ($month>=12) ? array('month'=>1, 'year'=>$year+1) : array('month'=>$month+1, 'year'=>$year);
    }

    /**
    * Return the previous month of a date.
    */
    public static function prevMonth($month, $year) {
        return ($month<=1) ? array('month'=>12, 'year'=>$year-1) : array('month'=>$month-1, 'year'=>$year);
    }

    /**
    * Return the text label of a month.
    */
    public static function textMonth($month) {
        $months = Date::arrayMonths();
        return __($months[__(intval($month))]);
    }

    /**
    * Return an array with the label of the months.
    */
    public static function textMonthArray() {
        $months = array();
        foreach (range(1,12) as $month) {
            $months[$month] = Date::textMonth($month);
        }
        return $months;
    }

    /**
    * Return an array with the first three letters of the label of the months.
    */
    public static function textMonthArraySimple() {
        $months = array();
        foreach (range(1,12) as $month) {
            $months[$month] = substr(html_entity_decode(Date::textMonth($month)), 0, 3);
        }
        return $months;
    }

    /**
    * Convert an SQL date into an associative array.
    */
    public static function sqlArray($date, $trim=true) {
        $result = array();
        $result['day'] = ($trim==true) ? intval(ltrim(substr($date, 8, 2),'0')) : intval(substr($date, 8, 2));
        $result['month'] = ($trim==true) ? intval(ltrim(substr($date, 5, 2),'0')) : intval(substr($date, 5, 2));
        $result['year'] = intval(substr($date, 0, 4));
        $result['hour'] = intval(substr($date, 11, 2));
        $result['minutes'] = intval(substr($date, 14, 2));
        return $result;
    }
    
    /**
    * Return an SQL date into a URL usable string.
    */
    public static function sqlArrayUrl($date) {
        $result = Date::sqlArray($date);
        return $result['day'].'-'.$result['month'].'-'.$result['year'].'-'.$result['hour'].'-'.$result['minutes'];
    }

    /**
    * Get the date of a SQL formatted string.
    */
    public static function sqlDate($date) {
        return substr($date, 0, 10);
    }

    /**
    * Get the day of a SQL formatted string.
    */
    public static function sqlDay($date) {
        return substr($date, 8, 2);
    }

    /**
    * Get the month of a SQL formatted string.
    */
    public static function sqlMonth($date) {
        return substr($date, 5, 2);
    }

    /**
    * Get the year of a SQL formatted string.
    */
    public static function sqlYear($date) {
        return substr($date, 0, 4);
    }

    /**
    * Get the time of a SQL formatted string.
    */
    public static function sqlTime($date) {
        return substr($date, 11, 5);
    }

    /**
    * Get the day and the month of a SQL formatted string.
    */
    public static function sqlDayMonth($date) {
        return Date::sqlDay($date).'-'.Date::sqlMonth($date);
    }

    /**
    * Convert an URL date into an associative array.
    */
    public static function urlArraySql($url) {
        $urlArray = explode('-', $url);
        $result = array();
        $result['day'] = (isset($urlArray[0])) ? $urlArray[0] : '';
        $result['month'] = (isset($urlArray[1])) ? $urlArray[1] : '';
        $result['year'] = (isset($urlArray[2])) ? $urlArray[2] : '';
        $result['hour'] = (isset($urlArray[3])) ? $urlArray[3] : '';
        $result['minutes'] = (isset($urlArray[4])) ? $urlArray[4] : '';
        return $result;
    }

    /**
    * This function converts a CSV file into a set of arrays.
    */
    public static function sqlInt($date) {
        $date = Date::sqlArray($date);
        return mktime($date['hour'], $date['minutes'], 0, $date['month'], $date['day'], $date['year']);
    }

    /**
    * Convert a SQL formatted date into a label text, hour is optional.
    */
    public static function sqlText($date, $withHour=false) {
        if ($date!='') {
            $dateArray = Date::sqlArray($date);
            if ($dateArray['day']!='') {
                $html = $dateArray['day'].' '.Date::textMonth($dateArray['month']).', '.$dateArray['year'];
                $html .= ($withHour) ? ' '.str_pad($dateArray['hour'], 2, "0", STR_PAD_LEFT).':'.str_pad($dateArray['minutes'], 2, "0", STR_PAD_LEFT) : '';
                return $html;
            }
        }
    }

    /**
    * Convert a SQL formatted date into a small label text, hour is optional.
    */
    public static function sqlTextSmall($date, $withHour=false) {
        if ($date!='') {
            $dateArray = Date::sqlArray($date);
            $html = $dateArray['day'].' '.substr(Date::textMonth($dateArray['month']),0,3);
            $html .= ($withHour) ? ' | '.str_pad($dateArray['hour'], 2, "0", STR_PAD_LEFT).':'.str_pad($dateArray['minutes'], 2, "0", STR_PAD_LEFT) : '';
            return $html;
        }
    }

    /**
    * Convert a SQL formatted date into a simple text, hour is optional.
    */
    public static function sqlTextSimple($date, $withHour=0) {
        if ($date!='') {
            $dateArray = Date::sqlArray($date);
            $html = Text::dateNumber($dateArray['day']).'-'.Text::dateNumber($dateArray['month']).'-'.$dateArray['year'];
            $html .= ($withHour==1) ? ' '.$dateArray['hour'].':'.$dateArray['minutes'] : '';
            return $html;
        }
    }

    /**
    * Get the hour of a SQL formatted date.
    */
    public static function sqlHour($date) {
        if ($date!='') {
            $dateArray = Date::sqlArray($date);
            return $dateArray['hour'].':'.$dateArray['minutes'];
        }
    }

    /**
    * Load the date from a POST formatted value.
    */
    public static function postFormat($postValue) {
        return str_pad($_POST[$postValue.'yea'], 2, "0", STR_PAD_LEFT).'-'.str_pad($_POST[$postValue.'mon'], 2, "0", STR_PAD_LEFT).'-'.str_pad($_POST[$postValue.'day'], 2, "0", STR_PAD_LEFT);
    }
    
    /**
    * Create a text label from a date array.
    */
    public static function arrayText($date, $withHour=0) {
        if (is_array($date)) {
            $html = $date['day'].' '.Date::textMonth($date['month']).', '.$date['year'];
            $html .= ($withHour==1) ? ' | '.$dateArray['hour'].':'.$dateArray['minutes'] : '';
            return $html;
        }
    }

    /**
    * Convert an SQL formatted date into a URL text string.
    */
    public static function sqlUrl($date) {
        if ($date!='') {
            $dateArray = Date::sqlArray($date);
            $html = intval($dateArray['year']).'_'.intval($dateArray['month']).'_'.$dateArray['day'];
            return $html;
        }
    }
    
    /**
    * Calculate the minutes using hours.
    */
    public static function minutes($hour, $minutes=0) {
        return intval($hour)*60 + intval($minutes);
    }

    /**
    * Calculate the integer difference of two dates.
    */
    public static function differenceInt($dateStart, $dateEnd) {
        $start = Date::sqlInt($dateStart);
        $end = Date::sqlInt($dateEnd);
        return $end - $start;
    }

    /**
    * Calculate a difference associative array of two dates.
    */
    public static function difference($dateStart, $dateEnd) {
        $difference = Date::differenceInt($dateStart, $dateEnd);
        $result = array();
        $result['hours'] = $difference/3600;
        $result['minutes'] = $difference/60;
        $result['days'] = $difference/86400;
        return $result;
    }
    
    /**
    * Format a publication date for RSS files.
    */
    public static function pubDate($date) {
        $values = Date::sqlArray($date);
        return date('D, d M Y H:i:s O', mktime($values['hour'], $values['minutes'], 0, $values['month'], $values['day'], $values['year']));
    }
    
    /**
    * Format today's publication date for RSS files.
    */
    public static function pubDateToday() {
        return date('D, d M Y H:i:s O');
    }

}
?>