<?php

class ArmyController extends AppController {

    var $uses = array('User', null);

    //---------------------------------------------------------------------------------------------
    function index () {
        $this->setPageTitle('Your Army');

        $characterIds = $this->User->Character->GetCharacterIdsByUserId($this->GameAuth->GetLoggedInUserId());
        $characters = $this->User->Character->GetCharactersEx($characterIds);
        $this->set('characters', $characters);
    }
}
?>