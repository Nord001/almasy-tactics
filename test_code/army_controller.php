<?php

class ArmyController extends AppController {

    var $uses = array('User', 'CClass');

    //---------------------------------------------------------------------------------------------
    function index () {
        $characterIds = $this->User->Character->GetCharacterIdsByUserId($this->GameAuth->GetLoggedInUserId());
        $characters = $this->User->Character->GetCharactersEx($characterIds);
        $this->set('characters', $characters);
    }
}
?>