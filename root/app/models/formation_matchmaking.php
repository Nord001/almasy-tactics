<?

class FormationMatchmaking extends AppModel {

    var $useTable = 'formation_matchmaking';

    //---------------------------------------------------------------------------------------------
    function SaveMatchmaking ($formationId, $targetIds) {
        CheckNumeric($formationId);
        CheckNumeric($targetIds);

        $ids = implode(',', $targetIds);
        $time = date(DB_FORMAT);

        $this->query(
            "INSERT INTO
                `formation_matchmaking`
            (
                `formation_id`,
                `target_ids`,
                `time`
            )
            VALUES
            (
                {$formationId},
                '{$ids}',
                '{$time}'
            )
            ON DUPLICATE KEY
            UPDATE
                `target_ids` = '{$ids}',
                `time` = '{$time}'"
        );
    }

    //---------------------------------------------------------------------------------------------
    function ClearMatchmaking ($formationId) {
        CheckNumeric($formationId);

        $this->query(
            "UPDATE
                `formation_matchmaking`
            SET
                `target_ids` = ''
            WHERE
                `formation_id` = {$formationId}"
        );
    }

    //---------------------------------------------------------------------------------------------
    function GetMatchmaking ($formationId) {
        CheckNumeric($formationId);

        $data = $this->query(
            "SELECT
                `target_ids`, `time`
            FROM
                `formation_matchmaking`
            WHERE
                `formation_id` = {$formationId}"
        );
        if (empty($data))
            return false;

        $targetIds = $data[0]['formation_matchmaking']['target_ids'];
        if ($targetIds === '')
            return false;

        $time = $data[0]['formation_matchmaking']['time'];
        if (strtotime($time) < strtotime('-15 minutes'))
            return false;

        return explode(',', $targetIds);
    }

};

?>