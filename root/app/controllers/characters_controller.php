<?php

define('NEW_CHARACTER_CACHE', 'new_character');
define('NEW_CHARACTER_CACHE_DURATION', 'file');

class CharactersController extends AppController {

    var $name = 'Characters';

    var $uses = array('Character', 'Formation', 'UserItem');

    //---------------------------------------------------------------------------------------------
    function view ($id = null) {
        if ($id == null) {
            $this->fof();
            return;
        }

        $character = $this->Character->GetCharacterEx($id);
        if ($character === false) {
            $this->fof();
            return;
        }

        if ($character['Character']['user_id'] != $this->GameAuth->GetLoggedInUserId()){
            $this->fof();
            return;
        }

        $this->set('character', $character);
        $this->setPageTitle($character['Character']['name']);
    }

    //--------------------------------------------------------------------------------------------
    function new_character () {
        $this->setPageTitle('New Character');

        if (!empty($this->data)) {
            if (!$this->CheckCSRFToken())
                return;

            $this->Character->create();

            $userId = $this->GameAuth->GetLoggedInUserId();

            // We read all the character data from cache instead of from input so that people
            // can't screw with the values and get uber characters.
            $cacheKey = GenerateCacheKey(NEW_CHARACTER_CACHE, $userId);
            $characterData = Cache::read($cacheKey, NEW_CHARACTER_CACHE_DURATION);

            if (!$characterData) {
                $this->Session->setFlash(ERROR_STR);
                IERR('Attempted to save a rolled character that wasn\'t there.');
                return;
            }

            if (!$this->User->DeductMoney($userId, CHARACTER_KEEP_COST)) {
                $this->Session->setFlash('You don\'t have enough money!');
                return;
            }

            // If invalid character match
            if (!$this->GameValidate->IsValidCharacterName($this->data['Character']['name'])) {
                $this->Session->setFlash('That name is invalid. Names should be alphanumeric and spaces only.');
                return;
            }

            $this->data['Character']['name'] = substr($this->data['Character']['name'], 0, CHARACTER_NAME_MAX_CHARS);
            if ($this->data['Character']['name'] == '')
                $this->data['Character']['name'] = 'Broseph'; // rawful

            // Add character rolled data to data
            $this->data['Character'] = array_merge($this->data['Character'], $characterData);

            $this->data['Character']['user_id'] = $userId;
            $this->data['Character']['class_id'] = 1; // Novice
            $this->data['Character']['date_created'] = date(DB_FORMAT);

            if ($this->Character->save($this->data)) {
                $this->Session->setFlash($this->data['Character']['name'] . ' has joined your party!');
                $this->Character->ClearCharacterIdsCacheByUser($this->GameAuth->GetLoggedInUserId());
                $this->redirect(array('action' => 'view', $this->Character->id));
            } else {
                $this->Session->setFlash('Could not save character.');
                IERR('Could not save character.');
            }
        } else {
            // Later fix
            $this->set('rollCost', CHARACTER_ROLL_COST);
        }
    }

