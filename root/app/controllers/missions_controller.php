<?php

class MissionsController extends AppController {
    var $helpers = array('MissionView');

    var $pageTitle = 'Missions';

    var $paginate = array(
        'limit' => 0,
        'contain' => array(
            'MissionReward',
        )
    );

    var $uses = array('Mission', 'Formation');

    //---------------------------------------------------------------------------------------------
    function index () {
        $this->redirect(array('action' => 'mission_list'));
    }

    //---------------------------------------------------------------------------------------------
    function mission_list ($formationId = null) {
        $user = $this->GameAuth->GetLoggedInUser();

        $userFormationIds = $this->Formation->GetFormationIdsByUserId($user['User']['id']);
        $userFormations = $this->Formation->GetFormations($userFormationIds);
        $this->set('userFormations', $userFormations);

        if ($formationId) {
            $formation = $this->Formation->GetFormation($formationId);
            if ($formation === false) {
                $this->fof();
                return;
            }
            if ($formation['Formation']['user_id'] != $user['User']['id']) {
                $this->fof();
                return;
            }

            $missionList = $this->Mission->GetMissionList($formationId);
            $openMissions = $this->Mission->GetMissions($missionList['open_missions']);
            $restrictedMissions = $this->Mission->GetMissions($missionList['restricted_missions']);
            $this->set('openMissions', $openMissions);
            $this->set('restrictedMissions', $restrictedMissions);
            $this->set('selectedFormation', $formation);
        } else {
            $this->set('noFormationSelected', true);

            if (count($userFormationIds) == 1) {
                $this->redirect(array('action' => 'index', $userFormationIds[0]));
                return;
            }
        }
    }

    //---------------------------------------------------------------------------------------------
    function view ($formationId = null, $missionId = null) {
        if ($missionId == null || $formationId == null) {
            $this->fof();
            return;
        }

        $mission = $this->Mission->GetMission($missionId);
        if ($mission === false) {
            $this->fof();
            return;
        }

        $formation = $this->Formation->GetFormation($formationId);
        if ($formation === false) {
            $this->fof();
            return;
        }
        $user = $this->GameAuth->GetLoggedInUser();
        if ($formation['Formation']['user_id'] != $user['User']['id']) {
            $this->fof();
            return;
        }

        $this->set('mission', $mission);

        $canDoMission = $this->Mission->CanDoMission($formationId, $missionId);
        $this->set('canDoMission', $canDoMission);
        $this->set('formation', $formation);
    }

    //---------------------------------------------------------------------------------------------
    function do_mission () {
        if (!$this->data) {
            $this->fof();
            return;
        }

        if (!$this->CheckCSRFToken()) {
            $this->fof();
            return;
        }

        do {
            $formationId = @$this->data['formation_id'];
            $missionId = @$this->data['mission_id'];

            if (!$formationId || !$missionId) {
                IERR('Form data incorrect.');
                break;
            }

            $formation = G($this->Formation->GetFormation($formationId));
            $user = $this->GameAuth->GetLoggedInUser();
            if ($formation['Formation']['user_id'] != $user['User']['id']) {
                IERR('Formation did not belong to user.');
                break;
            }

            if ($this->Mission->DoMission($formationId, $missionId)) {
                // Pretend like you always win for now.
                $this->Session->setFlash('Mission complete.');
                $this->redirect(array('action' => 'mission_list', $formationId));
                return;
            }
        } while (false);

        $this->Session->setFlash(ERROR_STR);
        $this->redirect(array('action' => 'view', $this->data['formation_id'], $this->data['mission_id']));
    }

    //---------------------------------------------------------------------------------------------
    function history () {
    }

    //---------------------------------------------------------------------------------------------
    function admi_index () {
        $missions = $this->paginate();
        $this->set('missions', $missions);
        $missionNames = Set::classicExtract($missions, '{n}.Mission.name');
        $this->set('missionNames', $missionNames);
        $missionGroups = $this->Mission->MissionGroup->find('all');
        $this->set('missionGroups', $missionGroups);
    }

    //---------------------------------------------------------------------------------------------
    function admi_view ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid mission.');
            $this->redirect(array('action' => 'index'));
        }

        $conditions = array('Mission.id' => $id);

        $mission = $this->Mission->find('first', array(
            'conditions' => array(
                $conditions,
            ),
            'contain' => array(
                'MissionGroup',
                'MissionReward',
            ),
        ));

        if (!$mission) {
            $this->Session->setFlash('Invalid mission.');
            $this->redirect(array('action' => 'index'));
        }

        $this->set('mission', $mission, false);
    }

    //---------------------------------------------------------------------------------------------
    function admi_add () {
        if (!empty($this->data)) {
            $this->Mission->create();
            if ($this->Mission->save($this->data)) {
                $this->Session->setFlash('Mission saved.');
                $this->redirect(array('action' => 'view', $this->Mission->id));
            } else {
                $this->Session->setFlash('Could not save mission.');
            }
        }
        $missionGroups = $this->Mission->MissionGroup->find('list');
        $this->set('missionGroups', $missionGroups);
    }

    //---------------------------------------------------------------------------------------------
    function admi_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid mission.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->Mission->save($this->data)) {
                $this->Session->setFlash('Mission saved.');
                $this->redirect(array('action' => 'view', $this->Mission->id));
            } else {
                $this->Session->setFlash('Could not save mission.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Mission->find('first', array(
                'conditions' => array(
                    'id' => $id,
                ),
            ));
        }
        $missionGroups = $this->Mission->MissionGroup->find('list');
        $this->set('missionGroups', $missionGroups);
    }

    //---------------------------------------------------------------------------------------------
    function admi_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for mission.');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->Mission->del($id)) {
            $this->Session->setFlash('Mission deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Could not delete mission.');
            $this->redirect($this->referer());
        }
    }
}
?>
