<?php

class MissionGroupsController extends AppController {

    var $pageTitle = 'MissionGroups';

    //---------------------------------------------------------------------------------------------
    function admi_add () {
        if (!empty($this->data)) {
            $this->MissionGroup->create();
            if ($this->MissionGroup->save($this->data)) {
                $this->Session->setFlash('Mission group saved.');
                $this->redirect(array('controller' => 'missions', 'action' => 'index'));
            } else {
                $this->Session->setFlash('Could not save mission group.');
            }
        }
    }

    //---------------------------------------------------------------------------------------------
    function admi_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid mission group.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->MissionGroup->save($this->data)) {
                $this->Session->setFlash('Mission group saved.');
                $this->redirect(array('controller' => 'missions', 'action' => 'index'));
            } else {
                $this->Session->setFlash('Could not save mission group.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->MissionGroup->find('first', array(
                'conditions' => array(
                    'id' => $id,
                ),
            ));
        }
    }

    //---------------------------------------------------------------------------------------------
    function admi_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for mission group.');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->MissionGroup->del($id)) {
            $this->Session->setFlash('Mission group deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Could not delete mission group.');
            $this->redirect($this->referer());
        }
    }
}
?>
