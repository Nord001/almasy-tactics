<?

define('LAST_BATTLES_CACHE', 'battles_last');
define('BATTLE_HISTORY_CACHE', 'battle_history');

class Battle extends AppModel {
    var $belongsTo = array(
        'AttackingFormation' => array(
            'className' => 'Formation',
            'foreignKey' => 'attacker_formation_id',
        ),
        'DefendingFormation' => array(
            'className' => 'Formation',
            'foreignKey' => 'defender_formation_id',
        ),
    );

    var $knows = array('Formation', 'Message', 'User');

    //---------------------------------------------------------------------------------------------
    function GetLastBattles () {
        $battles = CacheRead(LAST_BATTLES_CACHE);
        if ($battles)
            return $battles;

        $battles = $this->find('all', array(
            'fields' => array(
                'Battle.id',
                'Battle.attacker_formation_id',
                'Battle.defender_formation_id',
                'Battle.time',
                'Battle.victor',
            ),
            'order' => 'time DESC',
            'limit' => '25',
            'contain' => array(
                'AttackingFormation' => array(
                    'fields' => array('*'),
                    'User',
                ),
                'DefendingFormation' => array(
                    'fields' => array('*'),
                    'User',
                ),
            )
        ));

        CacheWrite(LAST_BATTLES_CACHE, $battles);
        return $battles;
    }

    //---------------------------------------------------------------------------------------------
    function ClearLastBattlesCache () {
        CacheDelete(LAST_BATTLES_CACHE);
    }

    //---------------------------------------------------------------------------------------------
    function Matchmake2 ($formationId) {
        CheckNumeric($formationId);

        $formation = $this->Formation->GetFormation($formationId);
        $reputation = $formation['Formation']['reputation'];

        // Minimum rep for formations. Use a minimum so that formations with
        // reputations under 300 will have an easier time.
        $minRep = min($reputation * 0.875, $reputation - 30);

        // Max rep for formations. Leaving it looser to minimize effect on gameplay.
        // Mostly for performance.
        $maxRep = max($reputation * 1.3, $reputation + 60);

        // Find targets that aren't admins, aren't formations that also belong to the current user,
        // that don't belong to users that are banned, and who haven't been attacked by
        // the current user in the last hour. Sort targets by their reputation difference
        // from the current formation.
        $time = date(DB_FORMAT, strtotime('-10 minutes'));

        $query = sprintf(
            "SELECT
                `Formation`.`id`, `Formation`.`reputation`
            FROM
                `formations` AS `Formation`
            INNER JOIN
                `users` as `User` ON `User`.`id` = `Formation`.`user_id`
            WHERE
                `Formation`.`active` = 1 AND
                `Formation`.`user_id` <> {$formation['Formation']['user_id']} AND
                `User`.`admin` = 0 AND
                `User`.`state` = %d AND
                `Formation`.`reputation` > {$minRep} AND
                `Formation`.`reputation` < {$maxRep} AND
                (
                    SELECT
                        COUNT(*)
                    FROM
                        `battles`
                    WHERE
                        `attacker_user_id` = {$formation['Formation']['user_id']} AND
                        `defender_user_id` = `Formation`.`user_id` AND
                        `time` > '%s'
                ) = 0
            ORDER BY ABS({$reputation} - CAST(`Formation`.`reputation` AS SIGNED)) ASC
            LIMIT 8",
            USER_STATE_NORMAL,
            $time
        );
        $candidates = $this->Formation->query($query);

        // Look for candidates that are "deadly," which means they have higher bounty and more reputation.
        $deadlyCandidate = $this->Formation->query(sprintf(
            "SELECT
                `Formation`.`id`
            FROM
                `formations` AS `Formation`
            INNER JOIN
                `users` as `User` ON `User`.`id` = `Formation`.`user_id`
            WHERE
                `Formation`.`active` = 1 AND
                `Formation`.`user_id` <> {$formation['Formation']['user_id']} AND
                `User`.`admin` = 0 AND
                `User`.`state` = %d AND
                `Formation`.`bounty` > 10 AND
                `Formation`.`reputation` > {$reputation} AND
                (
                    SELECT
                        COUNT(*)
                    FROM
                        `battles`
                    WHERE
                        `attacker_user_id` = {$formation['Formation']['user_id']} AND
                        `defender_user_id` = `Formation`.`user_id` AND
                        `time` > '%s'
                ) = 0
            ORDER BY ABS({$reputation} - CAST(`Formation`.`reputation` AS SIGNED)) ASC
            LIMIT 3",
            USER_STATE_NORMAL,
            $time
        ));

        $noDeadly = count($deadlyCandidate) === 0;

        $selectedIds = array();

        // If we have deadly candidates, select one of them for the result.
        if (!$noDeadly)
            $selectedIds[] = $deadlyCandidate[mt_rand(0, count($deadlyCandidate) - 1)]['Formation']['id'];

        $numLower = 0;
        $numHigher = 0;

        // Add IDs until we have four targets. Add at most one target with lower
        // reps than me and at most three targets with higher reps.
        $i = 0;
        $y = 0;
        while (count($selectedIds) < 4 && $i < count($candidates)) {
            $id = $candidates[$i]['Formation']['id'];
            $targetReputation = $candidates[$i]['Formation']['id'];

            $i++;

            if (in_array($id, $selectedIds))
                continue;

            if ($reputation < $targetReputation && $numLower < 1) {
                $selectedIds[] = $id;
                $numLower++;
            } else if ($reputation > $targetReputation && $numHigher < 3) {
                $selectedIds[] = $id;
                $numHigher++;
            }
        }

        // If we somehow didn't manage to fill up the list, don't be picky anymore
        // and just add formations.
        foreach ($candidates as $candidate) {
            if (count($selectedIds) >= 4)
                break;
            if (!in_array($candidate['Formation']['id'], $selectedIds))
                $selectedIds[] = $candidate['Formation']['id'];
        }

        shuffle($selectedIds);

        return $selectedIds;
    }

