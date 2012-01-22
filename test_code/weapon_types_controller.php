<?php

class WeaponTypesController extends AppController {


    var $paginate = array(
        'limit' => 0,
    );

    //---------------------------------------------------------------------------------------------
    function admin_index () {
        $this->set('weaponTypes', $this->paginate());
    }

    //---------------------------------------------------------------------------------------------
    function admin_view ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid item type.');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('weaponType', $this->WeaponType->read(null, $id));
    }

    //---------------------------------------------------------------------------------------------
    function admin_add () {
        if (!empty($this->data)) {
            $this->WeaponType->create();
            if ($this->WeaponType->save($this->data)) {
                $this->Session->setFlash('Item type saved.');
                $this->redirect(array('action' => 'view', $this->WeaponType->id));
            } else {
                $this->Session->setFlash('Could not save item type.');
            }
        }

        $attackTypes = $this->WeaponType->getEnumValues('attack_type');
        $this->set('attackTypes', $attackTypes);
    }

    //---------------------------------------------------------------------------------------------
    function admin_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid item type.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->WeaponType->save($this->data)) {
                $this->Session->setFlash('Item type saved.');
                $this->redirect(array('action' => 'view', $this->WeaponType->id));
            } else {
                $this->Session->setFlash('Could not save item type.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->WeaponType->read(null, $id);
        }

        $attackTypes = $this->WeaponType->getEnumValues('attack_type');
        $this->set('attackTypes', $attackTypes);
    }

    //---------------------------------------------------------------------------------------------
    function admin_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for item type.');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->WeaponType->del($id)) {
            $this->Session->setFlash('Item type deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Could not delete item type.');
            $this->redirect($this->referer());
        }
    }

}
?>