<?

define('MONSTER_CACHE', 'monster');
define('MONSTER_CACHE_DURATION', 'long');

class Monster extends AppModel {
    var $displayField = 'name';
    var $useTable = 'classes';

    var $hasMany = array(
        'Bonus',
        'Character',
    );

    var $knows = array('CClass');

    //--------------------------------------------------------------------------------------------
    function GetMonsterByName ($name) {
        $monster = $this->find('first', array(
            'fields' => array(
                'Monster.id',
            ),
            'conditions' => array(
                'Monster.name' => $name,
                'Monster.monster' => 1,
            ),
        ));

        if ($monster === false)
            return false;

        return $this->GetMonster($monster['Monster']['id']);
    }

    //--------------------------------------------------------------------------------------------
    function GetMonster ($monsterId) {
        CheckNumeric($monsterId);

        $cacheKey = GenerateCacheKey(MONSTER_CACHE, $monsterId);
        $monster = Cache::read($cacheKey, MONSTER_CACHE_DURATION);
        if ($monster === false) {
            $monster = $this->find('first', array(
                'conditions' => array(
                    'Monster.id' => $monsterId,
                    'Monster.monster' => 1,
                ),
                'contain' => array(
                    'Bonus' => array(
                        'BonusType',
                    ),
                ),
            ));

            if ($monster === false)
                return false;

            $monster['Monster']['Bonus'] = $monster['Bonus'];
            unset($monster['Bonus']);

            unset($monster['WeaponType']);

            $monster['Monster']['bonus_locations'] = $this->CClass->GetBonusLocations($monster['Monster']['Bonus']);

            Cache::write($cacheKey, $monster, MONSTER_CACHE_DURATION);
        }

        return $monster;
    }
}

?>