    //---------------------------------------------------------------------------------------------
    function GetBattleHistory ($userId) {
        CheckNumeric($userId);

        $cacheKey = GenerateCacheKey(BATTLE_HISTORY_CACHE, $userId);
        $battleHistory = CacheRead($cacheKey);
        if ($battleHistory == false) {
            $battleHistory = $this->find('all', array(
                'fields' => array(
                    'Battle.id',
                    'Battle.attacker_formation_name',
                    'Battle.defender_formation_name',
                    'Battle.attacker_user_id',
                    'Battle.defender_user_id',
                    'Battle.time',
                    'Battle.victor',
                    'Battle.battle_type',
                ),
                'order' => 'time DESC',
                'limit' => '25',
                'conditions' => array(
                    'OR' => array(
                        'attacker_user_id' => $userId,
                        'defender_user_id' => $userId,
                    )
                )
            ));

            foreach ($battleHistory as &$history) {
                $attackingUser = $this->User->GetUser($history['Battle']['attacker_user_id']);
                $history['Battle']['AttackingUser'] = $attackingUser['User'];
                $defendingUser = $this->User->GetUser($history['Battle']['defender_user_id']);
                $history['Battle']['DefendingUser'] = $defendingUser['User'];
            }

            CacheWrite($cacheKey, $battleHistory);
        }

        return $battleHistory;
    }

    //---------------------------------------------------------------------------------------------
    function ClearBattleHistoryCacheByUserId ($userId) {
        CheckNumeric($userId);

        $cacheKey = GenerateCacheKey(BATTLE_HISTORY_CACHE, $userId);
        CacheDelete($cacheKey);
    }

    //---------------------------------------------------------------------------------------------
    function GetExp ($rep) {
        return intval(pow(6 * $rep, 0.8));
    }

    //---------------------------------------------------------------------------------------------
    function GetMoneyReward ($rep) {
        return intval(pow($rep / 150, 1.8) + $rep);
    }

