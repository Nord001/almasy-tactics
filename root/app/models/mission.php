<?

define('MISSION_CACHE', 'mission');
define('MISSIONS_CACHE', 'missions_all');
define('MISSION_LIST_CACHE', 'mission_list');

class Mission extends AppModel {

    var $belongsTo = array(
        'MissionGroup',
    );

    var $hasMany = array(
        'MissionReward',
        'MissionHistory'
    );

    var $knows = array('Formation', 'UserItem');

    //--------------------------------------------------------------------------------------------
    function GetMission ($missionId) {
        CheckNumeric($missionId);

        $cacheKey = GenerateCacheKey(MISSION_CACHE, $missionId);
        $mission = CacheRead($cacheKey);
        if ($mission === false) {
            $mission = $this->find('first', array(
                'conditions' => array(
                    'Mission.id' => $missionId,
                ),
                'contain' => array(
                    'MissionGroup',
                    'MissionReward',
                )
            ));
            if ($mission === false)
                return false;

            $prereqs = &$mission['Mission']['prereqs'];
            $prereqs = str_replace(' ', '', $prereqs);
            if (strlen($prereqs) == 0)
                $prereqs = array();
            else
                $prereqs = explode(',', $prereqs);

            CacheWrite($cacheKey, $mission);
        }

        return $mission;
    }

    //--------------------------------------------------------------------------------------------
    function ClearMissionCache ($missionId) {
        CheckNumeric($missionId);

        $cacheKey = GenerateCacheKey(MISSION_CACHE, $missionId);
        CacheDelete($cacheKey);
    }

    //--------------------------------------------------------------------------------------------
    function GetMissions ($ids) {
        $missions = array();
        foreach ($ids as $id)
            $missions[] = $this->GetMission($id);
        return $missions;
    }

    //--------------------------------------------------------------------------------------------
    function GetAllMissionIds () {
        $missions = CacheRead(MISSIONS_CACHE);
        if ($missions === false) {
            $missions = $this->find('all', array(
                'fields' => array(
                    'Mission.id',
                ),
                'conditions' => array(
                    'Mission.active' => 1,
                ),
            ));
            $missions = Set::classicExtract($missions, '{n}.Mission.id');

            CacheWrite(MISSIONS_CACHE, $missions);
        }

        return $missions;
    }

    //--------------------------------------------------------------------------------------------
    function GetMissionList ($formationId) {
        CheckNumeric($formationId);

        $cacheKey = GenerateCacheKey(MISSION_LIST_CACHE, $formationId);
        $list = CacheRead($cacheKey);

        if ($list === false) {

            $list = array();

            $formation = $this->Formation->GetFormation($formationId);
            if ($formation === false)
                return false;

            $levels = Set::classicExtract($formation, 'Characters.{n}.Character.level');
            $minLevel = count($levels) > 0 ? min($levels) : 0;
            $maxLevel = count($levels) > 0 ? max($levels) : 0;

            $unresolvedMissionIds = $this->MissionHistory->GetUnresolvedMissionIds($formationId);
            $unresolvedMissionIdsTable = array_fill_keys($unresolvedMissionIds, 1);

            $openMissions = array();  // Eligible (passes restriction) and prereq-fulfilled missions.
            $restrictedMissions = array();  // Restricted but prereq-fulfilled missions.

            $missions = $this->GetMissions($this->GetAllMissionIds());
            foreach ($missions as $mission) {
                $eligible = ObeysExpr(
                    $mission['Mission']['restrictions'],
                    array(
                        'minCharLevel' => $minLevel,
                        'maxCharLevel' => $maxLevel,
                    )
                );
                $prereqsFulfilled = true;
                foreach ($mission['Mission']['prereqs'] as $prereqMissionId) {
                    if (!isset($unresolvedMissionIdsTable[$prereqMissionId])) {
                        $prereqsFulfilled = false;
                        break;
                    }
                }

                if ($mission['Mission']['only_once_per_user']) {
                    $alreadyDone = $this->query("
                        SELECT
                            COUNT(*)
                        FROM
                            `mission_histories`
                        INNER JOIN formations ON formations.id = formation_id
                        WHERE
                            user_id = {$formation['Formation']['user_id']} AND
                            mission_id = {$mission['Mission']['id']}"
                    );
                    if ($alreadyDone[0][0] > 0)
                        $prereqsFulfilled = false;
                }

                // Can't do a mission if you already did it and it's unresolved.
                if (isset($unresolvedMissionIdsTable[$mission['Mission']['id']]))
                    $prereqsFulfilled = false;

                if ($prereqsFulfilled) {
                    if ($eligible) {
                        $openMissions[] = $mission['Mission']['id'];
                    } else {
                        $restrictedMissions[] = $mission['Mission']['id'];
                    }
                }
            }

            $list['open_missions'] = $openMissions;
            $list['restricted_missions'] = $restrictedMissions;

            CacheWrite($cacheKey, $list);
        }

        return $list;
    }

