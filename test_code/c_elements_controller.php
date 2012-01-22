<?php

class CElementsController extends AppController {

    var $pageTitle = 'Element';
    var $name = 'CElements';


    //---------------------------------------------------------------------------------------------
    function admin_index () {
        $this->set('elements', $this->CElement->find('all'));
    }

    //---------------------------------------------------------------------------------------------
    function admin_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid element.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->CElement->save($this->data)) {
                $this->Session->setFlash('Element saved.');
                $this->redirect(array('action' => 'view', $this->CElement->id));
            } else {
                $this->Session->setFlash('Could not save element.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->CElement->read(null, $id);
        }
    }

}
?>