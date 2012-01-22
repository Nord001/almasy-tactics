<?

Mock::generate('GameAuth');

abstract class ControllerUnitTestCase extends UnitTestCase {

    //---------------------------------------------------------------------------------------------
    function SetupMockAuth () {
        $auth = new MockGameAuthComponent();
        //$auth->setReturnValue('GetLoggedInUserId', false);
        //$auth->setReturnValue('GetLoggedInUser', array());
        return $auth;
    }

    //---------------------------------------------------------------------------------------------
    function GetFlashMessage () {
        $flashMessage = $this->c->Session->read('Message.flash');
        if (!is_string($flashMessage) || empty($flashMessage))
            return false;
        return $flashMessage;
    }

    //---------------------------------------------------------------------------------------------
    function FlashMessageContains ($str) {
        $flashMessage = $this->GetFlashMessage();
        if ($flashMessage === false)
            return false;
        return stripos($flashMessage, $str) !== false;
    }


    //---------------------------------------------------------------------------------------------
    function FlashMessageLacks ($str) {
        $flashMessage = $this->GetFlashMessage();
        if ($flashMessage === false)
            return true;

        return stripos($flashMessage, $str) === false;
    }

    //---------------------------------------------------------------------------------------------
    function setupCSRFTokenTest () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'csrf_token' => 'string',
            ),
        ));
        $this->c->data['csrf_token'] = 'string2';
    }
}
?>