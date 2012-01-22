<?php

define('MATCHMAKING_CACHE', 'matchmaking');

class BattlesController extends AppController {

    var $uses = array('Battle', 'Formation');

    //---------------------------------------------------------------------------------------------
    function index () {
        $this->pageTitle = 'War Room';

        $user = $this->GameAuth->GetLoggedInUser();

        $activeFormationId = $user['User']['active_formation_id'];

        $userFormationIds = $this->Formation->GetFormationIdsByUserId($this->GameAuth->GetLoggedInUserId());
        $userFormations = $this->Formation->GetFormations($userFormationIds);

        // Get the active formation and remove it from the list.
        $activeFormation = '';
        foreach ($userFormations as $num => $formation) {
            if ($formation['Formation']['id'] == $activeFormationId) {
                $activeFormation = $formation;
                unset($userFormations[$num]);
            }
        }

        $this->set('userFormations', $userFormations);
        $this->set('activeFormation', $activeFormation);
    }

    //---------------------------------------------------------------------------------------------
    function history () {
        $this->pageTitle = 'Battle History';

        $battleHistory = $this->Battle->GetBattleHistory($this->GameAuth->GetLoggedInUserId());
        $this->set('battleHistory', $battleHistory);
    }

    //---------------------------------------------------------------------------------------------
    function fight () {
        if (empty($this->data))
            $this->fof();

        if (!isset($this->data['Battle']['battle_type']))
            $this->fof();

        $battleType = $this->data['Battle']['battle_type'];

        $battleId = -1;
        if ($battleType == 'spar') {
            if (!isset($this->data['Battle']['defender_name']))
                $this->fof();

            if (!isset($this->data['Battle']['attacker_id']))
                $this->fof();

            $formationName = $this->data['Battle']['defender_name'];

            $formationId = $this->Formation->find('first', array(
                'fields' => array(
                    'Formation.id',
                ),
                'conditions' => array(
                    'name' => $formationName,
                ),
            ));

            if ($formationId === false) {
                $this->Session->setFlash('That formation doesn\'t exist!');
                $this->redirect(array('action' => 'index'));
            }
            $formationId = $formationId['Formation']['id'];

            $formation = $this->Formation->GetFormation($formationId);
            if ($formation['Formation']['can_spar'] == 0) {
                $this->Session->setFlash('That formation is not available to spar.');
                $this->redirect(array('action' => 'index'));
            }

            $attackingFormationId = $this->data['Battle']['attacker_id'];

            $user = $this->GameAuth->GetLoggedInUser();
            $attackingFormation = $this->Formation->GetFormation($attackingFormationId);

            if ($attackingFormation['Formation']['user_id'] != $user['User']['id'])
                $this->fof();

            if ($attackingFormation['Formation']['id'] == $formationId) {
                $this->Session->setFlash('You can\'t fight yourself, that\'s just silly!');
                $this->redirect(array('action' => 'index'));
            }

            $battleId = $this->Battle->Fight($attackingFormationId, $formationId, 'spar');
        } else {
            $user = $this->GameAuth->GetLoggedInUser();
            $cacheKey = GenerateCacheKey(MATCHMAKING_CACHE, $user['User']['id']);
            $targetId = Cache::read($cacheKey, 'short');
            Cache::delete($cacheKey, 'short');

            if ($targetId === false) {
                $this->log('Tried to fight without target set.');
                $this->Session->setFlash('An error has occurred.');
                return;
            }

            if ($this->User->DeductBattle($user['User']['id'])) {
                $battleId = $this->Battle->Fight($user['User']['active_formation_id'], $targetId, 'battle');
            }
        }

        if ($battleId == -1) {
            $this->log('Battle code not working.');
            $this->Session->setFlash('An error has occurred.');
            return;
        }

        $this->redirect(array('action' => 'fight_result', $battleId));
    }

    //---------------------------------------------------------------------------------------------
    function matchmake () {
        if ($this->ShouldUseAjax()) {
            Configure::write('debug', 0);

            // Matchmaking
            $user = $this->GameAuth->GetLoggedInUser();

            if ($user['User']['num_battles'] <= 0) {
                echo 'You don\'t have any battles!';
                return;
            }

            $targetId = $this->Battle->Matchmake($user['User']['active_formation_id']);

            $userId = $this->GameAuth->GetLoggedInUserId();
            $cacheKey = GenerateCacheKey(MATCHMAKING_CACHE, $userId);
            if ($targetId == -1) {
                Cache::delete($cacheKey, 'short');
                return;
            }

            $targetFormation = $this->Formation->GetFormation($targetId);
            if ($targetFormation === false) {
                $this->log('Matchmaking formation didn\'t exist. formation id: ' . $targetId);
            }
            $tactician = $this->Formation->User->GetUser($targetFormation['Formation']['user_id']);
            if ($tactician === false) {
                $this->log('Matchmaking tactician didn\'t exist. user id: ' . $targetFormation['Formation']['user_id']);
            }
            $userFormation = $this->Formation->GetFormation($user['User']['active_formation_id']);

            $this->set('targetFormation', $targetFormation);
            $this->set('tactician', $tactician);
            $this->set('userFormation', $userFormation);

            Cache::write($cacheKey, $targetId, 'short');
            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function fight_result ($battleId) {
        $battle = $this->Battle->findById($battleId);
        if (!$battle)
            $this->fof();

        $this->pageTitle = sprintf('%s vs %s', $battle['Battle']['attacker_formation_name'], $battle['Battle']['defender_formation_name']);

        $battleLog = explode("\n", $battle['Battle']['web_log']);
        $battleInfo = json_decode($battleLog[0], true);
        $messages = array_slice($battleLog, 1);
        $this->set('battle', $battle);
        $this->set('battleInfo', $battleInfo);
        $this->set('battleLog', $battleLog[0]);
        $this->set('messages', $messages);
    }
}
?>