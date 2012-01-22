<?

class GameValidateComponent extends Object {
    var $disableStartup = true;

    //--------------------------------------------------------------------------------------------
    function IsValidCharacterName ($name) {
        if (preg_match('/^[a-z0-9 ]*$/', strtolower($name)) == 0)
            return false;

        if (strlen($name) == 0)
            return false;

        return true;
    }

    //--------------------------------------------------------------------------------------------
    function IsValidFormationName ($name) {
        if (preg_match('/^[a-z0-9 \']*$/', strtolower($name)) == 0)
            return false;

        if (strlen($name) == 0)
            return false;

        return true;
    }

    //--------------------------------------------------------------------------------------------
    function IsValidEmail ($email) {
        if (preg_match('/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/', $email) == 0)
            return false;
        if (strlen($email) == 0)
            return false;

        return true;
    }
};

?>