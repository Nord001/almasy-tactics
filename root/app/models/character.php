<?

// Caches the user's characters
define('CHARACTERS_BY_USER_CACHE', 'user_characters');
define('CHARACTERS_BY_USER_CACHE_DURATION', 'long');

// Caches individual characters
define('CHARACTER_CACHE', 'character');
define('CHARACTER_CACHE_DURATION', 'fifteen_min');

define('CHARACTER_STATS_CACHE', 'character_stats');
define('CHARACTER_STATS_CACHE_DURATION', 'fifteen_min');

define('TOP_CHARACTERS_BY_WINS_CACHE', 'characters_top_wins');
define('TOP_CHARACTERS_BY_WINS_CACHE_DURATION', 'short');


class Character extends AppModel {

    var $belongsTo = array(
        'CClass' => array(
            'className' => 'CClass',
            'foreignKey' => 'class_id',
        ),
        'User',
        'Weapon' => array(
            'className' => 'UserItem',
        ),
        'Armor' => array(
            'className' => 'UserItem',
        ),
    );

    var $hasAndBelongsToMany = array(
        'Formation' => array(
            'className' => 'Formation',
            'joinTable' => 'characters_formations',
            'foreignKey' => 'character_id',
            'associationForeignKey' => 'formation_id',
            'unique' => true
        ),
    );

    var $knows = array('UserItem', 'Monster');

    //--------------------------------------------------------------------------------------------
    function ClearCharacterExCache ($characterId) {
        CheckNumeric($characterId);

        $this->ClearCharacterStatsCache($characterId);
        $this->ClearCharacterCache($characterId);
    }

    //--------------------------------------------------------------------------------------------
    function ClearCharacterStatsCache ($characterId) {
        CheckNumeric($characterId);

        $cacheKey = GenerateCacheKey(CHARACTER_STATS_CACHE, $characterId);
        Cache::delete($cacheKey, CHARACTER_STATS_CACHE_DURATION);

        // When character stats are cleared, must clean formation stats too
        // because those are based off of character stats
        $formationIds = $this->query("
            SELECT
                formations.id
            FROM
                formations
            INNER JOIN
                characters_formations ON characters_formations.formation_id = formations.id
            INNER JOIN
                characters ON characters_formations.character_id = characters.id
            WHERE
                characters.id = {$characterId}"
        );
        $formationIds = Set::classicExtract($formationIds, '{n}.formations.id');

        foreach ($formationIds as $formationId)
            $this->Formation->ClearFormationStatsCache($formationId);
    }

    //--------------------------------------------------------------------------------------------
    function ClearCharacterCache ($characterId) {
        CheckNumeric($characterId);

        $cacheKey = GenerateCacheKey(CHARACTER_CACHE, $characterId);
        Cache::delete($cacheKey, CHARACTER_CACHE_DURATION);

        // When character is cleared, must clear related formation data
        $formationIds = $this->query("
            SELECT
                formations.id
            FROM
                formations
            INNER JOIN
                characters_formations ON characters_formations.formation_id = formations.id
            INNER JOIN
                characters ON characters_formations.character_id = characters.id
            WHERE
                characters.id = {$characterId}"
        );
        $formationIds = Set::classicExtract($formationIds, '{n}.formations.id');

        foreach ($formationIds as $formationId)
            $this->Formation->ClearFormationCache($formationId);
    }

    //--------------------------------------------------------------------------------------------
    function ClearCharacterIdsCacheByUser ($userId) {
        CheckNumeric($userId);

        $cacheKey = GenerateCacheKey(CHARACTERS_BY_USER_CACHE, $userId);
        Cache::delete($cacheKey, CHARACTERS_BY_USER_CACHE_DURATION);
    }


    //--------------------------------------------------------------------------------------------
    function GetCharacterIdsByUserId ($userId) {
        CheckNumeric($userId);

        $cacheKey = GenerateCacheKey(CHARACTERS_BY_USER_CACHE, $userId);
        $characterIds = Cache::read($cacheKey, CHARACTERS_BY_USER_CACHE_DURATION);

        if ($characterIds)
            return $characterIds;

        $characterIds = $this->find('all', array(
            'fields' => array(
                'Character.id',
            ),
            'conditions' => array(
                'Character.user_id' => $userId,
            ),
        ));
        $characterIds = Set::classicExtract($characterIds, '{n}.Character.id');

        Cache::write($cacheKey, $characterIds, CHARACTERS_BY_USER_CACHE_DURATION);
        return $characterIds;
    }

