<?

App::import('Controller', 'Battles');

App::import('Model', 'Battle');
Mock::generate('Battle');
App::import('Model', 'Formation');
Mock::generate('Formation');

class BattlesControllerTest extends ControllerUnitTestCase {

    var $c;

    //---------------------------------------------------------------------------------------------
    function setUp () {
        $this->c = new BattlesController;
        $this->c->constructClasses();
        $this->c->Component->initialize($c);
        $this->c->GameAuth = $this->SetupMockAuth();
        $this->c->stopFof = true;
        $this->c->stopRedirect = true;

        $this->c->Battle = new MockBattle;
        $this->c->Formation = new MockFormation;
    }

    //---------------------------------------------------------------------------------------------
    function tearDown () {
        unset($this->c);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_Index_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'active_formation_id' => 2
            )
        ));

        // Mock model to return satisfying data
        $this->c->Formation->setReturnValue('GetFormations', array(
            0 => array(
                'Formation' => array(
                    'id' => 1,
                ),
            ),
            1 => array(
                'Formation' => array(
                    'id' => 2,
                ),
            ),
            2 => array(
                'Formation' => array(
                    'id' => 3,
                ),
            ),
        ));

        $this->c->index(SOME_NUM);
        $this->assertTrue($this->c->viewVars['activeFormation']['Formation']['id'] == 2);
        $this->assertTrue(count($this->c->viewVars['userFormations']) == 2);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_History_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        $this->c->history();
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_Fight_SparSucceeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 5);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 5
            )
        ));

        // Mock model to return satisfying data
        $this->c->data = array(
            'Battle' => array(
                'battle_type' => 'spar',
                'defender_name' => 'defendName',
                'attacker_id' => 2,
            ),
        );
        $this->c->Formation->setReturnValue('find', array(
            'Formation' => array(
                'id' => 3,
            ),
        ));
        $this->c->Formation->setReturnValue('GetFormation', array(
            'Formation' => array(
                'name' => 'defendName',
                'id' => 3,
                'user_id' => 4,
                'can_spar' => 1
            ),
        ), array(3));
        $this->c->Formation->setReturnValue('GetFormation', array(
            'Formation' => array(
                'name' => 'attackName',
                'id' => 2,
                'user_id' => 5,
            ),
        ), array(2));
        $this->c->Formation->setReturnValue('GetFormation', array());


        $this->c->fight();
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->GetFlashMessage() === false);
    }

    //---------------------------------------------------------------------------------------------
    function test_Fight_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 8);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 8
            )
        ));

        // Mock model to return satisfying data
        $this->c->data = array(
            'Battle' => array(
                'battle_type' => 'spar',
                'defender_name' => 'defendName',
                'attacker_id' => 2,
            ),
        );
        $this->c->Formation->setReturnValue('find', array(
            'Formation' => array(
                'id' => 3,
            ),
        ));
        $this->c->Formation->setReturnValue('GetFormation', array(
            'Formation' => array(
                'name' => 'defendName',
                'id' => 3,
                'user_id' => 4,
                'can_spar' => 1
            ),
        ), array(3));
        $this->c->Formation->setReturnValue('GetFormation', array(
            'Formation' => array(
                'name' => 'attackName',
                'id' => 2,
                'user_id' => 5,
            ),
        ), array(2));
        $this->c->Formation->setReturnValue('GetFormation', array());


        $this->expectError('Attacking formation did not belong to authed user.');
        $this->c->fight();
    }

    //---------------------------------------------------------------------------------------------
    function test_Fight_SparIncompleteData () {
        $this->c->data = array(
            'Battle' => array(
            ),
        );
        $this->expectError('Form data incomplete.');
        $this->c->fight();

        $this->c->data = array(
            'Battle' => array(
                'battle_type' => 'spar',
                'defender_name' => 'f',
            ),
        );
        $this->expectError('Form data incomplete.');
        $this->c->fight();

        $this->c->data = array(
            'Battle' => array(
                'battle_type' => 'spar',
                'attacker_id' => 3,
            ),
        );
        $this->expectError('Form data incomplete.');
        $this->c->fight();
    }

    //---------------------------------------------------------------------------------------------
    function test_Fight_NoData () {
        $this->c->fight();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Fight_InvalidCSRFToken () {
        $this->setupCSRFTokenTest();
        $this->c->fight();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_Matchmake_Succeeds () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->matchmake(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_Matchmake_UserIdMismatch () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->matchmake(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Matchmake_NoData () {
        $this->c->disableAjaxCheck = true;
        $this->c->matchmake();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Matchmake_InvalidCSRFToken () {
        $this->c->disableAjaxCheck = true;
        $this->setupCSRFTokenTest();
        $this->c->matchmake();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Matchmake_FailedSave () {
        $this->c->disableAjaxCheck = true;
        // Mock model to fail save

        $this->c->matchmake();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_FightResult_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->fight_result(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_FightResult_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->fight_result(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_FightResult_NonexistentObject () {
        // Insert mock model code here
        // Mock model to always return false

        $this->c->fight_result(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_FightResult_MissingArgument () {
        $this->c->fight_result();
        $this->assertTrue($this->c->didFof);
    }

}

?>