    //---------------------------------------------------------------------------------------------
    function GetBountyBonus ($bounty) {
        return 6 * $bounty / (500 + $bounty);
    }

    //---------------------------------------------------------------------------------------------
    function ScaleDelta ($delta) {
        // Linear for [0, 0.4] but grows after that, in order to more quickly change
        // very one-sided battles towards equilibrium.
        if ($delta > 0)
            return $delta + 1.5 * $delta * $delta;
        return $delta;
    }

    //---------------------------------------------------------------------------------------------
    function GetNumBattlesToday () {
        $numBattles = $this->find('count', array(
            'conditions' => array(
                'time >' => date(DB_FORMAT, strtotime('-1 day')),
                ),
        ));

        return $numBattles;
    }

    //---------------------------------------------------------------------------------------------
    function CalculateBattleResults ($battleData, $attackingFormation, $defendingFormation) {
        // Fix simulator.

        $rA = $attackingFormation['Formation']['reputation'];
        $rB = $defendingFormation['Formation']['reputation'];

        $victor = $battleData['victor'];

        // Detect system gaming.
        // If attacker won and attacker rep is more than 10% below default attacker rep,
        // push attacker rep up to 90% of default attacker rep, cause we won't believe
        // they actually should be matched so weakly.
        $crankedUpRep = 0;
        if (isset($attackingFormation['Formation']['id'])) {
            $defaultRep = $this->Formation->GetDefaultReputation($attackingFormation['Formation']['id']);
            if ($victor == 'attacker' && $defaultRep > 3000) {
                $rA = max($rA, $defaultRep * 0.8);
                $crankedUpRep = 1;
            }
        }

        $qA = $rA * $rA * $rA;
        $qB = $rB * $rB * $rB;

        $eA = $qA / ($qA + $qB);
        $eB = 1 - $eA;

        $kA = max($rA * 0.1, 32);
        $kB = max($rB * 0.1, 32);

        $attackerHpPercent = $battleData['attacker_hp_percent'];
        $defenderHpPercent = $battleData['defender_hp_percent'];

        $attackerDelta = 0;  // Used later for exp calculations.
        if ($victor == 'attacker') {
            // Scale [0 - 100] to [.66 - 1].
            // The more hp the attacker has the end, the bigger the victory.
            $attackerScore = Clamp($attackerHpPercent / 200 + 0.5, 0, 1);
            $defenderScore = 1 - $attackerScore;

            // (score - expected) represents the difference between how they performed and what we thought of them.
            $attackerDelta = $this->ScaleDelta($attackerScore - $eA);
            $change = $kA * $attackerDelta;
            $rA += $change;

            // Scale defender too.
            $change = $kB * $this->ScaleDelta($defenderScore - $eB);
            $rB += $change;
        } else if ($victor == 'defender') {
            $defenderScore = Clamp($defenderHpPercent / 200 + 0.5, 0, 1);
            $attackerScore = 1 - $defenderScore;

            $change = $kA * $this->ScaleDelta($attackerScore - $eA);
            $rA += $change;

            $change = $kB * $this->ScaleDelta($defenderScore - $eB);
            $rB += $change;
        }

        $rA = Clamp($rA, MIN_REPUTATION, MAX_REPUTATION);
        $rB = Clamp($rB, MIN_REPUTATION, MAX_REPUTATION);

        $rA = intval($rA);
        $rB = intval($rB);

        $bA = $attackingFormation['Formation']['bounty'];
        $bB = $defendingFormation['Formation']['bounty'];

        $bABonus = $this->GetBountyBonus($bA);
        $bBBonus = $this->GetBountyBonus($bB);
        $attackerBonus = 1;
        $defenderBonus = 1;
        $attackerWinBonus = 0;
        if ($victor == 'attacker') {
            $attackerBonus = 1 + $bABonus + $bBBonus;

            // The more surprising the battle was to us, the less exp we give, to reward fair battling.
            $attackerWinBonus = Clamp(1.1 - 1.25 * $attackerDelta * $attackerDelta, 0.25, 1);
            $attackerBonus *= $attackerWinBonus;
        } else if ($victor == 'defender') {
            $defenderBonus = 1 + $bABonus + $bBBonus;
        }

        if ($victor == 'attacker') {
            $bA += ceil($bB / 2) + BOUNTY_ADD_RATE;
            $bB = 0;
        } else if ($victor == 'defender') {
            $bB += ceil($bA / 2) + BOUNTY_ADD_RATE;
            $bA = 0;
        }

        $PRORATION_MATRIX = BATTLE_PRORATION_MATRIX();

        // Give exp to characters

        // Attacker
        // Ties are considered as both players lose.
        $expRate = ($victor == 'attacker') ? $PRORATION_MATRIX[0][0]: $PRORATION_MATRIX[1][0];
        $exp = intval($this->GetExp($defendingFormation['Formation']['reputation']) * BATTLE_EXP_RATE * $expRate * $attackerBonus);
        $exp = max($exp, MIN_EXP_GAIN);
        $attackerExp = $exp;

        // Defender
        $expRate = ($victor == 'defender') ? $PRORATION_MATRIX[0][1]: $PRORATION_MATRIX[1][1];
        $exp = intval($this->GetExp($attackingFormation['Formation']['reputation']) * BATTLE_EXP_RATE * $expRate * $defenderBonus);
        $exp = max($exp, MIN_EXP_GAIN);
        $defenderExp = $exp;

        // Give money to characters

        // Attacker
        $ybRate = ($victor == 'attacker') ? $PRORATION_MATRIX[0][0]: $PRORATION_MATRIX[1][0];
        $yb = intval($this->GetMoneyReward($defendingFormation['Formation']['reputation']) * BATTLE_YB_RATE * $ybRate * $attackerBonus);
        $attackerYb = $yb;

        // Defender
        $ybRate = ($victor == 'defender') ? $PRORATION_MATRIX[0][1]: $PRORATION_MATRIX[1][1];
        $yb = intval($this->GetMoneyReward($attackingFormation['Formation']['reputation']) * BATTLE_YB_RATE * $ybRate * $defenderBonus);
        $defenderYb = $yb;

        $data = array(
            'attackerRep' => $rA,
            'attackerRepD' => $rA - $attackingFormation['Formation']['reputation'],
            'defenderRep' => $rB,
            'defenderRepD' => $rB - $defendingFormation['Formation']['reputation'],
            'attackerHpPercent' => $attackerHpPercent,
            'defenderHpPercent' => $defenderHpPercent,
            'attackerYbReward' => $attackerYb,
            'defenderYbReward' => $defenderYb,
            'attackerExpReward' => $attackerExp,
            'defenderExpReward' => $defenderExp,
            'attackerBounty' => $bA,
            'attackerBountyD' => $bA - $attackingFormation['Formation']['bounty'],
            'defenderBounty' => $bB,
            'defenderBountyD' => $bB - $defendingFormation['Formation']['bounty'],
            'attackerWinBonus' => $attackerWinBonus,
            'crankedUpRep' => $crankedUpRep,
        );

        $log = '';
        foreach ($data as $key => $value)
            $log .= sprintf('%s: %s ', $key, $value);
        $this->log($log, 'matchmaking');

        return $data;
    }

