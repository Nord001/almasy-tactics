<?

// Caches refine chances
define('REFINE_CACHE', 'refine');
define('REFINE_CACHE_DURATION', 'long');

class RefineTable extends AppModel {

    var $useTable = 'refine_table';

    var $knows = array('UserItem');

    //---------------------------------------------------------------------------------------------
    function GetRefineChanceForLevel ($level) {
        CheckNumeric($level);

        $chances = $this->GetRefineChances();

        // ID 1 has the chance at level 0, so add 1 to level
        return isset($chances[$level + 1]) ? $chances[$level + 1] : -1;
    }

    //---------------------------------------------------------------------------------------------
    function GetRefineTable () {
        $data = Cache::read(REFINE_CACHE, REFINE_CACHE_DURATION);
        if ($data)
            return $data;

        $data = $this->find('all');

        Cache::write(REFINE_CACHE, $data, REFINE_CACHE_DURATION);
        return $data;
    }

    //---------------------------------------------------------------------------------------------
    function GetRefineChances () {
        $data = $this->GetRefineTable();
        $chances = Set::combine($data, '{n}.RefineTable.id', '{n}.RefineTable.chance');
        return $chances;
    }

    //---------------------------------------------------------------------------------------------
    function GetRefineBonusForLevel ($level, $type) {
        CheckNumeric($level);

        if ($level <= 0 || $level > MAX_REFINE)
            return false;

        if ($type != 'weapon' && $type != 'armor')
            return false;

        $data = $this->GetRefineTable();
        return $data[$level - 1]['RefineTable'][$type . '_bonus'];
    }

    //---------------------------------------------------------------------------------------------
    function GetRefinePrefix ($level, $type) {
        CheckNumeric($level);

        if ($level <= 0 || $level > MAX_REFINE)
            return false;

        if ($type != 'weapon' && $type != 'armor')
            return false;

        $data = $this->GetRefineTable();
        return $data[$level - 1]['RefineTable'][$type . '_prefix'];
    }

    //---------------------------------------------------------------------------------------------
    function GetRefineCost ($userItemId) {
        CheckNumeric($userItemId);

        $userItem = $this->UserItem->GetUserItem($userItemId);
        if ($userItem === false)
            return false;

        $level = $userItem['UserItem']['refine'];

        if ($level <= 0 || $level > MAX_REFINE)
            return 0;

        $level++;
        return intval(($level / 4) * $userItem['UserItem']['Item']['value']);
    }
};

?>