    //--------------------------------------------------------------------------------------------
    function GetCharacterStats ($characterId) {
        CheckNumeric($characterId);

        $cacheKey = GenerateCacheKey(CHARACTER_STATS_CACHE, $characterId);
        $data = Cache::read($cacheKey, CHARACTER_STATS_CACHE_DURATION);

        if ($data === false) {
            try {
                $client = Network::GetInstance()->GetGameClient();
                $data = $client->getCharacterStats($characterId);
            } catch (TException $e) {
                IERR('Error getting character stats: ' . $e->getMessage());
            }

            if ($data === false)
                return false;

            $data = json_decode($data, true);

            Cache::write($cacheKey, $data, CHARACTER_STATS_CACHE_DURATION);
        }

        return $data;
    }

    //--------------------------------------------------------------------------------------------
    function GetCharacterEx ($characterId) {
        CheckNumeric($characterId);

        $character = $this->GetCharacter($characterId);
        if ($character === false)
            return false;

        $character['Character']['Stats'] = $this->GetCharacterStats($characterId);
        return $character;
    }

    //--------------------------------------------------------------------------------------------
    function GetCharactersEx ($characterIds) {
        $data = array();
        foreach ($characterIds as $characterId)
            $data[] = $this->GetCharacterEx($characterId);

        return $data;
    }

    //--------------------------------------------------------------------------------------------
    function GetCharacter ($characterId) {
        CheckNumeric($characterId);

        $cacheKey = GenerateCacheKey(CHARACTER_CACHE, $characterId);
        $character = Cache::read($cacheKey, CHARACTER_CACHE_DURATION);

        if ($character === false) {
            $character = $this->find('first', array(
                'conditions' => array(
                    'Character.id' => $characterId,
                ),
            ));

            if ($character === false)
                return false;

            // Get total exp
            App::import('Model', 'Experience');
            $Experience = new Experience;
            $character['Character']['total_exp_to_next_level'] = $Experience->GetExpForNextLevel($character['Character']['level']);

            $formationData = $this->query("
                SELECT
                    `formation_id`
                FROM
                    `characters_formations`
                WHERE
                    `character_id` = {$characterId}
                LIMIT 1"
            );
            if (!empty($formationData))
                $character['Character']['formation_id'] = $formationData[0]['characters_formations']['formation_id'];
            else
                $character['Character']['formation_id'] = false;

            Cache::write($cacheKey, $character, CHARACTER_CACHE_DURATION);
        }

        // Fetch items specially and use those
        $weaponId = $character['Character']['weapon_id'];
        $armorId = $character['Character']['armor_id'];

        if ($weaponId) {
            $weapon = $this->UserItem->GetUserItem($weaponId);
            $character['Character']['Weapon'] = $weapon['UserItem'];
        } else {
            $character['Character']['Weapon'] = array();
        }

        if ($armorId) {
            $armor = $this->UserItem->GetUserItem($armorId);
            $character['Character']['Armor'] = $armor['UserItem'];
        } else {
            $character['Character']['Armor'] = array();
        }

        $class = $this->CClass->GetClass($character['Character']['class_id']);
        if ($class === false) {
            $monster = $this->Monster->GetMonster($character['Character']['class_id']);
            $character['CClass'] = $monster['Monster'];
        } else {
            $character['CClass'] = $class['CClass'];

            // Get promotion info
            $minPromotionLevel = $character['CClass']['promote_class_1_level'];
            for ($i = 1; $i <= 4; $i++) {
                $promotionLevel = $character['CClass']['promote_class_' . $i . '_level'];
                if ($promotionLevel != '' && $promotionLevel != 0)
                    $minPromotionLevel = min($minPromotionLevel, $promotionLevel);
            }
            if ($minPromotionLevel == 0)
                $canPromote = false;
            else
                $canPromote = ($character['Character']['level'] >= $minPromotionLevel);

            // Can promote means character is leveled up enough to promote.
            // Has promotions means character still can promote at some point.
            $character['Character']['can_promote'] = $canPromote;
            $character['Character']['has_promotions'] = ($minPromotionLevel != '' && $minPromotionLevel != 0);
        }

        return $character;
    }

    //--------------------------------------------------------------------------------------------
    function GetCharacters ($characterIds) {
        $data = array();
        foreach ($characterIds as $characterId)
            $data[] = $this->GetCharacter($characterId);

        return $data;
    }

