<?php
class FormationsController extends AppController {

    var $helpers = array('FormationView');

    var $uses = array('Formation', 'AiScript');

    //---------------------------------------------------------------------------------------------
    function index () {
        $user = $this->GameAuth->GetLoggedInUser();

        $activeFormationId = $user['User']['active_formation_id'];

        $userFormationIds = $this->Formation->GetFormationIdsByUserId($user['User']['id']);
        $userFormations = $this->Formation->GetFormations($userFormationIds);

        // Get the active formation and remove it from the list.
        $activeFormation = '';
        foreach ($userFormations as $num => $formation) {
            if ($formation['Formation']['id'] == $activeFormationId) {
                $activeFormation = $formation;
                unset($userFormations[$num]);
            }
        }

        $this->set('formations', $userFormations);
        $this->set('activeFormation', $activeFormation);
    }

    //---------------------------------------------------------------------------------------------
    function __setViewData ($id) {
        $formation = $this->Formation->GetFormationEx($id);
        if ($formation['Formation']['user_id'] != $this->GameAuth->GetLoggedInUserId())
            $this->fof();

        $this->set('formation', $formation);
        $this->pageTitle = $formation['Formation']['name'];
    }

    //---------------------------------------------------------------------------------------------
    function rankings () {
        $personalRankings = $this->User->GetPersonalRankings($this->GameAuth->GetLoggedInUserId());
        foreach ($personalRankings as &$ranking) {
            $formation = $this->Formation->GetFormation($ranking['FormationId']);
            $ranking['Formation'] = $formation['Formation'];
            $user = $this->Formation->User->GetUser($formation['Formation']['user_id']);
            $ranking['Formation']['User'] = $user['User'];
            unset($ranking['FormationId']);
        }
        $this->set('personalRankings', $personalRankings);
        $this->set('numRankings', $this->Formation->GetFormationRankingsCount());
    }

