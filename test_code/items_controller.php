<?php

class ItemsController extends AppController {

    var $uses = array('UserItem', 'Item', 'Imbue', 'Character', 'RefineTable');

    //=============================================================================================
    // Item functions
    //=============================================================================================

    //---------------------------------------------------------------------------------------------
    // Most logic is in each of the actions in the tab
    function index () {
        $this->__setData();
    }

    // Organize page functions

    //---------------------------------------------------------------------------------------------
    // Sets the data for the organize page, called by all ajax functions and the organize page.
    function __setData () {
        // Get all items
        $userItemIds = $this->Item->UserItem->GetUserItemIdsByUser($this->GameAuth->GetLoggedInUserId());
        $userItems = $this->Item->UserItem->GetUserItems($userItemIds);
        $this->set('userItems', $userItems);

        // Get characters
        $characterIds = $this->Character->GetCharacterIdsByUserId($this->GameAuth->GetLoggedInUserId());
        $characters = $this->Character->GetCharacters($characterIds);
        $this->set('characters', $characters);

        // Get class equip ability, since this doesn't change
        $classWeaponEquips = $this->Character->CClass->GetWeaponEquipInfo();
        $this->set('classWeaponEquips', $classWeaponEquips);

        // Get refine chances
        $refineChances = $this->RefineTable->GetRefineChances();
        $this->set('refineChances', $refineChances);

        // Get imbues
        $weaponImbues = $this->Imbue->GetWeaponImbues();
        $armorImbues = $this->Imbue->GetArmorImbues();
        $this->set('weaponImbues', $weaponImbues);
        $this->set('armorImbues', $armorImbues);
    }

