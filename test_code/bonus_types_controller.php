<?php

class BonusTypesController extends AppController {


    var $paginate = array(
        'limit' => 0,
    );

    //---------------------------------------------------------------------------------------------
    function admin_index () {
        $this->set('bonusTypes', $this->paginate());
    }

    //---------------------------------------------------------------------------------------------
    function admin_view ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid bonus type.');
            $this->redirect(array('action' => 'index'));
        }
        $this->set('bonusType', $this->BonusType->read(null, $id));
    }

    //---------------------------------------------------------------------------------------------
    function admin_add () {
        if (!empty($this->data)) {
            $this->BonusType->create();
            if ($this->BonusType->save($this->data)) {
                $this->Session->setFlash('Bonus type saved.');
                $this->redirect(array('action' => 'view', $this->BonusType->id));
            } else {
                $this->Session->setFlash('Could not save bonus type.');
            }
        }
    }

    //---------------------------------------------------------------------------------------------
    function admin_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid bonus type.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->BonusType->save($this->data)) {
                $this->Session->setFlash('Bonus type saved.');
                $this->redirect(array('action' => 'view', $this->BonusType->id));
            } else {
                $this->Session->setFlash('Could not save bonus type.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->BonusType->read(null, $id);
        }
    }

    //---------------------------------------------------------------------------------------------
    function admin_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for bonus type.');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->BonusType->del($id)) {
            $this->Session->setFlash('Bonus type deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Could not delete bonus type.');
            $this->redirect($this->referer());
        }
    }

}
?>