<?

// Caches individual formations
define('FORMATION_CACHE', 'formation');
define('FORMATION_CACHE_DURATION', 'file');

define('FORMATION_STATS_CACHE', 'formation_stats');
define('FORMATION_STATS_CACHE_DURATION', 'file');

// Caches the formations of a user
define('FORMATIONS_BY_USER_CACHE', 'user_formations');
define('FORMATIONS_BY_USER_CACHE_DURATION', 'long');

define('TOP_FORMATIONS_BY_WINS_CACHE', 'formations_top_wins');
define('TOP_FORMATIONS_BY_WINS_CACHE_DURATION', 'fifteen_min');

define('FORMATION_RANKINGS_CACHE', 'formation_rankings');
define('FORMATION_RANKINGS_CACHE_DURATION', 'fifteen_min');

define('FORMATION_RANKINGS_COUNT_CACHE', 'formation_rankings_count');
define('FORMATION_RANKINGS_COUNT_CACHE_DURATION', 'fifteen_min');

define('TOP_FORMATIONS_BY_BOUNTY_CACHE', 'formations_bounty');
define('TOP_FORMATIONS_BY_BOUNTY_CACHE_DURATION', 'fifteen_min');

class Formation extends AppModel {

    var $belongsTo = array(
        'User',
    );

    var $hasAndBelongsToMany = array(
        'Character' => array(
            'className' => 'Character',
            'joinTable' => 'characters_formations',
            'foreignKey' => 'formation_id',
            'associationForeignKey' => 'character_id',
            'unique' => true
        ),
    );

    var $hasMany = array(
        'Battle',
    );

    //---------------------------------------------------------------------------------------------
    function ClearFormationExCache ($formationId) {
        CheckNumeric($formationId);

        $this->ClearFormationCache($formationId);
        $this->ClearFormationStatsCache($formationId);
    }

    //---------------------------------------------------------------------------------------------
    function ClearFormationStatsCache ($formationId) {
        CheckNumeric($formationId);

        $cacheKey = GenerateCacheKey(FORMATION_STATS_CACHE, $formationId);
        Cache::delete($cacheKey, FORMATION_STATS_CACHE_DURATION);
    }

    //---------------------------------------------------------------------------------------------
    // Clears the cache of an formation.
    function ClearFormationCache ($formationId) {
        CheckNumeric($formationId);

        $cacheKey = GenerateCacheKey(FORMATION_CACHE, $formationId);
        Cache::delete($cacheKey, FORMATION_CACHE_DURATION);
    }

    //---------------------------------------------------------------------------------------------
    // Clears the cache of a user's cached formation ids.
    function ClearFormationsCacheByUser ($userId) {
        CheckNumeric($userId);

        $cacheKey = GenerateCacheKey(FORMATIONS_BY_USER_CACHE, $userId);
        Cache::delete($cacheKey, FORMATIONS_BY_USER_CACHE_DURATION);
    }

    //---------------------------------------------------------------------------------------------
    function GetFormationStats ($formationId) {
        CheckNumeric($formationId);

        $cacheKey = GenerateCacheKey(FORMATION_STATS_CACHE, $formationId);
        $result = Cache::read($cacheKey, FORMATION_STATS_CACHE_DURATION);

        if ($result === false) {
            try {
                $client = Network::GetInstance()->GetGameClient();
                $data = $client->getFormationStats($formationId);
            } catch (TException $e) {
                IERR('Error getting formation stats: ' . $e->getMessage());
            }

            if ($data === false) {
                IERR('Failed to get formation stats for formation ' . $formationId);
                return false;
            }

            $characters = explode("\n", $data);

            $result = array();
            for ($i = 0; $i < count($characters); $i++) {
                $character = $characters[$i];
                if ($character == "null")
                    continue;

                $result[$i] = json_decode($character, true);
            }

            Cache::write($cacheKey, $result, FORMATION_STATS_CACHE_DURATION);
        }

        return $result;
    }

