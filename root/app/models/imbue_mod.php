<?

define('IMBUE_MOD_CACHE', 'imbue_mods');
define('IMBUE_MOD_CACHE_DURATION', 'long');

// Caches the list of random mods for weapons
define('IMBUE_MOD_RANDOM_WEAPON_LIST_CACHE', 'imbue_mods_weapon');
define('IMBUE_MOD_RANDOM_WEAPON_LIST_CACHE_DURATION', 'long');

define('IMBUE_MOD_RANDOM_ARMOR_LIST_CACHE', 'imbue_mods_armor');
define('IMBUE_MOD_RANDOM_ARMOR_LIST_CACHE_DURATION', 'long');

class ImbueMod extends AppModel {
    var $belongsTo = array(
        'Imbue',
        'BonusType'
    );

    var $validate = array(
        'min_duration' => array(
            'rule' => 'numeric',
            'allowEmpty' => false,
            'message' => 'This field is required.',
        ),
        'max_duration' => array(
            'rule' => 'numeric',
            'allowEmpty' => false,
            'message' => 'This field is required.',
        ),
    );

    //---------------------------------------------------------------------------------------------
    function GetWeaponImbueModsRandomList () {
        $cacheKey = GenerateCacheKey(IMBUE_MOD_RANDOM_WEAPON_LIST_CACHE);

        $imbueMods = Cache::read($cacheKey, IMBUE_MOD_RANDOM_WEAPON_LIST_CACHE_DURATION);
        if ($imbueMods)
            return $imbueMods;

        $imbueMods = $this->find('all', array(
            'conditions' => array(
                'ImbueMod.imbue_id IS NULL',
                'ImbueMod.item_type' => 'weapon',
            ),
        ));

        Cache::write($cacheKey, $imbueMods, IMBUE_MOD_RANDOM_WEAPON_LIST_CACHE_DURATION);

        return $imbueMods;
    }

    //---------------------------------------------------------------------------------------------
    function GetArmorImbueModsRandomList () {
        $cacheKey = GenerateCacheKey(IMBUE_MOD_RANDOM_ARMOR_LIST_CACHE);

        $imbueMods = Cache::read($cacheKey, IMBUE_MOD_RANDOM_ARMOR_LIST_CACHE_DURATION);
        if ($imbueMods)
            return $imbueMods;

        $imbueMods = $this->find('all', array(
            'conditions' => array(
                'ImbueMod.imbue_id IS NULL',
                'ImbueMod.item_type' => 'armor',
            ),
        ));

        Cache::write($cacheKey, $imbueMods, IMBUE_MOD_RANDOM_ARMOR_LIST_CACHE_DURATION);

        return $imbueMods;
    }

    //---------------------------------------------------------------------------------------------
    function GetImbueModsByImbueId ($imbueId) {
        CheckNumeric($imbueId);

        $cacheKey = GenerateCacheKey(IMBUE_MOD_CACHE, $imbueId);

        $imbueMods = Cache::read($cacheKey, IMBUE_MOD_CACHE_DURATION);
        if ($imbueMods)
            return $imbueMods;

        $imbueMods = $this->find('all', array(
            'conditions' => array(
                'ImbueMod.imbue_id' => $imbueId,
            ),
        ));

        Cache::write($cacheKey, $imbueMods, IMBUE_MOD_CACHE_DURATION);

        return $imbueMods;
    }
}

?>