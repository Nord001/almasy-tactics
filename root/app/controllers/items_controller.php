<?php

class ItemsController extends AppController {

    var $uses = array('UserItem', 'Item', 'Imbue', 'Character', 'RefineTable');

    //=============================================================================================
    // Item functions
    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    function index () {
        $this->setPageTitle('Armory');
        $weaponImbueList = $this->Imbue->GetWeaponImbues();
        $weaponImbues = array();
        foreach ($weaponImbueList as $id => $value)
            $weaponImbues[] = array('id' => $id, 'name' => $value);

        $armorImbueList = $this->Imbue->GetArmorImbues();
        $armorImbues = array();
        foreach ($armorImbueList as $id => $value)
            $armorImbues[] = array('id' => $id, 'name' => $value);

        $this->set('weaponImbues', $weaponImbues);
        $this->set('armorImbues', $armorImbues);

        $refineChances = $this->RefineTable->GetRefineChances();
        $this->set('refineChances', $refineChances);

        // Get class equip ability, since this doesn't change
        $classWeaponEquips = $this->Character->CClass->GetWeaponEquipInfo();
        $this->set('classWeaponEquips', $classWeaponEquips);

        $weaponTypes = $this->Item->WeaponType->GetWeaponTypes();
        $weaponSprites = Set::combine($weaponTypes, '{n}.WeaponType.id', '{n}.WeaponType.sprite');
        $armorTypes = $this->Item->ArmorType->GetArmorTypes();
        $armorSprites = Set::combine($armorTypes, '{n}.ArmorType.id', '{n}.ArmorType.sprite');
        $miscItemTypes = $this->Item->MiscItemType->GetMiscItemTypes();
        $miscItemSprites = Set::combine($miscItemTypes, '{n}.MiscItemType.id', '{n}.MiscItemType.sprite');

        $this->set('weaponSprites', $weaponSprites);
        $this->set('armorSprites', $armorSprites);
        $this->set('miscItemSprites', $miscItemSprites);
    }

    //---------------------------------------------------------------------------------------------
    function get_items () {
        if (!$this->ShouldUseAjax())
            $this->fof();

        Configure::write('ajaxMode', 1);
        $this->autoRender = false;

        $user = $this->GameAuth->GetLoggedInUser();

        $userItemIds = $this->Item->UserItem->GetUserItemIdsByUser($user['User']['id']);
        $userItems = $this->Item->UserItem->GetUserItems($userItemIds);

        foreach ($userItems as $key => $userItem) {
            if (@$userItem['UserItem']['CharacterEquipped']['id'] != '')
                unset($userItems[$key]);
        }

        $userItems = array_values($userItems);

        return json_encode($userItems);
    }

    //---------------------------------------------------------------------------------------------
    function unequip_item () {
        if (!$this->ShouldUseAjax()) {
            $this->fof();
            return;
        }

        Configure::write('ajaxMode', 1);
        $this->autoRender = false;

        $userItemId = @$this->params['form']['userItemId'];
        $userItem = $this->Item->UserItem->GetUserItem($userItemId);

        $userId = $this->GameAuth->GetLoggedInUserId();
        if ($userItem['UserItem']['user_id'] != $userId) {
            $this->autoRender = false;
            return AJAX_ERROR_CODE;
        }

        $this->Character->UnequipItem($userItemId);
    }

    //---------------------------------------------------------------------------------------------
    // Ajax call to equip item.
    function equip_item () {
        if (!$this->ShouldUseAjax()) {
            $this->fof();
            return;
        }

        Configure::write('ajaxMode', 1);
        $this->autoRender = false;

        $userItemId = @$this->params['form']['userItemId'];
        $characterId = @$this->params['form']['characterId'];

        $userItem = $this->Item->UserItem->GetUserItem($userItemId);
        $character = $this->Character->GetCharacter($characterId);

        // Make sure both belong to logged in user
        $userId = $this->GameAuth->GetLoggedInUserId();
        if ($character['Character']['user_id'] != $userId || $userItem['UserItem']['user_id'] != $userId) {
            return AJAX_ERROR_CODE;
        }

        // Find other character that might've had this item equipped and unequip it
        $this->Character->UnequipItem($userItemId);

        $this->Character->EquipItem($characterId, $userItemId);
    }

