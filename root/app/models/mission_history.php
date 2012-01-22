<?

define('UNRESOLVED_MISSION_HISTORY_CACHE', 'unresolved_mission_history');
define('MISSION_HISTORY_CACHE', 'mission_history');

class MissionHistory extends AppModel {

    //--------------------------------------------------------------------------------------------
    function GetUnresolvedMissionIds ($formationId) {
        CheckNumeric($formationId);

        $cacheKey = GenerateCacheKey(UNRESOLVED_MISSION_HISTORY_CACHE, $formationId);
        $history = CacheRead($cacheKey);
        if ($history === false) {
            $history = $this->find('all', array(
                'fields' => array(
                    'MissionHistory.mission_id',
                ),
                'conditions' => array(
                    'MissionHistory.formation_id' => $formationId,
                    'MissionHistory.resolved' => 0
                )
            ));
            $history = Set::classicExtract($history, '{n}.MissionHistory.mission_id');

            CacheWrite(UNRESOLVED_MISSION_HISTORY_CACHE, $history);
        }

        return $history;
    }

    //--------------------------------------------------------------------------------------------
    function GetMissionHistory ($formationId) {
        CheckNumeric($formationId);

        $history = CacheRead(MISSION_HISTORY_CACHE);
        if ($history === false) {
            $history = $this->find('all', array(
                'fields' => array(
                    'MissionHistory.mission_id',
                ),
                'conditions' => array(
                    'MissionHistory.formation_id' => $formationId,
                ),
                'limit' => 25
            ));
            $history = Set::classicExtract($history, '{n}.MissionHistory.mission_id');

            CacheWrite(MISSION_HISTORY_CACHE, $history);
        }

        return $history;
    }

    //--------------------------------------------------------------------------------------------
    function ClearMissionHistoryCache ($formationId) {
        CheckNumeric($formationId);

        $cacheKey = GenerateCacheKey(UNRESOLVED_MISSION_HISTORY_CACHE, $formationId);
        CacheDelete($cacheKey);
        $cacheKey = GenerateCacheKey(MISSION_HISTORY_CACHE, $formationId);
        CacheDelete($cacheKey);
    }
}

?>