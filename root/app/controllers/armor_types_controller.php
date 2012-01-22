<?php

class ArmorTypesController extends AppController {

    var $paginate = array(
        'limit' => 0,
    );

    //---------------------------------------------------------------------------------------------
    function admi_index () {
        $this->set('armorTypes', $this->paginate());
    }

    //---------------------------------------------------------------------------------------------
    function admi_view ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid item type.');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('armorType', $this->ArmorType->read(null, $id));
    }

    //---------------------------------------------------------------------------------------------
    function admi_add () {
        if (!empty($this->data)) {
            $this->ArmorType->create();
            if ($this->ArmorType->save($this->data)) {
                $this->Session->setFlash('Item type saved.');
                $this->redirect(array('action' => 'view', $this->ArmorType->id));
            } else {
                $this->Session->setFlash('Could not save item type.');
            }
        }

        $attackTypes = $this->ArmorType->getEnumValues('attack_type');
        $this->set('attackTypes', $attackTypes);
    }

    //---------------------------------------------------------------------------------------------
    function admi_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid item type.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->ArmorType->save($this->data)) {
                $this->Session->setFlash('Item type saved.');
                $this->redirect(array('action' => 'view', $this->ArmorType->id));
            } else {
                $this->Session->setFlash('Could not save item type.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ArmorType->read(null, $id);
        }
    }

    //---------------------------------------------------------------------------------------------
    function admi_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for item type.');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->ArmorType->del($id)) {
            $this->Session->setFlash('Item type deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Could not delete item type.');
            $this->redirect($this->referer());
        }
    }

}
?>