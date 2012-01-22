<?php

class ItemModsController extends AppController {


    //---------------------------------------------------------------------------------------------
    function admin_add ($userItemId = null) {
        if (!empty($this->data)) {
            $this->ItemMod->create();
            if ($this->ItemMod->save($this->data)) {
                $this->Session->setFlash('Item mod saved.');
                $this->redirect(array('controller' => 'user_items', 'action' => 'view', $this->data['ItemMod']['user_item_id']));
            } else {
                $this->Session->setFlash('Could not save item mod.');
            }
        }
        if ($userItemId == null)
            $this->fof();

        $this->set('userItemId', $userItemId);

        $bonusTypes = $this->ItemMod->BonusType->find('list');
        $this->set(compact('items', 'bonusTypes'));
    }

    //---------------------------------------------------------------------------------------------
    function admin_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid item mod.');
            $this->redirect($this->referer());
        }
        if (!empty($this->data)) {
            if ($this->ItemMod->save($this->data)) {
                $this->Session->setFlash('Item mod saved.');
                $this->redirect(array('controller' => 'user_items', 'action' => 'view', $this->data['ItemMod']['user_item_id']));
            } else {
                $this->Session->setFlash('Could not save item mod.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ItemMod->read(null, $id);
        }
        $bonusTypes = $this->ItemMod->BonusType->find('list');
        $this->set(compact('bonusTypes'));
    }

    //---------------------------------------------------------------------------------------------
    function admin_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for item mod.');
            $this->redirect($this->referer());
        }
        if ($this->ItemMod->del($id)) {
            $this->Session->setFlash('Item mod deleted.');
            $this->redirect($this->referer());
        } else {
            $this->Session->setFlash('Could not delete item mod.');
            $this->redirect($this->referer());
        }
    }

}
?>