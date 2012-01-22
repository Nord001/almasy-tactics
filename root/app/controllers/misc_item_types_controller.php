<?php

class MiscItemTypesController extends AppController {

    var $paginate = array(
        'limit' => 0,
    );

    //---------------------------------------------------------------------------------------------
    function admi_index () {
        $this->set('miscItemTypes', $this->paginate());
    }

    //---------------------------------------------------------------------------------------------
    function admi_view ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid item type.');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('miscItemType', $this->MiscItemType->read(null, $id));
    }

    //---------------------------------------------------------------------------------------------
    function admi_add () {
        if (!empty($this->data)) {
            $this->MiscItemType->create();
            if ($this->MiscItemType->save($this->data)) {
                $this->Session->setFlash('Item type saved.');
                $this->redirect(array('action' => 'view', $this->MiscItemType->id));
            } else {
                $this->Session->setFlash('Could not save item type.');
            }
        }
    }

    //---------------------------------------------------------------------------------------------
    function admi_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid item type.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->MiscItemType->save($this->data)) {
                $this->Session->setFlash('Item type saved.');
                $this->redirect(array('action' => 'view', $this->MiscItemType->id));
            } else {
                $this->Session->setFlash('Could not save item type.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->MiscItemType->read(null, $id);
        }
    }

    //---------------------------------------------------------------------------------------------
    function admi_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for item type.');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->MiscItemType->del($id)) {
            $this->Session->setFlash('Item type deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Could not delete item type.');
            $this->redirect($this->referer());
        }
    }

}
?>