    //---------------------------------------------------------------------------------------------
    function ProcessBattle ($battleId, $attackingFormationId, $defendingFormationId) {
        $attackingFormation = $this->Formation->GetFormation($attackingFormationId);
        $defendingFormation = $this->Formation->GetFormation($defendingFormationId);

        $this->id = $battleId;
        $victor = $this->field('victor');
        $battleData = array(
            'victor' => $victor,
            'attacker_hp_percent' => $this->field('attacker_hp_percent'),
            'defender_hp_percent' => $this->field('defender_hp_percent'),
        );
        $results = $this->CalculateBattleResults($battleData, $attackingFormation, $defendingFormation);

        // Assign reputation and bounty and increment battles.

        $this->Formation->id = $attackingFormationId;
        $this->Formation->fastSave('reputation', $results['attackerRep']);
        $this->Formation->fastSave('bounty', $results['attackerBounty']);
        if ($victor == 'attacker')
            $this->Formation->fastSave('battles_won', $attackingFormation['Formation']['battles_won'] + 1);
        else
            $this->Formation->fastSave('battles_lost', $attackingFormation['Formation']['battles_lost'] + 1);

        $this->Formation->id = $defendingFormationId;
        $this->Formation->fastSave('reputation', $results['defenderRep']);
        $this->Formation->fastSave('bounty', $results['defenderBounty']);
        if ($victor == 'defender')
            $this->Formation->fastSave('battles_won', $defendingFormation['Formation']['battles_won'] + 1);
        else
            $this->Formation->fastSave('battles_lost', $defendingFormation['Formation']['battles_lost'] + 1);

        $this->Formation->ClearFormationExCache($attackingFormationId);
        $this->Formation->ClearFormationExCache($defendingFormationId);

        // Increment battles for respective users.
        $this->Formation->User->GiveVictory($victor == 'attacker' ? $attackingFormation['Formation']['user_id'] : $defendingFormation['Formation']['user_id']);
        $this->Formation->User->GiveLoss($victor != 'attacker' ? $attackingFormation['Formation']['user_id'] : $defendingFormation['Formation']['user_id']);

        // Increment battles for respective characters in formation.
        $this->Formation->GiveVictoryToCharacters($victor == 'attacker' ? $attackingFormation['Formation']['id'] : $defendingFormation['Formation']['id']);
        $this->Formation->GiveLossToCharacters($victor != 'attacker' ? $attackingFormation['Formation']['id'] : $defendingFormation['Formation']['id']);

        // Give exp to characters.
        $attackingResult = $this->Formation->AwardBattleExp($attackingFormationId, $results['attackerExpReward']);
        $defendingResult = $this->Formation->AwardBattleExp($defendingFormationId, $results['defenderExpReward']);

        // Give money.
        $this->Formation->User->GiveMoney($attackingFormation['Formation']['user_id'], $results['attackerYbReward']);
        $attackingResult .= sprintf("Received %s yb!\n", $results['attackerYbReward']);

        $this->Formation->User->GiveMoney($defendingFormation['Formation']['user_id'], $results['defenderYbReward']);
        $defendingResult .= sprintf("Received %s yb!\n", $results['defenderYbReward']);

        $attackingResult .= sprintf("%s %s reputation!\n", $results['attackerRepD'] > 0 ? 'Gained' : 'Lost', abs($results['attackerRepD']));
        $defendingResult .= sprintf("%s %s reputation!\n", $results['defenderRepD'] > 0 ? 'Gained' : 'Lost', abs($results['defenderRepD']));

        $attackingResult .= sprintf("%s %s bounty!\n", $results['attackerBountyD'] > 0 ? 'Gained' : 'Lost', abs($results['attackerBountyD']));
        $defendingResult .= sprintf("%s %s bounty!\n", $results['defenderBountyD'] > 0 ? 'Gained' : 'Lost', abs($results['defenderBountyD']));

        $this->id = $battleId;
        $this->fastSave('attacker_result', $attackingResult);
        $this->fastSave('defender_result', $defendingResult);

        $attackingUser = $this->Formation->User->GetUser($attackingFormation['Formation']['user_id']);

        // Notify defender
        $link = sprintf('http://%s/battles/fight_result/%s', $_SERVER['SERVER_NAME'], $battleId);
        $message = sprintf(
            "%s (%s) has attacked %s. %s\n\n" .
            "%s %s %s reputation and now has %s reputation.\n\n" .
            "To see the result of the battle, follow this link:\n\n" .
            "<a href = '%s'>%s</a>\n\n" .
            "Thanks,\n" .
            "The Almasy Team",
            $attackingFormation['Formation']['name'],
            $attackingUser['User']['username'],
            $defendingFormation['Formation']['name'],
            ($victor == 'defender') ? 'Luckily, you were victorious!' : 'Unfortunately, you were defeated.',
            $defendingFormation['Formation']['name'],
            ($results['defenderRepD'] >= 0) ? 'gained' : 'lost',
            abs(intval($results['defenderRepD'])),
            intval($results['defenderRep']),
            $link,
            $link
        );
        $this->Message->SendNotification($defendingFormation['Formation']['user_id'], 'You were attacked!', $message);
    }


