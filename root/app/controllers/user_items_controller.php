<?php

class UserItemsController extends AppController {

    var $pageTitle = 'Users';

    //---------------------------------------------------------------------------------------------
    function admi_view ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid item.');
            $this->redirect(array('controller' => 'admin_home', 'action' => 'index'));
        }
        $item = $this->UserItem->find('first', array(
            'conditions' => array(
                'UserItem.id' => $id,
            ),
            'contain' => array(
                'User',
                'Item',
                'ItemMod' => array(
                    'BonusType',
                ),
            ),
        ));
        $this->set('item', $item);
    }

    //---------------------------------------------------------------------------------------------
    function admi_add ($userId = null) {
        if (!empty($this->data)) {
            $this->UserItem->create();
            if ($this->UserItem->save($this->data)) {
                $this->Session->setFlash('Item saved.');
                $this->redirect(array('action' => 'view', $this->UserItem->id));
            } else {
                $this->Session->setFlash('Could not save item.');
                // Set user id to be able to return to page
                $userId = $this->data['Item']['user_id'];
            }
        }
        if ($userId == null){
            $this->fof();
            return;
        }

        $this->set('items', $this->UserItem->Item->find('list'));
        $this->set('user_id', $userId);
        $rarities = $this->UserItem->getEnumValues('rarity');
        $this->set('rarities', $rarities);
    }

    //---------------------------------------------------------------------------------------------
    function admi_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid user item.');
            $this->redirect(array('controller' => 'admin_home', 'action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->UserItem->save($this->data)) {
                $this->Session->setFlash('Item saved.');
                $this->redirect(array('action' => 'view', $this->UserItem->id));
            } else {
                $this->Session->setFlash('Could not save item.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->UserItem->read(null, $id);
        }
        $this->set('items', $this->UserItem->Item->find('list'));
        $rarities = $this->UserItem->getEnumValues('rarity');
        $this->set('rarities', $rarities);
    }

    //---------------------------------------------------------------------------------------------
    function admi_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for item.');
            $this->redirect(array('controller' => 'admin_home', 'action' => 'index'));
        }
        if ($this->UserItem->del($id)) {
            $this->Session->setFlash('Item deleted.');
            $this->redirect($this->referer());
        } else {
            $this->Session->setFlash('Could not delete item.');
            $this->redirect($this->referer());
        }
    }
}