<?php

class MissionRewardsController extends AppController {

    var $pageTitle = 'MissionRewards';

    //---------------------------------------------------------------------------------------------
    function admi_add ($missionId = null) {
        if (!empty($this->data)) {
            $this->MissionReward->create();
            if ($this->MissionReward->save($this->data)) {
                $this->Session->setFlash('Mission reward saved.');
                $this->redirect(array('controller' => 'missions', 'action' => 'view', $this->data['MissionReward']['mission_id']));
            } else {
                $this->Session->setFlash('Could not save mission reward.');
            }
        }
        $this->set('missionId', $missionId);
    }

    //---------------------------------------------------------------------------------------------
    function admi_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid mission reward.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->MissionReward->save($this->data)) {
                $this->Session->setFlash('Mission reward saved.');
                $this->redirect(array('controller' => 'missions', 'action' => 'view', $this->data['MissionReward']['mission_id']));
            } else {
                $this->Session->setFlash('Could not save mission reward.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->MissionReward->find('first', array(
                'conditions' => array(
                    'id' => $id,
                ),
            ));
        }
    }

    //---------------------------------------------------------------------------------------------
    function admi_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for mission reward.');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->MissionReward->del($id)) {
            $this->Session->setFlash('Mission reward deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Could not delete mission reward.');
            $this->redirect($this->referer());
        }
    }
}
?>
