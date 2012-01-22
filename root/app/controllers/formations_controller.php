<?php
class FormationsController extends AppController {

    var $helpers = array('FormationView');

    var $uses = array('Formation', 'AiScript');

    //---------------------------------------------------------------------------------------------
    function index () {
        $this->setPageTitle('Your Formations');

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
    function __setViewData ($id) {
        $formation = $this->Formation->GetFormationEx($id);
        if ($formation === false) {
            $this->fof();
            return;
        }
        if ($formation['Formation']['user_id'] != $this->GameAuth->GetLoggedInUserId()){
            $this->fof();
            return;
        }

        $this->set('formation', $formation);
        $this->setPageTitle($formation['Formation']['name']);
    }

    //---------------------------------------------------------------------------------------------
    function rankings () {
        $this->setPageTitle('Rankings');

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
        if ($id == null) {
            $this->fof();
            return;
        }

        $this->__setViewData($id);
    }

    //---------------------------------------------------------------------------------------------
    function create () {
        $this->setPageTitle('Create Formation');

        $formationIds = $this->Formation->GetFormationIdsByUserId($this->GameAuth->GetLoggedInUserId());
        if (count($formationIds) >= MAX_FORMATIONS_PER_USER) {
            $this->Session->setFlash('You can\'t create any more formations.');
            $this->redirect(array('controller' => 'formations', 'action' => 'index'));
        }

        if (!empty($this->data)) {
            do {
                if (!$this->CheckCSRFToken())
                    break;

                if (!isset($this->data['Formation']['characterIds'])) {
                    $this->Session->setFlash('You must select characters!');
                    break;
                }

                if (@$this->data['Formation']['name'] == '') {
                    $this->Session->setFlash('You must have a name.');
                    break;
                }

                if (!$this->GameValidate->IsValidFormationName($this->data['Formation']['name'])) {
                    $this->Session->setFlash('That formation name is not valid. Alphanumeric characters and spaces only.');
                    break;
                }

                $existingFormation = $this->Formation->GetFormationByName($this->data['Formation']['name']);
                if ($existingFormation !== false) {
                    $this->Session->setFlash('That name is already taken.');
                    break;
                }

                $newCharacterIds = $this->data['Formation']['characterIds'];

                if (count($newCharacterIds) > MAX_CHARACTERS_PER_FORMATION) {
                    $this->Session->setFlash('Your formation has too many characters!');
                    break;
                }

                $userId = $this->GameAuth->GetLoggedInUserId();
                $characters = $this->Formation->Character->GetCharacters($newCharacterIds);
                $badCharacter = false;
                foreach ($characters as $character) {
                    if ($character['Character']['user_id'] != $userId) {
                        IERR('Edited a formation with characters that didn\'t belong to them.');
                        $this->Session->setFlash(ERROR_STR);
                        $badCharacter = true;
                        break;
                    }
                }
                if ($badCharacter)
                    break;

                $this->Formation->create();
                $this->data['Formation']['user_id'] = $userId;
                $this->data['Formation']['reputation'] = STARTING_REPUTATION;
                $this->data['Formation']['date_created'] = date(DB_FORMAT);

                $success = $this->Formation->save($this->data);
                if (!$success) {
                    $this->Session->setFlash('Could not create formation.');
                    IERR('Failed to save formation.');
                    break;
                }

                $formationId = $this->Formation->id;

                $success = $this->Formation->UpdateFormationComposition($formationId, $newCharacterIds);
                if (!$success) {
                    $this->Session->setFlash('Could not create formation.');
                    IERR('Failed to save formation.');
                    break;
                }

                $this->Formation->ClearFormationsCacheByUser($this->GameAuth->GetLoggedInUserId());
                $this->Session->setFlash('Formation created.');
                $this->redirect(array('action' => 'index'));
                return;
            } while (false);
        }

        $characterIds = $this->Formation->Character->GetCharacterIdsByUserId($this->GameAuth->GetLoggedInUserId());
        $characters = $this->Formation->Character->GetCharacters($characterIds);
        $this->set('characters', $characters);
    }

    //---------------------------------------------------------------------------------------------
    function edit ($id = null) {
        if ($id == null) {
            $this->fof();
            return;
        }

        if (!empty($this->data)) {
            do {

                if (!$this->CheckCSRFToken())
                    break;

                if (!isset($this->data['Formation']['id']) || !isset($this->data['Formation']['name'])) {
                    IERR('Form data incomplete.');
                    $this->Session->setFlash(ERROR_STR);
                    break;
                }

                $formationId = $this->data['Formation']['id'];

                $userId = $this->GameAuth->GetLoggedInUserId();

                if (empty($this->data['Formation']['name'])) {
                    $this->Session->setFlash('You must have a name.');
                    break;
                }

                if (!isset($this->data['Formation']['characterIds'])) {
                    $this->Session->setFlash('You must select characters!');
                    break;
                }

                $newCharacterIds = $this->data['Formation']['characterIds'];

                if (count($newCharacterIds) > MAX_CHARACTERS_PER_FORMATION) {
                    $this->Session->setFlash('Your formation has too many characters!');
                    break;
                }

                $formation = $this->Formation->GetFormation($formationId);
                if ($formation['Formation']['user_id'] != $userId) {
                    IERR('Edited a formation that didn\'t belong to them.');
                    $this->Session->setFlash(ERROR_STR);
                    break;
                }

                $characters = $this->Formation->Character->GetCharacters($newCharacterIds);
                $badCharacter = false;
                foreach ($characters as $character) {
                    if ($character['Character']['user_id'] != $userId) {
                        IERR('Edited a formation with characters that didn\'t belong to them.');
                        $this->Session->setFlash(ERROR_STR);
                        $badCharacter = true;
                        break;
                    }
                }
                if ($badCharacter)
                    break;

                // If invalid character match
                if (!$this->GameValidate->IsValidFormationName($this->data['Formation']['name'])) {
                    $this->Session->setFlash('That isn\'t a valid formation name.');
                    break;
                }

                $existingFormation = $this->Formation->GetFormationByName($this->data['Formation']['name']);
                if ($existingFormation !== false && $existingFormation['Formation']['id'] != $this->data['Formation']['id']) {
                    $this->Session->setFlash('That name is already taken.');
                    break;
                }

                $this->Formation->id = $formationId;
                $this->Formation->fastSave('name', $this->data['Formation']['name']);
                $this->Formation->fastSave('can_spar', isset($this->data['Formation']['can_spar']) ? $this->data['Formation']['can_spar'] : 0);
                $this->Formation->fastSave('active', isset($this->data['Formation']['active']) ? $this->data['Formation']['active'] : 0);

                $success = $this->Formation->UpdateFormationComposition($formationId, $newCharacterIds);
                $this->Formation->ClearFormationCache($formationId);

                if ($success) {
                    $this->Session->setFlash('Formation saved.');
                } else {
                    IERR('Failed to save formation.');
                    $this->Session->setFlash('Failed to save formation.');
                    break;
                }

                $this->redirect(array('action' => 'view', $formationId));
                return;
            } while (false);
        }

        $formation = $this->Formation->GetFormation($id);
        if ($formation === false) {
            $this->fof();
            return;
        }

        if ($formation['Formation']['user_id'] != $this->GameAuth->GetLoggedInUserId()) {
            $this->fof();
            return;
        }

        $this->setPageTitle('Edit ' . $formation['Formation']['name']);

        $characterIdsInFormation = Set::classicExtract($formation, 'Characters.{n}.Character.id');

        $characterIds = $this->Formation->Character->GetCharacterIdsByUserId($this->GameAuth->GetLoggedInUserId());
        $characters = $this->Formation->Character->GetCharacters($characterIds);
        $this->set('formation', $formation);
        $this->set('characters', $characters);
        $this->set('characterIdsInFormation', $characterIdsInFormation);
    }

    //---------------------------------------------------------------------------------------------
    function move_character () {
        if ($this->ShouldUseAjax()) {
            Configure::write('ajaxMode', 1);

            if (
                !isset($this->params['form']['formationId']) ||
                !isset($this->params['form']['characterId']) ||
                !isset($this->params['form']['endPosition'])
            ) {
                $this->autoRender = false;
                return AJAX_ERROR_CODE;
            }

            $formationId = $this->params['form']['formationId'];
            $characterId = $this->params['form']['characterId'];
            $endPosition = $this->params['form']['endPosition'];

            $formation = $this->Formation->GetFormation($formationId);

            if ($formation['Formation']['user_id'] != $this->GameAuth->GetLoggedInUserId()){
                $this->autoRender = false;
                return AJAX_ERROR_CODE;
            }

            // Assure that the character is in the formation
            $inFormation = false;
            $characterPosition = -1;
            foreach ($formation['Characters'] as $character) {
                if ($character['Character']['id'] == $characterId) {
                    $characterPosition = $character['CharactersFormation']['position'];
                    $inFormation = true;
                }
            }

            if (!$inFormation) {
                return AJAX_ERROR_CODE;
            }

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
    function delete () {
        if (!empty($this->data)) {
            if (!$this->CheckCSRFToken())
                return;

            if (!isset($this->data['Formation']['id'])) {
                $this->fof();
                return;
            }

            $id = $this->data['Formation']['id'];

            $formation = $this->Formation->GetFormation($id);
            if ($formation === false) {
                $this->fof();
                return;
            }

            if ($formation['Formation']['user_id'] != $this->GameAuth->GetLoggedInUserId()){
                $this->fof();
                return;
            }

            $this->Formation->ClearFormationsCacheByUser($this->GameAuth->GetLoggedInUserId());

            if ($this->Formation->del($id)) {
                $this->Session->setFlash('Formation deleted.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Could not delete formation.');
                $this->redirect($this->referer());
            }
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function strategy ($formationId = null) {
        if (!$formationId) {
            $this->fof();
            return;
        }

        $formation = $this->Formation->GetFormation($formationId);
        if ($formation === false) {
            $this->fof();
            return;
        }

        $loggedInId = $this->GameAuth->GetLoggedInUserId();
        if ($formation['Formation']['user_id'] != $loggedInId) {
            $this->fof();
            return;
        }

        $this->setPageTitle($formation['Formation']['name'] . ' Strategy');

        $this->set('formation', $formation);

        $scriptIds = $this->AiScript->GetAiScriptIdsByUserId($loggedInId);
        $scripts = $this->AiScript->GetAiScripts($scriptIds);
        $this->set('scripts', $scripts, true);
    }

    //---------------------------------------------------------------------------------------------
    function create_script ($formationId = null) {
        $this->setPageTitle('Create AI Script');

        if (!empty($this->data)) {
            if (!$this->CheckCSRFToken())
                return;

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
                $this->fof();
                return;
            }

            $formation = $this->Formation->GetFormation($formationId);
            $loggedInId = $this->GameAuth->GetLoggedInUserId();
            if ($formation['Formation']['user_id'] != $loggedInId) {
                $this->fof();
                return;
            }

            $this->set('formation', $formation);
        }
    }

    //---------------------------------------------------------------------------------------------
    function edit_script ($formationId = null, $scriptId = null) {
        if (!empty($this->data)) {

            do {
                if (!$this->CheckCSRFToken())
                    break;

                $script = $this->AiScript->GetAiScript($this->data['AiScript']['id']);
                if ($script === false) {
                    $this->Session->setFlash(ERROR_STR);
                    IERR('Attempted to edit nonexistent script.');
                    break;
                }

                if ($script['AiScript']['user_id'] != $this->GameAuth->GetLoggedInUserId()) {
                    $this->Session->setFlash(ERROR_STR);
                    IERR('Attempted to edit another user\'s script.');
                    break;
                }

                if ($this->AiScript->save($this->data)) {
                    $this->AiScript->ClearAiScriptCache($this->data['AiScript']['id']);
                    $this->Session->setFlash('Script saved.');
                    $this->redirect(array('action' => 'strategy', $this->data['formation_id']));
                    return;
                } else {
                    $this->Session->setFlash('Could not create script.');
                    break;
                }

            } while (false);
        }

        if (!$formationId) {
            $this->fof();
            return;
        }

        if (!$scriptId) {
            $this->fof();
            return;
        }

        $formation = $this->Formation->GetFormation($formationId);
        if ($formation === false) {
            $this->fof();
            return;
        }

        $loggedInId = $this->GameAuth->GetLoggedInUserId();
        if ($formation['Formation']['user_id'] != $loggedInId){
            $this->fof();
            return;
        }

        $this->set('formation', $formation);

        $script = $this->AiScript->GetAiScript($scriptId);
        if ($script === false) {
            $this->fof();
            return;
        }

        if ($script['AiScript']['user_id'] != $loggedInId){
            $this->fof();
            return;
        }

        $this->setPageTitle('Edit ' . $script['AiScript']['name']);
        $this->set('script', $script, true);
    }

    //---------------------------------------------------------------------------------------------
    function delete_script () {
        if (!empty($this->data)) {

            if (!$this->CheckCSRFToken())
                return;

            if (!isset($this->data['formation_id']) || !isset($this->data['script_id'])) {
                $this->Session->setFlash(ERROR_STR);
                IERR('Form data incomplete.');
                $this->redirect($this->referer());
                return;
            }

            $scriptId = $this->data['script_id'];
            $formationId = $this->data['formation_id'];

            $loggedInId = $this->GameAuth->GetLoggedInUserId();
            $script = $this->AiScript->GetAiScript($scriptId);
            if ($script['AiScript']['user_id'] != $loggedInId) {
                $this->Session->setFlash(ERROR_STR);
                IERR('Script does not belong to user.');
                $this->redirect($this->referer());
                return;
            }

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

            $this->Session->setFlash('Script deleted.');
            $this->redirect(array('controller' => 'formations', 'action' => 'strategy', $formationId));
            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function edit_ai () {
        if (!empty($this->data)) {

            if (!$this->CheckCSRFToken())
                return;

            $userId = $this->GameAuth->GetLoggedInUserId();

            $formation = $this->Formation->GetFormation($this->data['formation_id']);
            if ($formation === false) {
                $this->Session->setFlash(ERROR_STR);
                IERR('Formation did not exist.');
                $this->redirect($this->referer());
                return;
            }
            if ($formation['Formation']['user_id'] != $userId) {
                $this->Session->setFlash(ERROR_STR);
                IERR('Formation does not belong to user.');
                $this->redirect($this->referer());
                return;
            }

            foreach ($this->data['CharacterIds'] as $characterId) {
                $character = $this->Formation->Character->GetCharacter($characterId);
                if ($character === false) {
                    $this->Session->setFlash(ERROR_STR);
                    IERR('Character did not exist.');
                    $this->redirect($this->referer());
                    return;
                }

                if ($character['Character']['user_id'] != $userId) {
                    $this->Session->setFlash(ERROR_STR);
                    IERR('Character did not belong to user.');
                    $this->redirect($this->referer());
                    return;
                }
            }

            foreach ($this->data['CharacterSelect'] as $scriptId) {
                if ($scriptId == -1)
                    continue;

                $script = $this->AiScript->GetAiScript($scriptId);
                if ($script === false) {
                    $this->Session->setFlash(ERROR_STR);
                    IERR('Script did not exist.');
                    $this->redirect($this->referer());
                    return;
                }

                if ($script['AiScript']['user_id'] != $userId) {
                    $this->Session->setFlash(ERROR_STR);
                    IERR('Script did not belong to user.');
                    $this->redirect($this->referer());
                    return;
                }
            }

            $this->Formation->UpdateBoundScripts($this->data['formation_id'], $this->data['CharacterIds'], $this->data['CharacterSelect']);
            $this->Session->setFlash('Updated AIs!');
            $this->redirect(array('controller' => 'formations', 'action' => 'strategy', $this->data['formation_id']));
            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function get_formations () {
        if (!$this->ShouldUseAjax())
            $this->fof();

        Configure::write('ajaxMode', 1);
        $this->autoRender = false;

        $user = $this->GameAuth->GetLoggedInUser();

        $userFormationIds = $this->Formation->GetFormationIdsByUserId($user['User']['id']);
        $userFormations = $this->Formation->GetFormationsEx($userFormationIds);

        return json_encode($userFormations);
    }
}
?>
