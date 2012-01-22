<?

App::import('Controller', 'Formations');

// Insert model import code here
App::import('Model', 'Formation');
Mock::generate('Formation');

class FormationsControllerTest extends ControllerUnitTestCase {

    var $c;

    //---------------------------------------------------------------------------------------------
    function setUp () {
        $this->c = new FormationsController;
        $this->c->constructClasses();
        $this->c->Component->initialize($c);
        $this->c->GameAuth = $this->SetupMockAuth();
        $this->c->stopFof = true;

        $this->c->Formation = new MockFormation;
    }

    //---------------------------------------------------------------------------------------------
    function tearDown () {
        unset($this->c);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_Index_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->index(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_Index_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->index(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Index_NonexistentObject () {
        // Insert mock model code here
        // Mock model to always return false

        $this->c->index(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Index_MissingArgument () {
        $this->c->index();
        $this->assertTrue($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_Rankings_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->rankings(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_Rankings_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->rankings(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Rankings_NonexistentObject () {
        // Insert mock model code here
        // Mock model to always return false

        $this->c->rankings(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Rankings_MissingArgument () {
        $this->c->rankings();
        $this->assertTrue($this->c->didFof);
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
    function test_Create_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->create(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_Create_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->create(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Create_NoData () {
        $this->c->create();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Create_InvalidCSRFToken () {
        $this->setupCSRFTokenTest();
        $this->c->create();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Create_FailedSave () {
        // Mock model to fail save

        $this->c->create();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_Edit_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->edit(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_Edit_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->edit(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Edit_NoData () {
        $this->c->edit();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Edit_InvalidCSRFToken () {
        $this->setupCSRFTokenTest();
        $this->c->edit();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Edit_FailedSave () {
        // Mock model to fail save

        $this->c->edit();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_MoveCharacter_Succeeds () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->move_character(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_MoveCharacter_UserIdMismatch () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->move_character(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_MoveCharacter_NoData () {
        $this->c->disableAjaxCheck = true;
        $this->c->move_character();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_MoveCharacter_InvalidCSRFToken () {
        $this->c->disableAjaxCheck = true;
        $this->setupCSRFTokenTest();
        $this->c->move_character();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_MoveCharacter_FailedSave () {
        $this->c->disableAjaxCheck = true;
        // Mock model to fail save

        $this->c->move_character();
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

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_SetActive_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->set_active(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_SetActive_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->set_active(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_SetActive_NoData () {
        $this->c->set_active();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_SetActive_InvalidCSRFToken () {
        $this->setupCSRFTokenTest();
        $this->c->set_active();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_SetActive_FailedSave () {
        // Mock model to fail save

        $this->c->set_active();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_Strategy_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->strategy(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_Strategy_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->strategy(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Strategy_NonexistentObject () {
        // Insert mock model code here
        // Mock model to always return false

        $this->c->strategy(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Strategy_MissingArgument () {
        $this->c->strategy();
        $this->assertTrue($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_CreateScript_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->create_script(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_CreateScript_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->create_script(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_CreateScript_NoData () {
        $this->c->create_script();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_CreateScript_InvalidCSRFToken () {
        $this->setupCSRFTokenTest();
        $this->c->create_script();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_CreateScript_FailedSave () {
        // Mock model to fail save

        $this->c->create_script();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_EditScript_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->edit_script(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_EditScript_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->edit_script(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_EditScript_NoData () {
        $this->c->edit_script();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_EditScript_InvalidCSRFToken () {
        $this->setupCSRFTokenTest();
        $this->c->edit_script();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_EditScript_FailedSave () {
        // Mock model to fail save

        $this->c->edit_script();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_DeleteScript_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->delete_script(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_DeleteScript_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->delete_script(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_DeleteScript_NoData () {
        $this->c->delete_script();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_DeleteScript_InvalidCSRFToken () {
        $this->setupCSRFTokenTest();
        $this->c->delete_script();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_DeleteScript_FailedSave () {
        // Mock model to fail save

        $this->c->delete_script();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_EditAi_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->edit_ai(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_EditAi_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->edit_ai(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_EditAi_NoData () {
        $this->c->edit_ai();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_EditAi_InvalidCSRFToken () {
        $this->setupCSRFTokenTest();
        $this->c->edit_ai();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_EditAi_FailedSave () {
        // Mock model to fail save

        $this->c->edit_ai();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

}

?>