    //--------------------------------------------------------------------------------------------
    function CanDoMission ($formationId, $missionId) {
        CheckNumeric($formationId);
        CheckNumeric($missionId);

        $missionList = $this->GetMissionList($formationId);
        return $missionList !== false ? in_array($missionId, $missionList['open_missions']) : false;
    }

    //--------------------------------------------------------------------------------------------
    function ClearMissionList ($formationId) {
        CheckNumeric($formationId);

        $cacheKey = GenerateCacheKey(MISSION_LIST_CACHE, $formationId);
        CacheDelete($cacheKey);
    }

    //--------------------------------------------------------------------------------------------
    function DoMission ($formationId, $missionId) {
        CheckNumeric($formationId);
        CheckNumeric($missionId);

        $formation = G($this->Formation->GetFormation($formationId));
        $mission = G($this->GetMission($missionId));

        if (!$this->CanDoMission($formationId, $missionId)) {
            UERR('You can\'t do that mission!');
            return false;
        }

        // TODO: Pretend like you always win for now.

        // Give rewards.
        foreach ($mission['MissionReward'] as $reward) {
            $rand = mt_rand() / mt_getrandmax();
            $randSuccess = $rand < $reward['chance'];
            if (!$randSuccess)
                continue;

            // TODO: Factor in mission difficulty.

            $success = false;
            if ($reward['type'] == 'exp') {
                $success = $this->Formation->AwardBattleExp($formationId, $reward['value']);
            } else if ($reward['type'] == 'money') {
                $success = $this->Formation->User->GiveMoney($formation['Formation']['user_id'], $reward['value']);
            } else if ($reward['type'] == 'item') {
                $success = $this->UserItem->GiveUserItemToUser($reward['value'], $formation['Formation']['user_id']);
            } else if ($reward['type'] == 'character') {
                $success = $this->Formation->Character->GiveCharacterToUser($reward['value'], $formation['Formation']['user_id']);
            }
            if ($success === false)
                IERR('Failed to give reward.', $reward);
        }

        // Mark mission completed.
        $success = $this->MissionHistory->save(array(
            'formation_id' => $formationId,
            'mission_id' => $missionId,
            'resolved' => 0,
            'time' => date(DB_FORMAT)
        ));
        if ($success === false) {
            IERR('Failed to save mission history.');
            return false;
        }

        // If final, resolve all missions in this group.
        if ($mission['Mission']['final_mission']) {
            $success = $this->query(
                "UPDATE
                    `mission_histories`
                INNER JOIN
                    `missions` ON `mission_id` = `missions`.`id`
                SET
                    `resolved` = 1
                WHERE
                    `formation_id` = {$formationId} AND
                    `missions`.`mission_group_id` = {$mission['MissionGroup']['id']} AND
                    `resolved` = 0");
            if ($success === false) {
                IERR('Failed to resolve missions.');
                return false;
            }
        }

        $this->ClearMissionList($formationId);
        $this->MissionHistory->ClearMissionHistoryCache($formationId);

        // Update completion count.
        $success = $this->query(
            "UPDATE
                `missions`
            SET
                `completion_count` = `completion_count` + 1
            WHERE
                `id` = {$mission['Mission']['id']}
        ");
        if ($success === false) {
            IERR('Could not increment completion count.');
            return false;
        }
        $this->ClearMissionCache($mission['Mission']['id']);

        return true;
    }
};

?>