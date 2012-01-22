<?php

define('MATCHMAKING_CACHE', 'matchmaking');
define('MATCHMAKING_CACHE_DURATION', 'file');

class BattlesController extends AppController {

    var $uses = array('Battle', 'Formation', 'FormationMatchmaking');

    var $authList = array(
            'fight_result'          => AUTH_ALL,
    );

    //---------------------------------------------------------------------------------------------
    function index () {
        $this->setPageTitle('War Room');

        $user = $this->GameAuth->GetLoggedInUser();

        $userFormationIds = $this->Formation->GetFormationIdsByUserId($user['User']['id']);
        $userFormations = $this->Formation->GetFormations($userFormationIds);

        // Get the active formation and remove it from the list.
        $activeFormations = array();
        foreach ($userFormations as $num => $formation) {
            if ($formation['Formation']['active']) {
                $activeFormation[] = $formation;
                unset($userFormations[$num]);
            }
        }

        $this->set('inactiveFormations', $userFormations);
        $this->set('activeFormations', $activeFormation);
    }

    //---------------------------------------------------------------------------------------------
    function history () {
        $this->setPageTitle('Battle History');

        $battleHistory = $this->Battle->GetBattleHistory($this->GameAuth->GetLoggedInUserId());
        $this->set('battleHistory', $battleHistory);
    }

    //---------------------------------------------------------------------------------------------
    function fight () {
        if (empty($this->data)) {
            $this->fof();
            return;
        }

        if (!$this->CheckCSRFToken()) {
            $this->fof();
            return;
        }

        if (!isset($this->data['Battle']['battle_type'])){
            IERR('Form data incomplete.');
            $this->Session->setFlash(ERROR_STR);
            return;
        }

        $battleType = $this->data['Battle']['battle_type'];

        $battleId = -1;
        if ($battleType == 'spar') {
            if (!isset($this->data['Battle']['defender_name'])) {
                IERR('Form data incomplete.');
                $this->Session->setFlash(ERROR_STR);
                return;
            }

            if (!isset($this->data['Battle']['attacker_id'])) {
                IERR('Form data incomplete.');
                $this->Session->setFlash(ERROR_STR);
                return;
            }

            $formationName = $this->data['Battle']['defender_name'];
            if (empty($formationName)) {
               $this->Session->setFlash('You didn\'t enter a name.');
                   $this->redirect(array('action' => 'index'));
               return;
            }

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
                return;
            }
            $formationId = $formationId['Formation']['id'];

            $formation = $this->Formation->GetFormation($formationId);
            if ($formation['Formation']['can_spar'] == 0) {
                $this->Session->setFlash('That formation is not available to spar.');
                $this->redirect(array('action' => 'index'));
                return;
            }

            $attackingFormationId = $this->data['Battle']['attacker_id'];

            $user = $this->GameAuth->GetLoggedInUser();
            $attackingFormation = $this->Formation->GetFormation($attackingFormationId);

            if ($attackingFormation === false) {
                IERR('Attacking formation did not exist.');
                $this->Session->setFlash(ERROR_STR);
                $this->redirect(array('action' => 'index'));
                return;
            }

            if ($attackingFormation['Formation']['user_id'] != $user['User']['id']) {
                IERR('Attacking formation did not belong to authed user.');
                $this->Session->setFlash(ERROR_STR);
                $this->redirect(array('action' => 'index'));
                return;
            }

            if ($attackingFormation['Formation']['id'] == $formationId) {
                $this->Session->setFlash('You can\'t fight yourself, that\'s just silly!');
                $this->redirect(array('action' => 'index'));
                return;
            }

            // Remove matchmaking cache so that people can't fight after this
            $this->FormationMatchmaking->ClearMatchmaking($formationId);

            $battleId = $this->Battle->Fight($attackingFormationId, $formationId, 'spar');
        } else {
            /*
            if ($this->CaptchaRequired()) {
                IERR('Tried to fight but did not clear captcha.');
                $this->Session->setFlash(ERROR_STR);
                return;
            }
            */

            $user = $this->GameAuth->GetLoggedInUser();
            $cacheKey = GenerateCacheKey(MATCHMAKING_CACHE, $user['User']['id']);
            $formationId = $this->data['Formation']['formation_id'];
            $formation = $this->Formation->GetFormation($formationId);
            if ($formation['Formation']['user_id'] != $user['User']['id']) {
                IERR('Tried to fight with formation that didn\'t belong to them.');
                return;
            }

            $targetIds = $this->FormationMatchmaking->GetMatchmaking($formationId);
            $this->FormationMatchmaking->ClearMatchmaking($formationId);

            if ($targetIds === false) {
                //LogAppError('Tried to fight without target set.');
                $this->redirect(array('controller' => 'battles', 'action' => 'matchmake'));
                return;
            }

            $targetId = $this->data['Formation']['target_id'];
            if (!in_array($targetId, $targetIds)) {
                IERR('Tried to fight with invalid target.');
                $this->Session->setFlash(ERROR_STR);
                return;
            }

            if ($this->User->DeductBattle($user['User']['id'])) {
                $battleId = $this->Battle->Fight($formationId, $targetId, 'battle');
            } else {
                $this->Session->setFlash('You need battles to battle!');
                return;
            }
        }