    //--------------------------------------------------------------------------------------------
    function RollNewCharacter () {
        $stats = array(
            'str' => 0,
            'vit' => 0,
            'int' => 0,
            'luk' => 0,
        );

        $growths = array(
            'growth_str' => 0,
            'growth_vit' => 0,
            'growth_int' => 0,
            'growth_luk' => 0,
        );

        foreach ($stats as &$stat)
            $stat = mt_rand(1, 10);

        foreach ($growths as &$growth) {
            $growth = NormalDistribution(CHARACTER_STAT_ROLL_MEAN, CHARACTER_STAT_ROLL_VARIANCE);
            $growth = round($growth, 1);
            if ($growth < 1)
                $growth = 1;
            $growth = min($growth, CHARACTER_MAX_ROLL_GROWTH);
        }

        $affinity = mt_rand(0, NUM_AFFINITIES - 1);

        $character = array_merge(
            $stats,
            $growths,
            array('affinity' => $affinity)
        );

        return $character;
    }

    //--------------------------------------------------------------------------------------------
    function LevelUp ($characterId, $levels = 1) {
        CheckNumeric($characterId);
        CheckNumeric($levels);

        if ($levels == 0) return;

        $character = $this->GetCharacter($characterId);

        for ($i = 0; $i < $levels; $i++) {
            $character['Character']['str'] += $character['Character']['growth_str'];
            $character['Character']['int'] += $character['Character']['growth_int'];
            $character['Character']['vit'] += $character['Character']['growth_vit'];
            $character['Character']['luk'] += $character['Character']['growth_luk'];
        }

        $character['Character']['level'] += $levels;

        $this->save($character);
        $this->ClearCharacterExCache($characterId);
    }

    //--------------------------------------------------------------------------------------------
    function GainExp ($characterId, $exp) {
        CheckNumeric($characterId);
        CheckNumeric($exp);

        App::import('Model', 'Experience');
        $Experience = new Experience;

        // Load fields
        $this->id = $characterId;

        $character = $this->GetCharacter($characterId);
        $startLevel = $character['Character']['level'];
        $level = $startLevel;
        $currentExp = $character['Character']['exp'];
        $currentExp += $exp;

        // Simulate leveling up until you can't level up anymore
        $expToNextLevel = $Experience->GetExpForNextLevel($level);
        while($currentExp > $expToNextLevel) {
            if ($level >= CHARACTER_MAX_LEVEL)
                break;
            $currentExp -= $expToNextLevel;
            $level++;
            $expToNextLevel = $Experience->GetExpForNextLevel($level);
        }

        $levelChange = $level - $startLevel;
        if ($levelChange > MAX_LEVEL_GAIN) {
            $levelChange = MAX_LEVEL_GAIN;
            $level = $startLevel + MAX_LEVEL_GAIN;
            $currentExp = 0;
        }

        $this->LevelUp($characterId, $levelChange);

        if ($level != $startLevel)
            $this->fastSave('level', $level);

        $this->fastSave('exp', $currentExp);

        $this->ClearCharacterExCache($characterId);
    }

    //--------------------------------------------------------------------------------------------
    function ChangeClass ($characterId, $classId) {
        CheckNumeric($characterId);
        CheckNumeric($classId);

        $this->id = $characterId;
        $character = $this->GetCharacter($characterId);
        $currentClassId = $character['Character']['class_id'];
        $level = $character['Character']['level'];
        $promotionClasses = $this->CClass->GetPromotionClasses($currentClassId);

        foreach ($promotionClasses as $class) {
            if ($class['CClass']['id'] == $classId && $level >= $class['CClass']['required_level']) {
                $character = $this->GetCharacter($characterId);

                $character['Character']['growth_str'] += $class['CClass']['growth_str'];
                $character['Character']['growth_int'] += $class['CClass']['growth_int'];
                $character['Character']['growth_vit'] += $class['CClass']['growth_vit'];
                $character['Character']['growth_luk'] += $class['CClass']['growth_luk'];

                // If level is above the required level, compensate for the growth they missed
                $levelDiff = $level - $class['CClass']['required_level'];
                if ($levelDiff > 0) {
                    $character['Character']['str'] += $class['CClass']['growth_str'] * $levelDiff;
                    $character['Character']['int'] += $class['CClass']['growth_int'] * $levelDiff;
                    $character['Character']['vit'] += $class['CClass']['growth_vit'] * $levelDiff;
                    $character['Character']['luk'] += $class['CClass']['growth_luk'] * $levelDiff;
                }

                $character['Character']['class_id'] = $class['CClass']['id'];

                $this->save($character);
                $this->ClearCharacterExCache($characterId);

                // Reequip items just in case you switched to a class that no longer allows you to use
                // an equipped item

                if (!empty($character['Character']['Weapon'])) {
                    $userItemId = $character['Character']['Weapon']['id'];
                    $this->UnequipItem($userItemId);
                    $success = $this->EquipItem($characterId, $userItemId);
                }

                return true;
            }
        }

        // Should not reach here if character is changing to a valid class
        IERR('Could not change class.', array(
            'characterId' => $character['Character']['id'],
            'classId' => $character['Character']['class_id'],
            'targetId' => $classId
        ));

        return false;
    }