    //---------------------------------------------------------------------------------------------
    function roll_new_character () {
        if ($this->ShouldUseAjax()) {
            Configure::write('ajaxMode', 1);
            $userId = $this->GameAuth->GetLoggedInUserId();
            if (!$this->Character->User->DeductMoney($userId, CHARACTER_ROLL_COST)) {
                return;
            }

            $newCharacter = $this->Character->RollNewCharacter();

            // Save it in the cache for a little while, so when they click accept we just
            // use their latest. We don't accept their form values so people can't screw with
            // the values.
            $cacheKey = GenerateCacheKey(NEW_CHARACTER_CACHE, $userId);
            Cache::write($cacheKey, $newCharacter, NEW_CHARACTER_CACHE_DURATION);

            // Generate pictures for growth so people can't read it so easily
            $newCharacter['growth_str_img'] = $this->Captcha->RenderNumber($newCharacter['growth_str']);
            $newCharacter['growth_vit_img'] = $this->Captcha->RenderNumber($newCharacter['growth_vit']);
            $newCharacter['growth_int_img'] = $this->Captcha->RenderNumber($newCharacter['growth_int']);
            $newCharacter['growth_luk_img'] = $this->Captcha->RenderNumber($newCharacter['growth_luk']);

            $this->set('character', $newCharacter, false);

            return;
        }

        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function levelup ($characterId = null, $amount = null) {
        $user = $this->GameAuth->GetLoggedInUser();
        if ($user['User']['admin'] == 0 && LEVEL_UP_CHEAT == 0) {
            $this->fof();
            return;
        }

        if ($characterId == null) {
            $this->fof();
            return;
        }

        if ($amount == null)
            $amount = 1;

        $this->Character->id = $characterId;
        if ($this->Character->field('user_id') != $this->GameAuth->GetLoggedInUserId()) {
            $this->fof();
            return;
        }

        while ($this->Character->field('level') < CHARACTER_MAX_LEVEL && $amount-- > 0)
            $this->Character->LevelUp($characterId);

        $this->redirect(array('action' => 'view', $characterId));
    }

    //---------------------------------------------------------------------------------------------
    function change_class ($characterId = null) {
        if (!empty($this->data)) {
            do { // Cool shit
                if (!$this->CheckCSRFToken()) {
                    IERR('CSRF token invalid.');
                    $this->Session->setFlash(ERROR_STR);
                    break;
                }

                if (!isset($this->data['Character']['id']) || !isset($this->data['Character']['class_id'])) {
                    IERR('Form data incomplete.');
                    $this->Session->setFlash(ERROR_STR);
                    break;
                }

                $characterId = $this->data['Character']['id'];
                $classId = $this->data['Character']['class_id'];

                $character = $this->Character->GetCharacter($characterId);
                if ($character['Character']['user_id'] != $this->GameAuth->GetLoggedInUserId()) {
                    $this->fof();
                    break;
                }

                if ($this->Character->ChangeClass($characterId, $classId)) {
                    $this->Session->setFlash('Character promoted! Congrats!');
                    $this->redirect(array('action' => 'view', $characterId));
                    return;
                } else {
                    $this->Session->setFlash(ERROR_STR);
                }
            } while (false);
        }

        if ($characterId == null) {
            $this->fof();
            return;
        }

        $character = $this->Character->GetCharacter($characterId);
        if ($character['Character']['user_id'] != $this->GameAuth->GetLoggedInUserId()) {
            $this->fof();
            return;
        }

        $this->setPageTitle(sprintf('Promote %s', $character['Character']['name']));

        $promotionClasses = $this->Character->CClass->GetPromotionClasses($character['Character']['class_id']);
        $baseClasses = $this->Character->CClass->GetBaseClasses($character['Character']['class_id']);

        if ($promotionClasses === false) {
            $this->Session->setFlash('You cannot change classes.');
            $this->redirect(array('action' => 'view', $characterId));
            return;
        }

        // For each promotion class, determine the classes that it can become and list them.
        foreach ($promotionClasses as &$class) {
            $promotions = $this->Character->CClass->GetPromotionClasses($class['CClass']['id']);
            $names = Set::extract($promotions, '{n}.CClass.name');
            $class['CClass']['promotionClassNames'] = $names;
        }

        $this->set('character', $character);
        $this->set('promotionClasses', $promotionClasses, false);
        $this->set('baseClasses', $baseClasses);
    }

    //---------------------------------------------------------------------------------------------
    function delete () {

        if (!empty($this->data)) {
            if (!$this->CheckCSRFToken())
                return;

            if (!isset($this->data['Character']['character_id'])) {
                $this->fof();
                return;
            }

            $id = $this->data['Character']['character_id'];
            $character = $this->Character->GetCharacter($id);
            if (!$character) {
                $this->fof();
                return;
            }

            if ($character['Character']['user_id'] != $this->GameAuth->GetLoggedInUserId()){
                $this->fof();
                return;
            }

            if ($this->Character->del($id)) {
                $this->Character->ClearCharacterExCache($id);
                $this->Character->ClearCharacterIdsCacheByUser($this->GameAuth->GetLoggedInUserId());

                // Clear all formations (more efficient is clear the ones that the character is in)
                $formationIds = $this->Formation->GetFormationIdsByUserId($this->GameAuth->GetLoggedInUserId());
                foreach ($formationIds as $formationId)
                    $this->Formation->ClearFormationExCache($formationId);

                // Clear items
                if (!empty($character['Character']['Weapon']))
                    $this->UserItem->ClearUserItemCache($character['Character']['Weapon']['id']);

                if (!empty($character['Character']['Armor']))
                    $this->UserItem->ClearUserItemCache($character['Character']['Armor']['id']);

                $this->Session->setFlash('Character expelled.');
                $this->redirect(array('controller' => 'army', 'action' => 'index'));
            } else {
                $this->fof();
            }
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function change_name ($id = null) {
        if (!empty($this->data)) {
            do {
                if (!$this->CheckCSRFToken()) {
                    IERR('CSRF token invalid.');
                    $this->Session->setFlash(ERROR_STR);
                    break;
                }

                if (!isset($this->data['Character']['id']) || !isset($this->data['Character']['name'])) {
                    IERR('Form data incomplete.');
                    $this->Session->setFlash(ERROR_STR);
                    break;
                }

                $characterId = $this->data['Character']['id'];
                $name = $this->data['Character']['name'];

                if (!$this->GameValidate->IsValidCharacterName($name)) {
                    $this->Session->setFlash('That name is invalid. Names should be alphanumeric and spaces only.');
                    break;
                }

                $character = $this->Character->GetCharacter($characterId);
                if ($character['Character']['user_id'] != $this->GameAuth->GetLoggedInUserId()) {
                    $this->fof();
                    break;
                }

                if ($this->Character->Rename($characterId, $name)) {
                    $this->Session->setFlash('Character name changed.');
                    $this->redirect(array('action' => 'view', $characterId));
                    return;
                } else {
                    $this->Session->setFlash(ERROR_STR);
                }
            } while (false);
        }

        if ($id == null) {
            $this->fof();
            return;
        }

        $character = $this->Character->GetCharacter($id);
        if ($character === false) {
            $this->fof();
            return;
        }

        if ($character['Character']['user_id'] != $this->GameAuth->GetLoggedInUserId()) {
            $this->fof();
            return;
        }

        $this->set('character', $character);
        $this->setPageTitle($character['Character']['name']);
    }

    //---------------------------------------------------------------------------------------------
    function admi_view ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid character.');
            $this->redirect($this->referer());
        }

        $character = $this->Character->GetCharacter($id);

        if (!$character) {
            $this->Session->setFlash('Invalid character.');
            $this->redirect($this->referer());
        }

        $this->set('character', $character);
    }

    //---------------------------------------------------------------------------------------------
    function admi_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid character.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->Character->save($this->data)) {
                $this->Character->ClearCharacterCache($this->Character->id);
                $this->Session->setFlash('Character saved.');
                $this->redirect(array('action' => 'view', $this->Character->id));
            } else {
                $this->Session->setFlash('Could not save character.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Character->find('first', array(
                'conditions' => array(
                    'id' => $id,
                ),
            ));
        }
        $classes = $this->Character->CClass->find('list');
        $this->set('classes', $classes);
    }

}
?>
