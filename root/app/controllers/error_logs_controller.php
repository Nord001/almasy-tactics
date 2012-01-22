<?

class ErrorLogsController extends AppController {

    //---------------------------------------------------------------------------------------------
    function admi_index () {
        $errorList = $this->ErrorLog->GetErrorList();
        $this->set('errorList', $errorList);

        $numRecentErrors = $this->ErrorLog->GetNumRecentErrors();
        $this->set('numRecentErrors', $numRecentErrors);
    }

    //---------------------------------------------------------------------------------------------
    function admi_view ($id) {
        $error = $this->ErrorLog->GetError($id);

        // Filter out weird characters from cookie.
        if (isset($error['ErrorLog']['cookie'])) {
            $str = '';
            for ($i = 0; $i < strlen($error['ErrorLog']['cookie']); $i++) {
                if (ord($error['ErrorLog']['cookie'][$i]) < 127)
                    $str .= $error['ErrorLog']['cookie'][$i];
            }
            $error['ErrorLog']['cookie'] = $str;
        }
        $this->set('error', $error, true);

        $this->set('errorUser', $this->User->GetUser($error['ErrorLog']['user_id']));
    }
}

?>