    //---------------------------------------------------------------------------------------------
    function view ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid formation.');
            $this->redirect(array('action' => 'index'));
        }

        $this->__setViewData($id);
    }

    //---------------------------------------------------------------------------------------------
    function create () {
        $formationIds = $this->Formation->GetFormationIdsByUserId($this->GameAuth->GetLoggedInUserId());
        if (count($formationIds) >= MAX_FORMATIONS_PER_USER) {
            $this->Session->setFlash('You can\'t create any more formations.');
            $this->redirect($this->referer());
        }

        if (!empty($this->data)) {

            // Validate
            if (!$this->GameValidate->IsValidFormationName($this->data['Formation']['name']))
                $this->fof();

            $this->Formation->create();
            $this->data['Formation']['user_id'] = $this->GameAuth->GetLoggedInUserId();
            $this->data['Formation']['reputation'] = STARTING_REPUTATION;
            $this->data['Formation']['date_created'] = date("Y-m-d H:i:s");

            $existingFormation = $this->Formation->GetFormationByName($this->data['Formation']['name']);
            if ($existingFormation !== false) {
                $this->Session->setFlash('That name is already taken.');
                $this->redirect($this->referer());
            }

            if ($this->Formation->save($this->data)) {
                $this->Formation->ClearFormationsCacheByUser($this->GameAuth->GetLoggedInUserId());
                $this->Session->setFlash('Formation created.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Could not create formation.');
            }
        }
    }

    //---------------------------------------------------------------------------------------------
    function edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid formation.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            $formationId = $this->data['Formation']['id'];

            $userId = $this->GameAuth->GetLoggedInUserId();

            if (!isset($this->data['Formation']['characterIds'])) {
                $this->log(sprintf('%s edited a formation and didn\'t have character ids.', $userId));
                $this->fof();
            }

            $newCharacterIds = $this->data['Formation']['characterIds'];

            if (count($newCharacterIds) > MAX_CHARACTERS_PER_FORMATION) {
                $this->Session->setFlash('Your formation has too many characters!');
                $this->redirect($this->referer());
            }

            $formation = $this->Formation->GetFormation($formationId);
            if ($formation['Formation']['user_id'] != $userId) {
                $this->log(sprintf('%s edited a formation that didn\'t belong to them.', $userId));
                $this->fof();
            }

            $characters = $this->Formation->Character->GetCharacters($newCharacterIds);
            foreach ($characters as $character) {
                if ($character['Character']['user_id'] != $userId) {
                    $this->log(sprintf('%s edited a formation with characters that didn\'t belong to them.', $userId));
                    $this->fof();
                }
            }

            // If invalid character match
            if (!$this->GameValidate->IsValidFormationName($this->data['Formation']['name'])) {
                $this->Session->setFlash('That isn\'t a valid formation name.');
                $this->redirect($this->referer());
            }

            $existingFormation = $this->Formation->GetFormationByName($this->data['Formation']['name']);
            if ($existingFormation !== false && $existingFormation['Formation']['id'] != $this->data['Formation']['id']) {
                $this->Session->setFlash('That name is already taken.');
                $this->redirect($this->referer());
            }

            $this->Formation->id = $formationId;
            $this->Formation->fastSave('name', $this->data['Formation']['name']);
            $this->Formation->fastSave('can_spar', $this->data['Formation']['can_spar']);

            $success = $this->Formation->UpdateFormationComposition($formationId, $newCharacterIds);
            $this->Formation->ClearFormationCache($formationId);

            if ($success)
                $this->Session->setFlash('Formation saved.');
            else
                $this->fof();

            $this->redirect(array('action' => 'view', $formationId));
        }

        if (empty($this->data)) {
            $formation = $this->Formation->GetFormation($id);
            $characterIdsInFormation = Set::classicExtract($formation, 'Characters.{n}.Character.id');
            $characterIds = $this->Formation->Character->GetCharacterIdsByUserId($this->GameAuth->GetLoggedInUserId());
            $characters = $this->Formation->Character->GetCharacters($characterIds);
            $this->set('formation', $formation);
            $this->set('characters', $characters);
            $this->set('characterIdsInFormation', $characterIdsInFormation);
        }
    }

    //---------------------------------------------------------------------------------------------
    function move_character () {
        if ($this->ShouldUseAjax()) {
            if (
                !isset($this->params['form']['formationId']) ||
                !isset($this->params['form']['characterId']) ||
                !isset($this->params['form']['endPosition'])
            ) {
                $this->autoRender = false;
                echo 'Error.';
                return;
            }

            $formationId = $this->params['form']['formationId'];
            $characterId = $this->params['form']['characterId'];
            $endPosition = $this->params['form']['endPosition'];

            $formation = $this->Formation->GetFormation($formationId);

            if ($formation['Formation']['user_id'] != $this->GameAuth->GetLoggedInUserId())
                $this->fof();

            // Assure that the character is in the formation
            $inFormation = false;
            $characterPosition = -1;
            foreach ($formation['Characters'] as $character) {
                if ($character['Character']['id'] == $characterId) {
                    $characterPosition = $character['CharactersFormation']['position'];
                    $inFormation = true;
                }
            }

            if (!$inFormation)
                $this->fof();

            $endCharacterIndex = $formation['CharacterFormation'][$endPosition];
            if ($endCharacterIndex == -1) {
                // No character in this spot, do the move
                $this->Formation->MoveCharacterToEmptySpot($formationId, $characterId, $endPosition);
            } else {
                $this->Formation->SwapCharacters($formationId, $characterId, $characterPosition, $endPosition);
            }

            $this->__setViewData($formationId);

            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for formation.');
            $this->redirect(array('action' => 'index'));
        }

        $formation = $this->Formation->GetFormation($id);
        if ($formation['Formation']['user_id'] != $this->GameAuth->GetLoggedInUserId())
            $this->fof();

        $this->Formation->ClearFormationsCacheByUser($this->GameAuth->GetLoggedInUserId());

        if ($this->Formation->del($id)) {
            $this->Session->setFlash('Formation deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Could not delete formation.');
            $this->redirect($this->referer());
        }
    }

    //---------------------------------------------------------------------------------------------
    function set_active ($formationId = null) {
        if (!$formationId) {
            $this->Session->setFlash('Invalid ID for formation.');
            $this->redirect(array('action' => 'index'));
        }

        $formation = $this->Formation->GetFormation($formationId);
        if ($formation['Formation']['user_id'] != $this->GameAuth->GetLoggedInUserId())
            $this->fof();

        if (count($formation['Characters']) == 0) {
            $this->Session->setFlash('Can\'t set empty formation as active!');
            $this->redirect(array('action' => 'index'));
        }

        $this->Formation->User->id = $this->GameAuth->GetLoggedInUserId();
        $this->Formation->User->fastSave('active_formation_id', $formationId);
        $this->Formation->User->ClearUserCache($this->Formation->User->id);

        $this->redirect(array('action' => 'index'));
    }

    //---------------------------------------------------------------------------------------------
    function strategy ($formationId = null) {
        if (!$formationId) {
            $this->Session->setFlash('Invalid ID for formation.');
            $this->redirect(array('action' => 'index'));
        }

        $formation = $this->Formation->GetFormation($formationId);
        $loggedInId = $this->GameAuth->GetLoggedInUserId();
        if ($formation['Formation']['user_id'] != $loggedInId)
            $this->fof();

        $this->set('formation', $formation);

        $scriptIds = $this->AiScript->GetAiScriptIdsByUserId($loggedInId);
        $scripts = $this->AiScript->GetAiScripts($scriptIds);
        $this->set('scripts', $scripts);
    }

    //---------------------------------------------------------------------------------------------
    function create_script ($formationId = null) {
        if (!empty($this->data)) {

            $this->AiScript->create();
            $this->data['AiScript']['user_id'] = $this->GameAuth->GetLoggedInUserId();

            if ($this->AiScript->save($this->data)) {
                $this->AiScript->ClearAiScriptIdsCache($this->GameAuth->GetLoggedInUserId());
                $this->Session->setFlash('Script saved.');

                $this->redirect(array('action' => 'strategy', $this->data['formation_id']));
            } else {
                $this->Session->setFlash('Could not save script.');
            }
        } else {
            if (!$formationId) {
                $this->Session->setFlash('Invalid ID for formation.');
                $this->redirect(array('action' => 'index'));
            }

            $formation = $this->Formation->GetFormation($formationId);
            $loggedInId = $this->GameAuth->GetLoggedInUserId();
            if ($formation['Formation']['user_id'] != $loggedInId)
                $this->fof();

            $this->set('formation', $formation);
        }
    }

    //---------------------------------------------------------------------------------------------
    function edit_script ($formationId = null, $scriptId = null) {
        if (!empty($this->data)) {

            $script = $this->AiScript->GetAiScript($this->data['AiScript']['id']);
            if ($script === false) {
                $this->Session->setFlash('An error has occurred.');
                $this->log('Attempted to edit nonexistent script.');
                $this->redirect($this->referer());
            }

            if ($script['AiScript']['user_id'] != $this->GameAuth->GetLoggedInUserId()) {
                $this->Session->setFlash('An error has occurred.');
                $this->log('Attempted to edit another user\'s script.');
                $this->redirect($this->referer());
            }

            if ($this->AiScript->save($this->data)) {
                $this->AiScript->ClearAiScriptCache($this->data['AiScript']['id']);
                $this->Session->setFlash('Script saved.');

                $this->redirect(array('action' => 'strategy', $this->data['formation_id']));
            } else {
                $this->Session->setFlash('Could not create formation.');
            }
        } else {
            if (!$formationId) {
                $this->Session->setFlash('Invalid ID for formation.');
                $this->redirect(array('action' => 'index'));
            }

            if (!$scriptId) {
                $this->Session->setFlash('Invalid ID for script.');
                $this->redirect(array('action' => 'index'));
            }

            $formation = $this->Formation->GetFormation($formationId);
            $loggedInId = $this->GameAuth->GetLoggedInUserId();
            if ($formation['Formation']['user_id'] != $loggedInId)
                $this->fof();

            $this->set('formation', $formation);

            $script = $this->AiScript->GetAiScript($scriptId);
            if ($script['AiScript']['user_id'] != $loggedInId)
                $this->fof();

            $this->set('script', $script);
        }
    }

    //---------------------------------------------------------------------------------------------
    function delete_script ($formationId = null, $scriptId = null) {
        if (!$formationId) {
            $this->Session->setFlash('Invalid ID for formation.');
            $this->redirect(array('action' => 'index'));
        }

        if (!$scriptId) {
            $this->Session->setFlash('Invalid ID for script.');
            $this->redirect(array('action' => 'index'));
        }

        $loggedInId = $this->GameAuth->GetLoggedInUserId();
        $script = $this->AiScript->GetAiScript($scriptId);
        if ($script['AiScript']['user_id'] != $loggedInId)
            $this->fof();

        $formations = $this->AiScript->query("
            SELECT
                `formation_id`
            FROM
                `characters_formations`
            WHERE
                `script_id` = {$script['AiScript']['id']}"
        );

        $this->AiScript->query("
            UPDATE
                `characters_formations`
            SET
                `script_id` = NULL
            WHERE
                `script_id` = {$script['AiScript']['id']}"
        );

        foreach ($formations as $formation)
            $this->Formation->ClearFormationCache($formation['characters_formations']['formation_id']);

        $this->AiScript->delete($script['AiScript']['id']);
        $this->AiScript->ClearAiScriptIdsCache($loggedInId);
        $this->AiScript->ClearAiScriptCache($script['AiScript']['id']);

        $this->redirect(array('controller' => 'formations', 'action' => 'strategy', $formationId));
    }

    //---------------------------------------------------------------------------------------------
    function edit_ai () {
        if (!empty($this->data)) {

            foreach ($this->data['CharacterIds'] as $characterId) {
                $character = $this->Formation->Character->GetCharacter($characterId);
                if ($character['Character']['user_id'] != $this->GameAuth->GetLoggedInUserId())
                    $this->fof();
            }

            $this->Formation->UpdateBoundScripts($this->data['formation_id'], $this->data['CharacterIds'], $this->data['CharacterSelect']);
            $this->redirect(array('controller' => 'formations', 'action' => 'strategy', $this->data['formation_id']));
            return;
        }
        $this->fof();
    }
}
?>