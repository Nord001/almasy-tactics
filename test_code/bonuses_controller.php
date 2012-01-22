<?php

class BonusesController extends AppController {


    //---------------------------------------------------------------------------------------------
    function admin_add ($classId = null) {
        if (!empty($this->data)) {
            $this->Bonus->create();

            if ($this->Bonus->save($this->data)) {
                $this->Session->setFlash('Bonus saved.');
                $this->redirect(array('controller' => 'c_classes', 'action' => 'view', $this->data['Bonus']['class_id']));
            } else {
                $this->Session->setFlash('Could not save bonus.');
            }
        }
        $classes = $this->Bonus->Class->find('list');
        $bonusTypes = $this->Bonus->BonusType->find('list');
        $this->set('class_id', $classId);

        $this->set(compact('classes', 'bonusTypes'));
    }

    //---------------------------------------------------------------------------------------------
    function admin_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid bonus.');
            $this->redirect($this->referer());
        }
        if (!empty($this->data)) {
            if ($this->Bonus->save($this->data)) {
                $this->Session->setFlash('Bonus saved.');
                $this->redirect(array('controller' => 'c_classes', 'action' => 'view', $this->data['Bonus']['class_id']));
            } else {
                $this->Session->setFlash('Could not save bonus.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Bonus->read(null, $id);
        }
        $classes = $this->Bonus->Class->find('list');
        $bonusTypes = $this->Bonus->BonusType->find('list');
        $this->set(compact('classes','bonusTypes'));
    }

    //---------------------------------------------------------------------------------------------
    function admin_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for bonus.');
            $this->redirect($this->referer());
        }
        if ($this->Bonus->del($id)) {
            $this->Session->setFlash('Bonus deleted.');
            $this->redirect($this->referer());
        } else {
            $this->Session->setFlash('Could not delete bonus.');
            $this->redirect($this->referer());
        }
    }

}
?>