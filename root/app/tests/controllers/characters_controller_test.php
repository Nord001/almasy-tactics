<?

App::import('Controller', 'Characters');

App::import('Model', 'Character');
Mock::generate('Character');
App::import('Model', 'User');
Mock::generate('User');

class CharactersControllerTestCase extends ControllerUnitTestCase {

    var $c;

    //---------------------------------------------------------------------------------------------
    function setUp () {
        $this->c = new CharactersController;
        $this->c->stopFof = true;
        $this->c->constructClasses();
        $this->c->Component->initialize($c);
        $this->c->GameAuth = $this->SetupMockAuth();

        $this->c->Character = new MockCharacter();
        $this->c->Character->User = new MockUser();
    }

    //---------------------------------------------------------------------------------------------
    function tearDown () {
        unset($this->c);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_View_Succeeds () {
        $this->c->Character->setReturnValue('GetCharacterEx', array(
            'Character' => array(
                'user_id' => 2,
                'name' => 'name',
            ),
            'CClass' => array(
                'bonus_description' => 'desc',
            ),
        ));
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->view(SOME_NUM);
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_View_UserIdMismatch () {
        $this->c->Character->setReturnValue('GetCharacterEx', array(
            'Character' => array(
                'user_id' => 2,
            ),
        ));
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', SOME_NUM);
        $this->c->view(2);

        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_View_NonexistentObject () {
        $this->c->Character->setReturnValue('GetCharacterEx', false);
        $this->c->view(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_View_MissingArgument () {
        $this->c->view();
        $this->assertTrue($this->c->didFof);
    }

    //=============================================================================================

    /* FIX caching issue
    //---------------------------------------------------------------------------------------------
    function test_NewCharacter_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->new_character();
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_NewCharacter_InvalidCSRFToken () {
        $this->setupCSRFTokenTest();
        $this->c->new_character();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_NewCharacter_InsufficientMoney () {
        $this->c->data = array(
            'csrf_token' => 'tok',
        );
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        $this->c->new_character(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_NewCharacter_MissingArgument () {
        $this->c->new_character();
        $this->assertTrue($this->c->didFof);
    }
    */

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_RollNewCharacter_Succeeds () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        $this->c->Character->User->setReturnValue('DeductMoney', true);
        $this->c->Character->setReturnValue('RollNewCharacter', array('Character' => 'blah'));

        $this->c->roll_new_character();
        $this->assertTrue(isset($this->c->viewVars['character']));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_RollNewCharacter_NoAjax () {
        $this->c->roll_new_character();
        $this->assertTrue($this->c->didFof);
    }

//---------------------------------------------------------------------------------------------
    function test_RollNewCharacter_InsufficientMoney () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        $this->c->Character->User->setReturnValue('DeductMoney', false);

        $this->c->roll_new_character();
        $this->assertFalse(isset($this->c->viewVars['character']));
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_Levelup_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->levelup(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_Levelup_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->levelup(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Levelup_NoData () {
        $this->c->levelup();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Levelup_InvalidCSRFToken () {
        $this->setupCSRFTokenTest();
        $this->c->levelup();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Levelup_FailedSave () {
        // Mock model to fail save

        $this->c->levelup();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_ChangeClass_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->change_class(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_ChangeClass_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->change_class(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_ChangeClass_NoData () {
        $this->c->change_class();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_ChangeClass_InvalidCSRFToken () {
        $this->setupCSRFTokenTest();
        $this->c->change_class();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_ChangeClass_FailedSave () {
        // Mock model to fail save

        $this->c->change_class();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_Delete_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->delete(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_Delete_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->delete(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Delete_NoData () {
        $this->c->delete();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Delete_InvalidCSRFToken () {
        $this->setupCSRFTokenTest();
        $this->c->delete();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Delete_FailedSave () {
        // Mock model to fail save

        $this->c->delete();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

}

?>