        if ($battleId == -1) {
            $this->Session->setFlash(ERROR_STR);
            return;
        }

        $this->redirect(array('action' => 'fight_result', $battleId));
    }

    //---------------------------------------------------------------------------------------------
    function matchmake ($matchmakingFormationId = null) {
        $user = $this->GameAuth->GetLoggedInUser();
        if ($user['User']['num_battles'] <= 0) {
            $this->Session->setFlash('You need battles to battle!');
            $this->redirect(array('controller' => 'battles', 'action' => 'index'));
            return;
        }

        if ($matchmakingFormationId == null) {
            $matchmakingFormationId = $user['User']['last_battle_formation_id'];
        } else {
            if ($user['User']['last_battle_formation_id'] != $matchmakingFormationId) {
                $this->Battle->User->id = $user['User']['id'];
                $this->Battle->User->fastSave('last_battle_formation_id', $matchmakingFormationId);
                $this->Battle->User->ClearUserCache($user['User']['id']);
            }
        }

        $targetIds = $this->Battle->Matchmake2($matchmakingFormationId);
        $this->FormationMatchmaking->SaveMatchmaking($matchmakingFormationId, $targetIds);

        $targetFormations = $this->Formation->GetFormations($targetIds);
        $this->set('targetFormations', $targetFormations);

        $user = $this->GameAuth->GetLoggedInUser();
        $activeFormation = $this->Formation->GetFormation($matchmakingFormationId);
        $this->set('activeFormation', $activeFormation);
    }

    //---------------------------------------------------------------------------------------------
    function fight_result ($battleId = null) {
        if ($battleId === null) {
            $this->fof();
        }

        $battle = $this->Battle->findById($battleId);
        if (!$battle) {
            $this->fof();
            return;
        }

        // Ungzip web log. If it fails, run with the original.
        $decodedLog = @gzuncompress($battle['Battle']['web_log']);
        if ($decodedLog !== false)
            $battle['Battle']['web_log'] = $decodedLog;

        $this->setPageTitle(sprintf('%s vs %s', $battle['Battle']['attacker_formation_name'], $battle['Battle']['defender_formation_name']));

        $battleLog = explode("\n", $battle['Battle']['web_log']);
        $battleInfo = json_decode($battleLog[0], true);
        $messages = array_slice($battleLog, 1);
        $this->set('battle', $battle);
        $this->set('battleInfo', $battleInfo);
        $this->set('battleLog', $battleLog[0], false);
        $this->set('messages', $messages, false);
    }

    //---------------------------------------------------------------------------------------------
    function admi_simulator () {
        if (!empty($this->data)) {
            $attackingFormation = array(
                'Formation' => $this->data['AttackingFormation'],
            );
            $defendingFormation = array(
                'Formation' => $this->data['DefendingFormation'],
            );

            $victor = isset($this->data['victor']) ? $this->data['victor'] : '';
            if ($victor == 'attacker')
                $this->data['defender_hp_percent'] = 0;
            elseif ($victor == 'defender')
                $this->data['attacker_hp_percent'] = 0;

            $battleData = array(
                'victor' => $victor,
                'attacker_hp_percent' => $this->data['attacker_hp_percent'],
                'defender_hp_percent' => $this->data['defender_hp_percent'],
            );

            $result = $this->Battle->CalculateBattleResults($battleData, $attackingFormation, $defendingFormation);

            $this->set('result', $result);
        }
    }
}
?>
