<?php

class ArmyController extends AppController {

    var $uses = array('User');

    //---------------------------------------------------------------------------------------------
    function index () {
        $user = $this->User->find('first', array(
            'conditions' => array(
                'User.id' => $this->GameAuth->GetLoggedInUserId(),
            ),
            'contain' => array(
                'Character' => array(
                    'CClass',
                ),
            ),
        ));
        $this->set('user', $user);
    }
}
?>