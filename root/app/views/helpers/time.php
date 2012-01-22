<?php

class TimeHelper extends AppHelper {

    //---------------------------------------------------------------------------------------------
    function GetHourMinuteSecondString ($seconds) {
        $days = intval($seconds / 3600 / 24);
        $seconds -= $days * 3600 * 24;

        $hours = intval($seconds / 3600);
        $seconds -= $hours * 3600;

        $minutes = intval($seconds / 60);
        $seconds -= $minutes * 60;

        $str = '';
        if ($days != 0)
            $str .= $days . 'd ';
        if ($hours != 0)
            $str .= $hours . 'h ';
        if ($minutes != 0)
            $str .= $minutes . 'm ';
        if ($seconds != 0)
            $str .= $seconds . 's';

        return trim($str);
    }

    //---------------------------------------------------------------------------------------------
    function GetTimeAgoString ($referencedate=0, $timepointer='', $measureby='', $autotext=true){    // Measureby can be: s, m, h, d, or y
        if (!CheckNumeric($referencedate))
            return false;
            
        if($timepointer == '') $timepointer = time();
        $Raw = $timepointer-$referencedate;    // Raw time difference
        $Clean = abs($Raw);
        $calcNum = array(array('s', 60), array('m', 60*60), array('h', 60*60*60), array('d', 60*60*60*24), array('y', 60*60*60*24*365));    // Used for calculating
        $calc = array('s' => array(1, 'sec'), 'm' => array(60, 'min'), 'h' => array(60*60, 'hour'), 'd' => array(60*60*24, 'day'), 'y' => array(60*60*24*365, 'year'));    // Used for units and determining actual differences per unit (there probably is a more efficient way to do this)

        if($measureby == ''){    // Only use if nothing is referenced in the function parameters
            $usemeasure = 's';    // Default unit

            for($i=0; $i<count($calcNum); $i++){    // Loop through calcNum until we find a low enough unit
                if($Clean <= $calcNum[$i][1]){        // Checks to see if the Raw is less than the unit, uses calcNum b/c system is based on seconds being 60
                    $usemeasure = $calcNum[$i][0];    // The if statement okayed the proposed unit, we will use this friendly key to output the time left
                    $i = count($calcNum);            // Skip all other units by maxing out the current loop position
                }
            }
        }else{
            $usemeasure = $measureby;                // Used if a unit is provided
        }

        $datedifference = floor($Clean/$calc[$usemeasure][0]);    // Rounded date difference

        $prospect = '';
        if($autotext==true && ($timepointer==time())){
            if($Raw < 0){
                $prospect = ' from now';
            }else{
                $prospect = ' ago';
            }
        }

        if($referencedate != 0){        // Check to make sure a date in the past was supplied
            $str = $datedifference . ' ' . $calc[$usemeasure][1] . ($datedifference == 1 ? '' : 's') . ' ' . $prospect;

            if ($datedifference > 14 && $usemeasure == 'd')
                return date('M. j', $referencedate);
            else
                return $str;
        }else{
            return '';
        }
    }
}
?>