    //---------------------------------------------------------------------------------------------
    function GetFormationEx ($formationId) {
        CheckNumeric($formationId);

        $formation = $this->GetFormation($formationId);
        if ($formation === false)
            return false;

        $formationStats = $this->GetFormationStats($formationId);
        foreach ($formation['Characters'] as &$character) {
            $position = $character['CharactersFormation']['position'];
            $character = $this->Character->GetCharacterEx($character['Character']['id']);
            $character['CharactersFormation']['Stats'] = $formationStats[$position];
        }
        return $formation;
    }

    //---------------------------------------------------------------------------------------------
    function GetCharacterDataFromFormationId ($formationId) {
        CheckNumeric($formationId);

        $data = $this->query("
            SELECT
                `character_id`, `position`, `script_id`
            FROM
                `characters_formations`
            WHERE
                `formation_id` = {$formationId}"
        );
        $characterData = array();
        foreach ($data as $char) {
            $characterData[] = array(
                'position' => $char['characters_formations']['position'],
                'character_id' => $char['characters_formations']['character_id'],
                'script_id' => $char['characters_formations']['script_id'],
            );
        }
        return $characterData;
    }

    //---------------------------------------------------------------------------------------------
    function GetFormation ($formationId) {
        CheckNumeric($formationId);

        $cacheKey = GenerateCacheKey(FORMATION_CACHE, $formationId);
        $formation = Cache::read($cacheKey, FORMATION_CACHE_DURATION);

        if (!$formation) {
            $formation = $this->find('first', array(
                'conditions' => array(
                    'Formation.id' => $formationId,
                ),
            ));

            if (!$formation)
                return false;

            $formation['Characters'] = array();

            $characterData = $this->GetCharacterDataFromFormationId($formationId);

            // We're caching characters inside formation just because we have to clear formation
            // cache anyways whenever a character changes, in order to recalculate the stats.
            $positions = array_fill(0, FORMATION_WIDTH * FORMATION_HEIGHT, -1);
            $n = 0;
            foreach ($characterData as $char) {
                $position = $char['position'];
                $character = $this->Character->GetCharacter($char['character_id']);
                $character['CharactersFormation']['position'] = $position;
                $character['CharactersFormation']['script_id'] = $char['script_id'];

                $formation['Characters'][] = $character;

                $positions[$position] = $n++;
            }

            // Calculate average character level.
            $levels = Set::classicExtract($formation['Characters'], '{n}.Character.level');
            $avgLevel = count($levels) > 0 ? array_sum($levels) / count($levels) : 0;
            $formation['Formation']['avg_character_level'] = $avgLevel;

            $formation['Formation']['total_battles'] = $formation['Formation']['battles_won'] + $formation['Formation']['battles_lost'];

            // array of positions -> character indexes into $formation['Characters']
            $formation['CharacterFormation'] = $positions;

            Cache::write($cacheKey, $formation, FORMATION_CACHE_DURATION);
        }

        return $formation;
    }

    //---------------------------------------------------------------------------------------------
    function GetFormationsEx ($formationIds) {
        $data = array();
        foreach ($formationIds as $formationId)
            $data[] = $this->GetFormationEx($formationId);

        return $data;
    }


    //---------------------------------------------------------------------------------------------
    function GetFormations ($formationIds) {
        $data = array();
        foreach ($formationIds as $formationId)
            $data[] = $this->GetFormation($formationId);

        return $data;
    }

    //---------------------------------------------------------------------------------------------
    function GetFormationIdsByUserId ($userId) {
        CheckNumeric($userId);

        $cacheKey = GenerateCacheKey(FORMATIONS_BY_USER_CACHE, $userId);
        $formationIds = Cache::read($cacheKey, FORMATIONS_BY_USER_CACHE_DURATION);
        if ($formationIds)
            return $formationIds;

        $formationIds = $this->find('all', array(
            'fields' => array(
                'Formation.id',
            ),
            'conditions' => array(
                'Formation.user_id' => $userId,
            ),
        ));
        $formationIds = Set::classicExtract($formationIds, '{n}.Formation.id');

        Cache::write($cacheKey, $formationIds, FORMATIONS_BY_USER_CACHE_DURATION);

        return $formationIds;
    }

