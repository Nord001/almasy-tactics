<?

define('USER_ITEM_MODS_CACHE', 'user_item_mods');
define('USER_ITEM_MODS_CACHE_DURATION', 'long');

class ItemMod extends AppModel {

    var $belongsTo = array(
        'UserItem',
        'BonusType',
    );

    //---------------------------------------------------------------------------------------------
    function ClearItemModsCache ($userItemId) {
        CheckNumeric($userItemId);

        $cacheKey = GenerateCacheKey(USER_ITEM_MODS_CACHE, $userItemId);
        Cache::delete($cacheKey, USER_ITEM_MODS_CACHE_DURATION);
    }

    //---------------------------------------------------------------------------------------------
    function GetItemMods ($userItemId) {
        CheckNumeric($userItemId);

        $cacheKey = GenerateCacheKey(USER_ITEM_MODS_CACHE, $userItemId);
        $itemMods = Cache::read($cacheKey, USER_ITEM_MODS_CACHE_DURATION);

        if ($itemMods === false) {
            $itemModData = $this->find('all', array(
                'conditions' => array(
                    'ItemMod.user_item_id' => $userItemId,
                ),
                'contain' => array(
                    'BonusType' => array(
                        'fields' => array(
                            'BonusType.name',
                        ),
                    ),
                ),
            ));

            if ($itemModData === false)
                return false;


            $itemMods = array('ItemMod' => array());
            foreach ($itemModData as $itemMod) {
                $mod = $itemMod['ItemMod'];
                $mod['BonusType'] = $itemMod['BonusType'];
                $itemMods['ItemMod'][] = $mod;
            }

            Cache::write($cacheKey, $itemMods, USER_ITEM_MODS_CACHE_DURATION);
        }

        return $itemMods;
    }
}

?>