<?

App::import('Controller', 'Items');

// Insert model import code here
App::import('Model', 'Item');
Mock::generate('Item');

class ItemsControllerTest extends ControllerUnitTestCase {

    var $c;

    //---------------------------------------------------------------------------------------------
    function setUp () {
        $this->c = new ItemsController;
        $this->c->constructClasses();
        $this->c->Component->initialize($c);
        $this->c->GameAuth = $this->SetupMockAuth();
        $this->c->stopFof = true;

        $this->c->Item = new MockItem;
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
    function test_UnequipItem_Succeeds () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->unequip_item(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_UnequipItem_UserIdMismatch () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->unequip_item(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_UnequipItem_NoData () {
        $this->c->disableAjaxCheck = true;
        $this->c->unequip_item();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_UnequipItem_InvalidCSRFToken () {
        $this->c->disableAjaxCheck = true;
        $this->setupCSRFTokenTest();
        $this->c->unequip_item();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_UnequipItem_FailedSave () {
        $this->c->disableAjaxCheck = true;
        // Mock model to fail save

        $this->c->unequip_item();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_EquipItem_Succeeds () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->equip_item(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_EquipItem_UserIdMismatch () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->equip_item(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_EquipItem_NoData () {
        $this->c->disableAjaxCheck = true;
        $this->c->equip_item();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_EquipItem_InvalidCSRFToken () {
        $this->c->disableAjaxCheck = true;
        $this->setupCSRFTokenTest();
        $this->c->equip_item();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_EquipItem_FailedSave () {
        $this->c->disableAjaxCheck = true;
        // Mock model to fail save

        $this->c->equip_item();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_PerformRefine_Succeeds () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->perform_refine(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_PerformRefine_UserIdMismatch () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->perform_refine(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_PerformRefine_NoData () {
        $this->c->disableAjaxCheck = true;
        $this->c->perform_refine();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_PerformRefine_InvalidCSRFToken () {
        $this->c->disableAjaxCheck = true;
        $this->setupCSRFTokenTest();
        $this->c->perform_refine();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_PerformRefine_FailedSave () {
        $this->c->disableAjaxCheck = true;
        // Mock model to fail save

        $this->c->perform_refine();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_PerformImbue_Succeeds () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->perform_imbue(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_PerformImbue_UserIdMismatch () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->perform_imbue(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_PerformImbue_NoData () {
        $this->c->disableAjaxCheck = true;
        $this->c->perform_imbue();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_PerformImbue_InvalidCSRFToken () {
        $this->c->disableAjaxCheck = true;
        $this->setupCSRFTokenTest();
        $this->c->perform_imbue();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_PerformImbue_FailedSave () {
        $this->c->disableAjaxCheck = true;
        // Mock model to fail save

        $this->c->perform_imbue();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_Store_Succeeds () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->store(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_Store_UserIdMismatch () {
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->store(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Store_NonexistentObject () {
        // Insert mock model code here
        // Mock model to always return false

        $this->c->store(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_Store_MissingArgument () {
        $this->c->store();
        $this->assertTrue($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_ViewItems_Succeeds () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->view_items(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_ViewItems_UserIdMismatch () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->view_items(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_BuyItem_Succeeds () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->buy_item(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_BuyItem_UserIdMismatch () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->buy_item(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_BuyItem_NoData () {
        $this->c->disableAjaxCheck = true;
        $this->c->buy_item();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_BuyItem_InvalidCSRFToken () {
        $this->c->disableAjaxCheck = true;
        $this->setupCSRFTokenTest();
        $this->c->buy_item();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_BuyItem_FailedSave () {
        $this->c->disableAjaxCheck = true;
        // Mock model to fail save

        $this->c->buy_item();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function test_SellItem_Succeeds () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 1);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 1
            )
        ));

        // Mock model to return satisfying data

        $this->c->sell_item(SOME_NUM);
        $this->assertFalse($this->c->didFof);
        $this->assertTrue($this->FlashMessageLacks("error"));
    }

    //---------------------------------------------------------------------------------------------
    function test_SellItem_UserIdMismatch () {
        $this->c->disableAjaxCheck = true;
        $this->c->GameAuth->setReturnValue('GetLoggedInUserId', 2);
        $this->c->GameAuth->setReturnValue('GetLoggedInUser', array(
            'User' => array(
                'id' => 2
            )
        ));

        // Insert mock model code here to set user id to be something different

        $this->c->sell_item(SOME_NUM);
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_SellItem_NoData () {
        $this->c->disableAjaxCheck = true;
        $this->c->sell_item();
        $this->assertTrue($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_SellItem_InvalidCSRFToken () {
        $this->c->disableAjaxCheck = true;
        $this->setupCSRFTokenTest();
        $this->c->sell_item();
        $this->assertTrue($this->FlashMessageContains('error'));
        $this->assertFalse($this->c->didFof);
    }

    //---------------------------------------------------------------------------------------------
    function test_SellItem_FailedSave () {
        $this->c->disableAjaxCheck = true;
        // Mock model to fail save

        $this->c->sell_item();
        $this->assertTrue($this->FlashMessageContains("error"));
        $this->assertFalse($this->c->didFof);
    }

}

?>