    //---------------------------------------------------------------------------------------------
    function GetDefaultReputation ($formationId) {
        CheckNumeric($formationId);

        $formation = $this->GetFormation($formationId);

        $reputation = 0;
        foreach ($formation['Characters'] as $character) {
            $level = $character['Character']['level'];
            $reputation += 0.4 * $level * $level + $level;
        }

        $reputation = intval($reputation);
        $reputation = Clamp($reputation, MIN_REPUTATION, MAX_REPUTATION);

        return $reputation;
    }

    //---------------------------------------------------------------------------------------------
    function ResetReputation ($formationId) {
        CheckNumeric($formationId);

        $reputation = $this->GetDefaultReputation($formationId);

        $this->id = $formationId;
        $success = $this->fastSave('reputation', $reputation);
        if ($success === false) {
            IERR('Failed to reset reputation.');
            return false;
        }
        $this->ClearFormationCache($formationId);

        return true;
    }

    //---------------------------------------------------------------------------------------------
    // Updates the formation to consist of the given characterIds.
    // Makes sure that new characters are put in logical positions.
    function UpdateFormationComposition ($formationId, $newCharacterIds) {
        CheckNumeric($formationId);
        CheckNumeric($newCharacterIds);

        $formation = $this->GetFormation($formationId);
        $characterIds = Set::classicExtract($formation, 'Characters.{n}.Character.id');

        $toRemoveIds = array_diff($characterIds, $newCharacterIds);
        if (count($toRemoveIds) > 0) {
            $this->query(sprintf('
                DELETE FROM
                    characters_formations
                WHERE
                    formation_id = %s AND
                    character_id IN (%s)',
                    $formationId,
                    implode(',', $toRemoveIds)
                ));
        }

        $takenPositions = $this->query("
            SELECT
                position
            FROM
                characters_formations
            WHERE
                formation_id = {$formationId}");
        $takenPositions = Set::classicExtract($takenPositions, '{n}.characters_formations.position');
        $availablePositions = array_diff(range(0, 11), $takenPositions);

        $toAddIds = array_diff($newCharacterIds, $characterIds);
        if (count($toAddIds) > 0) {

            if (count($toAddIds) > count($availablePositions))
                return false;

            foreach ($toAddIds as $id) {
                $newPosition = array_shift($availablePositions);
                $this->query("
                    INSERT INTO
                        characters_formations
                    (
                        character_id,
                        formation_id,
                        position
                    ) VALUES
                    (
                        {$id},
                        {$formationId},
                        {$newPosition}
                    )");
            }
        }

        $noActionTaken = count($toRemoveIds) == 0 && count($toAddIds) == 0;

        $this->ClearFormationExCache($formationId);

        if (!$noActionTaken) {
            // Recalculate reputation.
            $this->ResetReputation($formationId);

            $this->id = $formationId;
            $this->fastSave('bounty', 0);
        }

        return true;
    }

    //---------------------------------------------------------------------------------------------
    function MoveCharacterToEmptySpot ($formationId, $characterId, $endPosition) {
        CheckNumeric($formationId);
        CheckNumeric($characterId);

        $this->query("
            UPDATE
                characters_formations
            SET
                position = {$endPosition}
            WHERE
                formation_id = {$formationId} AND
                character_id = {$characterId}
            ");
        $this->ClearFormationExCache($formationId);
    }

    //---------------------------------------------------------------------------------------------
    function SwapCharacters($formationId, $characterId, $characterPosition, $otherCharacterPosition) {
        CheckNumeric($formationId);
        CheckNumeric($characterId);
        CheckNumeric($characterPosition);
        CheckNumeric($otherCharacterPosition);

        $this->query("START TRANSACTION");
        $this->query("
            UPDATE
                characters_formations
            SET
                position = {$otherCharacterPosition}
            WHERE
                formation_id = {$formationId} AND
                character_id = {$characterId}
            ");
        $this->query("
            UPDATE
                characters_formations
            SET
                position = {$characterPosition}
            WHERE
                formation_id = {$formationId} AND
                character_id <> {$characterId} AND
                position = {$otherCharacterPosition}
            ");

        $this->query("COMMIT");

        $this->ClearFormationExCache($formationId);
    }

    //---------------------------------------------------------------------------------------------
    function AwardBattleExp ($formationId, $exp) {
        CheckNumeric($formationId);
        CheckNumeric($exp);
        if ($exp < 0)
            return false;
        if ($exp == 0)
            return true;

        $formation = $this->GetFormation($formationId);

        $resultStr = '';
        if (!empty($formation['Characters'])) {
            foreach ($formation['Characters'] as $character) {
                $levelDiff = abs($formation['Formation']['avg_character_level'] - $character['Character']['level']);
                $expMultiplier = 1 - 1 / (1 + exp(-(($levelDiff - 20) / 4)));

                $awarded = intval($exp * $expMultiplier);
                $this->Character->GainExp($character['Character']['id'], $awarded);

                $resultStr .= sprintf("%s gained %s exp!\n", $character['Character']['name'], $awarded);
            }
        }

        return $resultStr;
    }

    //---------------------------------------------------------------------------------------------
    function GiveVictoryToCharacters ($formationId) {
        CheckNumeric($formationId);

        $this->query("
            UPDATE
                `characters` AS `C`
            INNER JOIN
                `characters_formations` AS `CF` ON `CF`.`character_id` = `C`.`id`
            SET
                battles_won = battles_won + 1
            WHERE
                formation_id = {$formationId}");

        $formation = $this->GetFormation($formationId);
        foreach ($formation['Characters'] as $character) {
            $this->Character->ClearCharacterCache($character['Character']['id']);
        }
    }

    //---------------------------------------------------------------------------------------------
    function GiveLossToCharacters ($formationId) {
        CheckNumeric($formationId);

        $this->query("
            UPDATE
                `characters` AS `C`
            INNER JOIN
                `characters_formations` AS `CF` ON `CF`.`character_id` = `C`.`id`
            SET
                `C`.`battles_lost` = `C`.`battles_lost` + 1
            WHERE
                formation_id = {$formationId}");

        $formation = $this->GetFormation($formationId);
        foreach ($formation['Characters'] as $character) {
            $this->Character->ClearCharacterCache($character['Character']['id']);
        }
    }

    //---------------------------------------------------------------------------------------------
    function GetTopFormationIdsByWins () {
        $formationIds = Cache::read(TOP_FORMATIONS_BY_WINS_CACHE, TOP_FORMATIONS_BY_WINS_CACHE_DURATION);
        if ($formationIds !== false)
            return $formationIds;

        $formationIds = $this->find('all', array(
            'order' => 'Formation.battles_won DESC',
            'limit' => 5,
        ));
        $formationIds = Set::classicExtract($formationIds, '{n}.Formation.id');
        Cache::write(TOP_FORMATIONS_BY_WINS_CACHE, $formationIds, TOP_FORMATIONS_BY_WINS_CACHE_DURATION);

        return $formationIds;
    }

    //---------------------------------------------------------------------------------------------
    function GetFormationRankingsCount () {
        $formationRankData = Cache::read(FORMATION_RANKINGS_COUNT_CACHE, FORMATION_RANKINGS_COUNT_CACHE_DURATION);
        if ($formationRankData !== false)
            return $formationRankData;

        $formationRankingData = $this->query(sprintf(
            "SELECT
                COUNT(*) as `number`
            FROM
                `formations` AS `Formation`
            INNER JOIN `users` as `User` ON `User`.`id` = `Formation`.`user_id`
            WHERE
                `User`.`admin` = 0 AND
                `User`.`state` = %d",
            USER_STATE_NORMAL
        ));
        $formationRankingData = $formationRankingData[0][0]['number'];
        Cache::write(FORMATION_RANKINGS_COUNT_CACHE, $formationRankData, FORMATION_RANKINGS_COUNT_CACHE_DURATION);

        return $formationRankingData;
    }

    //---------------------------------------------------------------------------------------------
    function GetFormationByName ($formationName) {
        $formationName = mysql_escape_string($formationName);
        $result = $this->query(
            "SELECT
                `Formation`.`id`
            FROM
                `formations` AS `Formation`
            WHERE
                `Formation`.`name` = '{$formationName}'");

        if (empty($result))
            return false;

        return $this->GetFormation($result[0]['Formation']['id']);
    }

    //---------------------------------------------------------------------------------------------
    function GetTopFormationsByBounty () {
        $data = Cache::read(TOP_FORMATIONS_BY_BOUNTY_CACHE, TOP_FORMATIONS_BY_BOUNTY_CACHE_DURATION);
        if ($data !== false)
            return $data;

        $data = $this->query(sprintf(
            "SELECT
                `Formation`.`id`, `Formation`.`bounty`
            FROM
                `formations` AS `Formation`
            INNER JOIN `users` as `User` ON `User`.`id` = `Formation`.`user_id`
            WHERE
                `User`.`admin` = 0 AND
                `User`.`state` = %d
            ORDER BY `Formation`.`bounty` DESC
            LIMIT 5",
            USER_STATE_NORMAL
        ));
        $data = Set::extract($data, '{n}.Formation.id');
        Cache::write(TOP_FORMATIONS_BY_BOUNTY_CACHE, $data, TOP_FORMATIONS_BY_BOUNTY_CACHE_DURATION);

        return $data;
    }

    //---------------------------------------------------------------------------------------------
    function GetFormationRankings () {
        $formationRankData = Cache::read(FORMATION_RANKINGS_CACHE, FORMATION_RANKINGS_CACHE_DURATION);
        if ($formationRankData !== false)
            return $formationRankData;

        $formationRankingData = $this->query(sprintf(
            "SELECT
                `Formation`.`id`, `Formation`.`reputation`
            FROM
                `formations` AS `Formation`
            INNER JOIN `users` as `User` ON `User`.`id` = `Formation`.`user_id`
            WHERE
                `User`.`admin` = 0 AND
                `User`.`state` = %d
            ORDER BY `Formation`.`reputation` DESC",
            USER_STATE_NORMAL
        ));
        $rankByFormation = array();
        $formationRankings = array();
        $i = 1;
        foreach ($formationRankingData as $data) {
            $formationRankings[$i] = $data['Formation']['id'];
            $rankByFormation[$data['Formation']['id']] = $i;
            $i++;
        }

        $formationRankData = array('RankByFormation' => $rankByFormation, 'FormationRankings' => $formationRankings);
        Cache::write(FORMATION_RANKINGS_CACHE, $formationRankData, FORMATION_RANKINGS_CACHE_DURATION);

        return $formationRankData;
    }

    //---------------------------------------------------------------------------------------------
    function UpdateBoundScripts ($formationId, $characterIds, $scriptIds) {
        CheckNumeric($formationId);
        CheckNumeric($characterIds);
        CheckNumeric($scriptIds);

        $data = array_combine($characterIds, $scriptIds);

        foreach ($data as $characterId => $scriptId) {
            $value = $scriptId != -1 ? $scriptId : 'NULL';
            $this->query("
                UPDATE
                    `characters_formations`
                SET
                    `script_id` = {$value}
                WHERE
                    `formation_id` = {$formationId} AND
                    `character_id` = {$characterId}
                ");
        }
        $this->ClearFormationCache($formationId);
    }
}

?>
