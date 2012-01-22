<?

App::import('Controller', 'Characters');

// Insert model import code here
App::import('Model', 'Character');
Mock::generate('Character');

class CharactersControllerTest extends ControllerUnitTestCase {

    var $c;

    //---------------------------------------------------------------------------------------------
    function setUp () {
        $this->c = new CharactersController;
	$this->c->constructClasses();
	$this->c->Component->initialize($c);
	$this->c->GameAuth = $this->SetupMockAuth();
	$this->c->stopFof = true;

	$this->c->Character = new MockCharacter;
    }

    //---------------------------------------------------------------------------------------------
    function tearDown () {
        unset($this->c);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_View_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->view(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_View_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->view(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_View_NonexistentObject () {
        // Insert mock model code here
        // Mock model to always return false

        $this->c->view(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_View_MissingArgument () {
        $this->c->view();
        $this->assertTrue($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_NewCharacter_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->new_character(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_NewCharacter_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->new_character(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_NewCharacter_NonexistentObject () {
        // Insert mock model code here
        // Mock model to always return false

        $this->c->new_character(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_NewCharacter_MissingArgument () {
        $this->c->new_character();
        $this->assertTrue($this->c->didFof);
    }

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

        // Mock model to return satisfying data

        $this->c->roll_new_character(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_RollNewCharacter_UserIdMismatch () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->roll_new_character(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_RollNewCharacter_NoData () {
        $this->c->disableAjaxCheck = true;
        $this->c->roll_new_character();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_RollNewCharacter_InvalidCSRFToken () {
        $this->c->disableAjaxCheck = true;
        $this->setupCSRFTokenTest();
        $this->c->roll_new_character();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_RollNewCharacter_FailedSave () {
        $this->c->disableAjaxCheck = true;
        // Mock model to fail save

        $this->c->roll_new_character();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
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