    //---------------------------------------------------------------------------------------------
    function EquipItem ($characterId, $userItemId) {
        CheckNumeric($characterId);
        CheckNumeric($userItemId);

        $this->id = $characterId;

        $this->UserItem->id = $userItemId;

        $character = $this->GetCharacter($characterId);

        $item = $this->UserItem->Item->GetItem($this->UserItem->field('item_id'));

        // Can't equip if you aren't leveled enough!
        if ($character['Character']['level'] < $item['Item']['req_lvl'])
            return false;

        // Can't equip if your class can't equip it
        if ($item['Item']['weapon_type_id'] != '') {
            $classEquipData = $this->CClass->GetWeaponEquipInfo();
            if (!isset($classEquipData[$character['Character']['class_id']]))
                return false;

            $data = $classEquipData[$character['Character']['class_id']];

            if (in_array($item['Item']['weapon_type_id'], $data, true) === false)
                return false;

        }

        // Save into correct slot
        $oldId = '';
        if ($item['Item']['weapon_type_id'] != '') {
            $oldId = $character['Character']['weapon_id'];
            $this->fastSave('weapon_id', $userItemId);
        } else if ($item['Item']['armor_type_id'] != '') {
            $oldId = $character['Character']['armor_id'];
            $this->fastSave('armor_id', $userItemId);
        } else {
            UERR('You cannot equip this item.');
        }

        if ($oldId != '')
            $this->UserItem->ClearUserItemCache($oldId);

        $this->UserItem->ClearUserItemCache($userItemId);
        $this->ClearCharacterExCache($characterId);

        return true;
    }

    //---------------------------------------------------------------------------------------------
    // Returns the character that used to have it equipped
    function UnequipItem ($userItemId) {
        CheckNumeric($userItemId);

        // Search for the character that has it equipped and unequip it.
        $character = $this->find('first', array(
            'fields' => array(
                'Character.id',
                'Character.weapon_id',
                'Character.armor_id',
            ),
            'conditions' => array(
                'OR' => array(
                    'Character.weapon_id' => $userItemId,
                    'Character.armor_id' => $userItemId,
                ),
            ),
        ));

        if ($character) {
            $this->id = $character['Character']['id'];
            if ($character['Character']['weapon_id'] == $userItemId)
                $this->fastSave('weapon_id', '');
            else
                $this->fastSave('armor_id', '');

            $this->UserItem->ClearUserItemCache($userItemId);
            $this->ClearCharacterExCache($this->id);

            return $this->id;
        }
        return false;
    }

    //---------------------------------------------------------------------------------------------
    function GetTopCharacterIdsByWins () {
        $characterIds = Cache::read(TOP_CHARACTERS_BY_WINS_CACHE, TOP_CHARACTERS_BY_WINS_CACHE_DURATION);
        if ($characterIds !== false)
            return $characterIds;

        $characterIds = $this->find('all', array(
            'fields' => array(
                'Character.id'
            ),
            'order' => 'Character.battles_won DESC',
            'limit' => 5,
        ));
        $characterIds = Set::classicExtract($characterIds, '{n}.Character.id');

        Cache::write(TOP_CHARACTERS_BY_WINS_CACHE, $characterIds, TOP_CHARACTERS_BY_WINS_CACHE_DURATION);

        return $characterIds;
    }

    //---------------------------------------------------------------------------------------------
    function Rename ($characterId, $name) {
        CheckNumeric($characterId);

        $character = $this->GetCharacter($characterId);
        if ($character['Character']['has_custom_name'] == 1)
            return false;

        $this->id = $characterId;
        $this->fastSave('name', $name);
        $this->fastSave('has_custom_name', 1);

        $this->ClearCharacterCache($characterId);

        return true;
    }

    //---------------------------------------------------------------------------------------------
    function GiveCharacterToUser ($characterId, $userId) {
        CheckNumeric($characterId);
        CheckNumeric($userId);

        $character = $this->GetCharacter($characterId);
        if ($character === false)
            return false;

        unset($character['Character']['id']);
        $character['Character']['user_id'] = $userId;
        $success = $this->save($character['Character']);
        $this->ClearCharacterIdsCacheByUser($userId);
        return $success !== false;
    }
}

?>