    //---------------------------------------------------------------------------------------------
    function perform_refine () {
        if ($this->ShouldUseAjax()) {
            $this->autoRender = false;
            Configure::write('ajaxMode', 1);

            $userItemId = @$this->params['form']['userItemId'];

            $userItem = $this->UserItem->GetUserItem($userItemId);

            // Make sure item exists
            if ($userItem === false) {
                IERR('User item to be refined doesn\'t exist.');
                return AJAX_ERROR_CODE;
            }

            // Make sure item belongs to logged in user
            $userId = $this->GameAuth->GetLoggedInUserId();
            if ($userItem['UserItem']['user_id'] != $userId) {
                IERR('User item to be refined doesn\'t belong to authed user.');
                return AJAX_ERROR_CODE;
            }

            // Make sure item refine is sane
            $refineLevel = $userItem['UserItem']['refine'];
            if ($refineLevel < 0 || $refineLevel >= MAX_REFINE) {
                IERR('User item to be refined cannot be refined anymore.');
                return AJAX_ERROR_CODE;
            }

            $cost = $this->RefineTable->GetRefineCost($userItemId);
            if (!$this->User->DeductMoney($userId, $cost)) {
                return AJAX_INSUFFICIENT_FUNDS;
            }

            $refineChance = $this->RefineTable->GetRefineChanceForLevel($refineLevel);

            $success = (mt_rand() / mt_getrandmax() * 100) < $refineChance;
            if ($success) {
                $this->UserItem->id = $userItemId;
                $this->UserItem->fastSave('refine', $refineLevel + 1);

                // Reequip it to refresh the character cache
                $characterId = $this->Character->UnequipItem($userItemId);
                if ($characterId !== false)
                    $this->Character->EquipItem($characterId, $userItemId);
            } else {
                $this->Character->UnequipItem($userItemId);
                $deleted = $this->UserItem->del($userItemId); // Ensure constraints allow this to happen
                $this->UserItem->ClearUserItemCacheByUser($userId);
            }

            $this->UserItem->ClearUserItemCache($userItemId);

            return $success ? 1 : 0;
        }

        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function perform_imbue () {
        if ($this->ShouldUseAjax()) {
            Configure::write('ajaxMode', 1);
            $this->autoRender = false;

            $userItemId = @$this->params['form']['userItemId'];
            $imbueId = @$this->params['form']['imbueId'];

            $userItem = $this->UserItem->GetUserItem($userItemId);

            // Make sure item exists
            if ($userItem === false) {
                IERR('User item to be imbued doesn\'t exist.');
                return AJAX_ERROR_CODE;
            }

            // Make sure user can pay for it
            $userId = $this->GameAuth->GetLoggedInUserId();
            $cost = $this->Imbue->GetImbueCost($userItemId);
            if (!$this->User->DeductMoney($userId, $cost)) {
                return AJAX_INSUFFICIENT_FUNDS;
            }

            // Make sure item belongs to logged in user
            if ($userItem['UserItem']['user_id'] != $userId) {
                IERR('User item to be imbued doesn\'t belong to user.');
                return AJAX_ERROR_CODE;
            }

            // Make sure it's not unique
            if ($userItem['UserItem']['rarity'] == 'unique') {
                IERR('User item was unique.');
                return AJAX_ERROR_CODE;
            }

            // Make sure imbue is right type for item
            $imbueType = $this->Imbue->GetImbueType($imbueId);
            $itemType = $userItem['UserItem']['Item']['WeaponType']['id'] != '' ? 'weapon' : 'armor';
            if ($itemType != $imbueType) {
                IERR('Imbue was wrong type for item.');
                return AJAX_ERROR_CODE;
            }

            $imbueMods = $this->Imbue->RollImbue($imbueId);

            // Make sure imbue exists
            if (!$imbueMods) {
                IERR('Imbue rolled no mods.');
                return AJAX_ERROR_CODE;
            }

            // Delete old mods that aren't native
            $success = $this->UserItem->ItemMod->deleteAll(array(
                'ItemMod.user_item_id' => $userItemId,
                'ItemMod.native'       => 0,
            ));

            // Save new mods
            foreach ($imbueMods as $mod) {
                $mod['ItemMod']['user_item_id'] = $userItemId;
                $this->UserItem->ItemMod->create();
                $success &= $this->UserItem->ItemMod->save($mod);
            }

            // Make it imbued
            $this->UserItem->id = $userItemId;
            $success &= $this->UserItem->saveField('rarity', 'imbued');
            $this->Imbue->id = $imbueId;
            $success &= $this->UserItem->saveField('name', $this->Imbue->field('name'));

            // Reequip it to refresh the character cache
            $characterId = $this->Character->UnequipItem($userItemId);
            if ($characterId !== false)
                $this->Character->EquipItem($characterId, $userItemId);

            $this->UserItem->ClearUserItemCache($userItemId);
            $this->UserItem->ItemMod->ClearItemModsCache($userItemId);

            if (!$success) {
                IERR('Saving imbue failed.');
                return AJAX_ERROR_CODE;
            }

            return AJAX_SUCCESS;
        }

        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function store () {
        $weaponTypes = $this->Item->WeaponType->GetWeaponTypes();
        $armorTypes = $this->Item->ArmorType->GetArmorTypes();

        $this->set('weaponTypes', $weaponTypes);
        $this->set('armorTypes', $armorTypes);
    }

    //---------------------------------------------------------------------------------------------
    // View for showing items of a type.
    function view_items () {
        if ($this->ShouldUseAjax()) {
            if (!isset($this->params['form']['type']) || !isset($this->params['form']['typeId'])) {
                $this->autoRender = false;
                IERR('Form data incomplete.');
                return AJAX_ERROR_CODE;
            }

            $type = $this->params['form']['type'];
            $typeId = $this->params['form']['typeId'];

            if ($type == 'weapon')
                $typeName = $this->Item->WeaponType->GetWeaponTypeName($typeId);
            elseif ($type == 'armor')
                $typeName = $this->Item->ArmorType->GetArmorTypeName($typeId);


            $userItemIds = $this->Item->UserItem->ItemCatalogEntry->GetUserItemIdsByType($type, $typeId);
            $items = $this->Item->UserItem->ItemCatalogEntry->GetItemEntries($userItemIds);

            $this->set('items', $items);
            $this->set('itemType', $type);
            $this->set('typeName', $typeName);
            $this->set('itemTypeId', $typeId);
            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function buy_item () {
        if ($this->ShouldUseAjax()) {
            Configure::write('ajaxMode', 1);
            $this->autoRender = false;

            if (!isset($this->params['form']['userItemId'])) {
                IERR('Form data incomplete.');
                return AJAX_ERROR_CODE;
            }

            $userItemId = $this->params['form']['userItemId'];

            $itemEntry = $this->UserItem->ItemCatalogEntry->GetItemEntry($userItemId);
            if ($itemEntry !== false) {
                $result = $this->UserItem->ItemCatalogEntry->PurchaseItem($this->GameAuth->GetLoggedInUserId(), $userItemId);
                return ($result ? AJAX_SUCCESS : AJAX_INSUFFICIENT_FUNDS);
            } else {
                // This item's not for sale!
                IERR('Attempted to buy item that was not for sale.');
                return AJAX_ERROR_CODE;
            }
            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function sell_item () {
        if ($this->ShouldUseAjax()) {
            Configure::write('ajaxMode', 1);
            $this->autoRender = false;

            $userItemId = @$this->params['form']['userItemId'];
            $userItem = $this->UserItem->GetUserItem($userItemId);
            if ($userItem === false) {
                IERR('Item didn\'t exist.');
                return AJAX_ERROR_CODE;
            }

            // Make sure item belongs to logged in user
            $userId = $this->GameAuth->GetLoggedInUserId();
            if ($userItem['UserItem']['user_id'] != $userId) {
                $this->autoRender = false;
                IERR('Item didn\'t belong to user.');
                return AJAX_ERROR_CODE;
            }

            try {
                $this->UserItem->ItemCatalogEntry->SellItem($userItemId,
                                                            @$this->params['form']['quantity']);
            } catch (AppException $e) {
                return AJAX_ERROR_CODE;
            }

            return AJAX_SUCCESS;
        }
        $this->fof();
    }

    //=============================================================================================
    // Admin functions
    //=============================================================================================

    var $paginate = array(
        'Item' => array(
            'limit' => 0,
            'contain' => array(
                'WeaponType',
                'ArmorType',
            ),
        ),
    );

    //---------------------------------------------------------------------------------------------
    function admi_index () {
        $this->set('items', $this->paginate('Item'));
    }

    //---------------------------------------------------------------------------------------------
    function admi_view ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid item.');
            $this->redirect(array('controller' => 'admin_home', 'action' => 'index'));
        }
        $item = $this->Item->find('first', array(
            'conditions' => array(
                'Item.id' => $id,
            ),
            'contain' => array(
                'ArmorType',
                'WeaponType',
                'MiscItemType',
            ),
        ));
        $this->set('item', $item);
    }

    //---------------------------------------------------------------------------------------------
    function admi_add () {
        if (!empty($this->data)) {

            $this->Item->create();
            if ($this->Item->save($this->data)) {
                $this->Session->setFlash('Item saved.');
                $this->redirect(array('action' => 'view', $this->Item->id));
            } else {
                $this->Session->setFlash('Could not save item.');
            }
        }

        $weaponTypes = $this->Item->WeaponType->find('list');
        $this->set('weaponTypes', $weaponTypes);
        $armorTypes = $this->Item->ArmorType->find('list');
        $this->set('armorTypes', $armorTypes);
        $miscItemTypes = $this->Item->MiscItemType->find('list');
        $this->set('miscItemTypes', $miscItemTypes);
    }

    //---------------------------------------------------------------------------------------------
    function admi_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid item.');
            $this->redirect(array('controller' => 'admin_home', 'action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->Item->save($this->data)) {
                $this->Session->setFlash('Item saved.');
                $this->redirect(array('action' => 'view', $this->Item->id));
            } else {
                $this->Session->setFlash('Could not save item.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Item->read(null, $id);
        }
        $weaponTypes = $this->Item->WeaponType->find('list');
        $this->set('weaponTypes', $weaponTypes);
        $armorTypes = $this->Item->ArmorType->find('list');
        $this->set('armorTypes', $armorTypes);
        $miscItemTypes = $this->Item->MiscItemType->find('list');
        $this->set('miscItemTypes', $miscItemTypes);
    }

    //---------------------------------------------------------------------------------------------
    function admi_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for item.');
            $this->redirect(array('controller' => 'admin_home', 'action' => 'index'));
        }
        if ($this->Item->del($id)) {
            $this->Session->setFlash('Item deleted.');
            $this->redirect(array('controller' => 'users', 'action' => 'index'));
        } else {
            $this->Session->setFlash('Could not delete item.');
            $this->redirect($this->referer());
        }
    }

}
?>