    //---------------------------------------------------------------------------------------------
    function unequip_item () {
        if ($this->ShouldUseAjax()) {
            if (!isset($this->params['form']['userItemId'])) {
                $this->autoRender = false;
                echo 'Error.';
                return;
            }

            $userItemId = $this->params['form']['userItemId'];
            $userItem = $this->Item->UserItem->GetUserItem($userItemId);

            $userId = $this->GameAuth->GetLoggedInUserId();
            if ($userItem['UserItem']['user_id'] != $userId) {
                $this->autoRender = false;
                echo 'Error.';
                return;
            }

            $this->Character->UnequipItem($userItemId);

            $this->__setData();

            return;
        }

        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    // Ajax call to equip item.
    function equip_item () {
        if ($this->ShouldUseAjax()) {
            if (!isset($this->params['form']['userItemId']) || !isset($this->params['form']['characterId'])) {
                $this->autoRender = false;
                echo 'Error.';
                return;
            }

            $userItemId = $this->params['form']['userItemId'];
            $characterId = $this->params['form']['characterId'];

            $userItem = $this->Item->UserItem->GetUserItem($userItemId);
            $character = $this->Character->GetCharacter($characterId);

            // Make sure both belong to logged in user
            $userId = $this->GameAuth->GetLoggedInUserId();
            if ($character['Character']['user_id'] != $userId || $userItem['UserItem']['user_id'] != $userId) {
                $this->autoRender = false;
                echo 'Error.';
                return;
            }

            // Find other character that might've had this item equipped and unequip it
            $this->Character->UnequipItem($userItemId);

            $this->Character->EquipItem($characterId, $userItemId);

            $this->__setData();
            return;
        }

        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function perform_refine () {
        if ($this->ShouldUseAjax()) {

            if (!isset($this->params['form']['userItemId'])) {
                echo 'Error';
                return;
            }

            $userItemId = $this->params['form']['userItemId'];

            $userItem = $this->UserItem->GetUserItem($userItemId);

            // Make sure item exists
            if ($userItem === false) {
                echo 'Error.';
                return;
            }

            // Make sure item belongs to logged in user
            $userId = $this->GameAuth->GetLoggedInUserId();
            if ($userItem['UserItem']['user_id'] != $userId) {
                echo 'Error.';
                return;
            }

            // Make sure item refine is sane
            $refineLevel = $userItem['UserItem']['refine'];
            if ($refineLevel < 0 || $refineLevel >= 10) {
                echo 'Error.';
                return;
            }

            $cost = $this->RefineTable->GetRefineCost($userItemId);
            if (!$this->User->DeductMoney($userId, $cost)) {
                echo 'Error.';
                return;
            }

            $refineChance = $this->RefineTable->GetRefineChanceForLevel($refineLevel);

            $success = (mt_rand() / mt_getrandmax() * 100) < $refineChance;
            if ($success) {
                $this->UserItem->id = $userItemId;
                $this->UserItem->fastSave('refine', $refineLevel + 1);

                // Reequip it to refresh the character cache
                $characterId = $this->Character->UnequipItem($userItemId);
                $this->Character->EquipItem($characterId, $userItemId);
            } else {
                $this->Character->UnequipItem($userItemId);
                $deleted = $this->UserItem->del($userItemId); // Ensure constraints allow this to happen
                $this->UserItem->ClearUserItemCacheByUser($userId);
            }

            $this->UserItem->ClearUserItemCache($userItemId);

            $this->__setData();

            $this->set('success', $success);
            return;
        }

        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function perform_imbue () {
        if ($this->ShouldUseAjax()) {

            if (!isset($this->params['form']['userItemId']) && !isset($this->params['form']['imbueId'])) {
                echo 'Error.';
                return;
            }

            $userItemId = $this->params['form']['userItemId'];
            $imbueId = $this->params['form']['imbueId'];

            $userItem = $this->UserItem->GetUserItem($userItemId);

            // Make sure item exists
            if ($userItem === false) {
                echo 'Error.';
                return;
            }

            // Make sure item belongs to logged in user
            $userId = $this->GameAuth->GetLoggedInUserId();
            if ($userItem['UserItem']['user_id'] != $userId) {
                echo 'Error.';
                return;
            }

            // Make sure user can pay for it
            $cost = $this->Imbue->GetImbueCost($userItemId);
            if (!$this->User->DeductMoney($userId, $cost)) {
                echo 'Error.';
                return;
            }

            // Make sure it's not unique
            if ($userItem['UserItem']['rarity'] == 'unique') {
                echo 'Error.';
                return;
            }

            // Make sure imbue is right type for item
            $imbueType = $this->Imbue->GetImbueType($imbueId);
            $itemType = $userItem['UserItem']['Item']['WeaponType']['id'] != '' ? 'weapon' : 'armor';
            if ($itemType != $imbueType) {
                echo 'Error.';
                return;
            }


            $imbueMods = $this->Imbue->RollImbue($imbueId);

            // Make sure imbue exists
            if (!$imbueMods) {
                echo 'Error.';
                return;
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

            $this->__setData();
            return;
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
                echo 'Error.';
                return;
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
            Configure::write('debug', 0);
            $this->autoRender = false;

            if (!isset($this->params['form']['userItemId'])) {
                echo 0;
                return;
            }

            $userItemId = $this->params['form']['userItemId'];

            $itemEntry = $this->UserItem->ItemCatalogEntry->GetItemEntry($userItemId);
            if ($itemEntry) {
                $result = $this->UserItem->ItemCatalogEntry->PurchaseItem($this->GameAuth->GetLoggedInUserId(), $userItemId);
                echo $result ? '1' : '0';
            } else {
                // This item's not for sale!
                $this->log(sprintf('%s attempted to buy item %s that was not for sale.', $this->GameAuth->GetLoggedInUserId(), $userItemId));
                echo '0'; // Error
            }
            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function sell_item () {
        if ($this->ShouldUseAjax()) {
            if (!isset($this->params['form']['userItemId'])) {
                echo 'Error.';
                return;
            }

            $userItemId = $this->params['form']['userItemId'];

            $userItem = $this->UserItem->GetUserItem($userItemId);

            // Make sure item belongs to logged in user
            $userId = $this->GameAuth->GetLoggedInUserId();
            if ($userItem['UserItem']['user_id'] != $userId) {
                echo 'Error.';
                return;
            }

            $result = $this->UserItem->ItemCatalogEntry->SellItem($userItemId);
            if (!$result) {
                echo 'Error.';
                return;
            }

            $this->__setData();
            return;
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
    function admin_index () {
        $this->set('items', $this->paginate('Item'));
    }

    //---------------------------------------------------------------------------------------------
    function admin_view ($id = null) {
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
            ),
        ));
        $this->set('item', $item);
    }

    //---------------------------------------------------------------------------------------------
    function admin_add () {
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
    }

    //---------------------------------------------------------------------------------------------
    function admin_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid item.');
            $this->redirect(array('controller' => 'admin_home', 'action' => 'admin_index'));
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
    }

    //---------------------------------------------------------------------------------------------
    function admin_delete ($id = null) {
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
