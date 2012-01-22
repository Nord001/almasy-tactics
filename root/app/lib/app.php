<?

define('DB_FORMAT', 'Y-m-d H:i:s');
define('ERROR_STR', 'Sorry, an error has occurred :(');

uses('security');

//----------------------------------------------------------------------------------------------
function GetVersionNumber () {
    $versionNumber = @file_get_contents(ROOT . '/VERSION');
    return $versionNumber !== false ? $versionNumber : 'N/A';
}

//---------------------------------------------------------------------------------------------
function LocationsFromBitLocation ($location) {
    $locations = array();
    for ($i = 0; $i < 9; $i++) {
        if ($location >> $i & 1)
            $locations[] = $i + 1;
    }
    return $locations;
}

//---------------------------------------------------------------------------------------------
function BitLocationFromLocations ($locations) {
    $location = 0;
    for ($i = 0; $i < count($locations); $i++) {
        $location = $location | (1 << ($locations[$i] - 1));
    }
    return $location;
}

//---------------------------------------------------------------------------------------------
// Double quotes needed around [ and ]
// So you need four, so php can escape two of them...
function EscapeJQuerySelector ($str) {
    $str = str_replace('[', '\\\\[', $str);
    $str = str_replace(']', '\\\\]', $str);

    return $str;
}

//---------------------------------------------------------------------------------------------
function NumberToString ($num) {
    if (intval($num) == $num)
        return sprintf("%+d", $num);
    else
        return sprintf("%+.1f", $num);
}

//---------------------------------------------------------------------------------------------
// Turns an array of key -> value into an array of (key, value) pairs.
function AssocArrayToValues ($input) {
    $array = array();
    foreach ($input as $key => $value)
        $array[] = array($key, $value);
    return $array;
}

//---------------------------------------------------------------------------------------------
function ArraysToKeyValuePair ($labels, $values) {
    return AssocArrayToValues (array_combine($labels, $values));
}

//---------------------------------------------------------------------------------------------
function ClearDir ($dir) {
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                @unlink($dir . '/' . $file);
            }
        }
        closedir($handle);
    }
}

//---------------------------------------------------------------------------------------------
function NewlinesToHtmlBreaks ($str, $numBreaks = 2) {
    $br = str_repeat('<br />', $numBreaks);
    return str_replace("\n\r", $br, $str);
}

//---------------------------------------------------------------------------------------------
// Verifies that $var is either a numeric value, or a nonempty array that contains only numeric
// values.
function CheckNumeric ($var) {

    $isNumeric = false;
    if (is_array($var)) {
        if (empty($var)) {
            $isNumeric = false;
        } else {
            $isNumeric = true;
            foreach ($var as $val)
                if (!is_numeric($val))
                    $isNumeric = false;
        }

    }

    if (is_numeric($var))
        $isNumeric = true;

    if ($isNumeric)
        return true;

    IERR('Variable that was supposed to be numeric was not.');
    return false;
}

//---------------------------------------------------------------------------------------------
function GenerateRandomString ($length) {
    $randstr = "";
    for($i=0; $i<$length; $i++){
        $randnum = mt_rand(0,61);
        if($randnum < 10){
            $randstr .= chr($randnum+48);
        }else if($randnum < 36){
            $randstr .= chr($randnum+55);
        }else{
            $randstr .= chr($randnum+61);
        }
    }
    return $randstr;
}

//---------------------------------------------------------------------------------------------
function BuildCookie ($userId, $passwordHash, $expiration, $ip) {
    $cookieData = $userId . '|' . $passwordHash . '|' . $expiration . '|' . $ip;
    $cookieData = base64_encode($cookieData);
    $cookieData = Security::cipher($cookieData, USER_COOKIE_KEY);
    return $cookieData;
}

//---------------------------------------------------------------------------------------------
function DecipherCookie ($cookieData) {
    $cookieData = Security::cipher($cookieData, USER_COOKIE_KEY);
    $cookieData = base64_decode($cookieData);
    $data = explode('|', $cookieData);
    if (count($data) != 4)
        return false;
    return $data;
}

?>