    //---------------------------------------------------------------------------------------------
    function Fight ($attackerId, $defenderId, $type) {
        if (!CheckNumeric($attackerId))
            return -1;
        if (!CheckNumeric($defenderId))
            return -1;

        // Trying to fix bug
        if ($defenderId == 0) {
            IERR('Defender id was 0.');
            return -1;
        }

        $data = false;
        try {
            $client = Network::GetInstance()->GetGameClient();
            $data = $client->getBattle($attackerId, $defenderId);
        } catch (TException $e) {
            IERR('Error getting battle: ' . $e->getMessage());
        }

        if ($data) {
            $data = explode("\n", $data);
            $battleData = array_shift($data);

            $webLog = implode("\n", $data);

            $battleData = json_decode($battleData, true);
            if ($battleData == NULL) {
                IERR('Failed to decode battle JSON.');
                return -1;
            }

            $this->save(array(
                'attacker_formation_id' => $battleData['attacker_formation_id'],
                'defender_formation_id' => $battleData['defender_formation_id'],
                'victor' => $battleData['victor'],
                'web_log' => gzcompress($webLog),
                'attacker_formation_name' => $battleData['attacker_formation_name'],
                'attacker_user_id' => $battleData['attacker_user_id'],
                'defender_formation_name' => $battleData['defender_formation_name'],
                'defender_user_id' => $battleData['defender_user_id'],
                'attacker_hp_percent' => $battleData['attacker_hp_percent'],
                'defender_hp_percent' => $battleData['defender_hp_percent'],
                'time' => date(DB_FORMAT),
                'battle_type' => $type
            ));

            $battleId = $this->id;

            $attackingFormation = $this->Formation->GetFormation($battleData['attacker_formation_id']);
            $defendingFormation = $this->Formation->GetFormation($battleData['defender_formation_id']);

            $victor = $battleData['victor'];

            $this->ClearBattleHistoryCacheByUserId($attackingFormation['Formation']['user_id']);
            $this->ClearBattleHistoryCacheByUserId($defendingFormation['Formation']['user_id']);

            if ($type == 'spar') {
                $attackingUser = $this->Formation->User->GetUser($attackingFormation['Formation']['user_id']);

                if ($attackingFormation['Formation']['user_id'] != $defendingFormation['Formation']['user_id']) {
                    // Notify defender
                    $link = sprintf('http://%s/battles/fight_result/%s', $_SERVER['SERVER_NAME'], $battleId);
                    $message = sprintf(
                        "%s (%s) sparred with %s. %s\n\n" .
                        "To see the result of the battle, follow this link:\n\n" .
                        "<a href = '%s'>%s</a>\n\n" .
                        "Thanks,\n" .
                        "The Almasy Team",
                        $attackingFormation['Formation']['name'],
                        $attackingUser['User']['username'],
                        $defendingFormation['Formation']['name'],
                        ($victor == 'defender') ? 'Yon won!' : 'You lost.',
                        $link,
                        $link
                    );
                    $this->Message->SendNotification($defendingFormation['Formation']['user_id'], 'Sparring Match!', $message);
                }
            } elseif ($type == 'battle') {
                $this->ProcessBattle($battleId, $attackerId, $defenderId);
            }

            return $battleId;
        }

        IERR('Could not get data for battle.');

        return -1;
    }
};

?>
