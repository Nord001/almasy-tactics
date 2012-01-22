<?php

define('NEW_CHARACTER_CACHE', 'new_character');

class CharactersController extends AppController {

    var $name = 'Characters';
    var $helpers = array('Html', 'Form');

    var $uses = array('Character', 'Formation', 'UserItem');

    //---------------------------------------------------------------------------------------------
    function view ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid character.');
            $this->redirect($this->referer());
        }

        $character = $this->Character->GetCharacterEx($id);
        if (!$character)
            $this->fof();

        if ($character['Character']['user_id'] != $this->GameAuth->GetLoggedInUserId())
            $this->fof();

        $this->set('character', $character);
        $this->pageTitle = $character['Character']['name'];
    }

    //--------------------------------------------------------------------------------------------
    function new_character () {
        if (!empty($this->data)) {
            $this->Character->create();

            $userId = $this->GameAuth->GetLoggedInUserId();

            // We read all the character data from cache instead of from input so that people
            // can't screw with the values and get uber characters.
            $cacheKey = GenerateCacheKey(NEW_CHARACTER_CACHE, $userId);
            $characterData = Cache::read($cacheKey, 'short');

            if (!$characterData) {
                $this->Session->setFlash('An error has occured.');
                $this->log(sprintf('%s attempted to save a rolled character that wasn\'t there.', $userId));
                return;
            }

            if (!$this->User->DeductMoney($userId, CHARACTER_KEEP_COST)) {
                $this->Session->setFlash('You don\'t have enough money!');
                return;
            }

            // If invalid character match
            if (!$this->GameValidate->IsValidCharacterName($this->data['Character']['name']))
                $this->fof();

            $this->data['Character']['name'] = substr($this->data['Character']['name'], 0, CHARACTER_NAME_MAX_CHARS);
            if ($this->data['Character']['name'] == '')
                $this->data['Character']['name'] = 'Broseph'; // rawful

            // Add character rolled data to data
            $this->data['Character'] = array_merge($this->data['Character'], $characterData);

            $this->data['Character']['user_id'] = $userId;
            $this->data['Character']['class_id'] = 1; // Novice
            $this->data['Character']['date_created'] = date("Y-m-d H:i:s");

            if ($this->Character->save($this->data)) {
                $this->Session->setFlash($this->data['Character']['name'] . ' has joined your party!');
                $this->Character->ClearCharacterIdsCacheByUser($this->GameAuth->GetLoggedInUserId());
                $this->redirect(array('action' => 'view', $this->Character->id));
            } else {
                $this->Session->setFlash('Could not save character.');
            }
        } else {
            // Later fix
            $this->set('rollCost', CHARACTER_ROLL_COST);
        }
    }

    //---------------------------------------------------------------------------------------------
    function roll_new_character () {
        if ($this->ShouldUseAjax()) {

            $userId = $this->GameAuth->GetLoggedInUserId();
            if (!$this->Character->User->DeductMoney($userId, CHARACTER_ROLL_COST)) {
                $this->log(sprintf('%s attempted to roll a character without having the money', $userId));
                return;
            }

            $newCharacter = $this->Character->RollNewCharacter();

            // Save it in the cache for a little while, so when they click accept we just
            // use their latest. We don't accept their form values so people can't screw with
            // the values.
            $cacheKey = GenerateCacheKey(NEW_CHARACTER_CACHE, $userId);
            Cache::write($cacheKey, $newCharacter, 'short');

            $this->set('character', $newCharacter);

            return;
        }

        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function levelup ($characterId = null, $amount = null) {
        if (LEVEL_UP_CHEAT == 0)
            $this->fof();

        if ($characterId == null)
            $this->fof();

        if ($amount == null)
            $amount = 1;

        $this->Character->id = $characterId;
        if ($this->Character->field('user_id') != $this->GameAuth->GetLoggedInUserId())
            $this->fof();

        while ($this->Character->field('level') < CHARACTER_MAX_LEVEL && $amount-- > 0)
            $this->Character->LevelUp($characterId);

        $this->redirect(array('action' => 'view', $characterId));
    }

    //---------------------------------------------------------------------------------------------
    function change_class ($characterId = null, $classId = null) {
        if ($characterId == null) {
            $this->fof();
        }

        $this->Character->id = $characterId;
        if ($this->Character->field('user_id') != $this->GameAuth->GetLoggedInUserId())
            $this->fof();

        if ($classId != null) {
            $this->Character->ChangeClass($characterId, $classId);
            $this->redirect(array('action' => 'view', $characterId));
        } else {
            $this->Character->id = $characterId;
            $currentClassId = $this->Character->field('class_id');
            $promotionClasses = $this->Character->CClass->GetPromotionClasses($currentClassId);

            $this->set('character', $this->Character->read());
            $this->set('promotionClasses', $promotionClasses);
        }
    }

    //---------------------------------------------------------------------------------------------
    function delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for character.');
            $this->redirect(array('action' => 'index'));
        }

        $character = $this->Character->GetCharacter($id);
        if (!$character)
            $this->fof();

        if ($character['Character']['user_id'] != $this->GameAuth->GetLoggedInUserId())
            $this->fof();

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

}
?>