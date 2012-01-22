<?

App::import('Controller', 'Messages');

// Insert model import code here
App::import('Model', 'Message');
Mock::generate('Message');

class MessagesControllerTest extends ControllerUnitTestCase {

    var $c;

    //---------------------------------------------------------------------------------------------
    function setUp () {
        $this->c = new MessagesController;
	$this->c->constructClasses();
	$this->c->Component->initialize($c);
	$this->c->GameAuth = $this->SetupMockAuth();
	$this->c->stopFof = true;

	$this->c->Message = new MockMessage;
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
    function test_Send_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->send(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_Send_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->send(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Send_NoData () {
        $this->c->send();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Send_InvalidCSRFToken () {
        $this->setupCSRFTokenTest();
        $this->c->send();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Send_FailedSave () {
        // Mock model to fail save

        $this->c->send();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_MarkRead_Succeeds () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->mark_read(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_MarkRead_UserIdMismatch () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->mark_read(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_MarkRead_NoData () {
        $this->c->disableAjaxCheck = true;
        $this->c->mark_read();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_MarkRead_InvalidCSRFToken () {
        $this->c->disableAjaxCheck = true;
        $this->setupCSRFTokenTest();
        $this->c->mark_read();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_MarkRead_FailedSave () {
        $this->c->disableAjaxCheck = true;
        // Mock model to fail save

        $this->c->mark_read();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_Clean_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->clean(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_Clean_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->clean(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Clean_NoData () {
        $this->c->clean();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Clean_InvalidCSRFToken () {
        $this->setupCSRFTokenTest();
        $this->c->clean();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Clean_FailedSave () {
        // Mock model to fail save

        $this->c->clean();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_MarkAllRead_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->mark_all_read(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_MarkAllRead_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->mark_all_read(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_MarkAllRead_NoData () {
        $this->c->mark_all_read();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_MarkAllRead_InvalidCSRFToken () {
        $this->setupCSRFTokenTest();
        $this->c->mark_all_read();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_MarkAllRead_FailedSave () {
        // Mock model to fail save

        $this->c->mark_all_read();
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