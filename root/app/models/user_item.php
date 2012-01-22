<?

// Caches individual user items
define('USER_ITEM_CACHE', 'user_item');
define('USER_ITEM_CACHE_DURATION', 'long');

// Caches user items by user
define('USER_ITEMS_BY_USER_CACHE', 'user_items');
define('USER_ITEMS_BY_USER_CACHE_DURATION', 'long');

// Represents an instance of an item. The instance belongs to a user, and can have unique mods and traits.
class UserItem extends AppModel {

    var $belongsTo = array(
        'Item',
        'User',
    );

    var $hasMany = array(
        'ItemMod',
    );

    var $hasOne = array(
        'ItemCatalogEntry',
        'CharacterEquippedAsWeapon' => array(
            'className' => 'Character',
            'foreignKey' => 'weapon_id',
        ),
        'CharacterEquippedAsArmor' => array(
            'className' => 'Character',
            'foreignKey' => 'armor_id',
        ),
    );

    var $knows = array('RefineTable');

    //---------------------------------------------------------------------------------------------
    // Clears the cache of an item.
    function ClearUserItemCache ($userItemId) {
        CheckNumeric($userItemId);

        $cacheKey = GenerateCacheKey(USER_ITEM_CACHE, $userItemId);
        Cache::delete($cacheKey, USER_ITEM_CACHE_DURATION);
    }

    //---------------------------------------------------------------------------------------------
    // Clears the cache of all items that a user owns.
    function ClearUserItemCacheByUser ($userId) {
        CheckNumeric($userId);

        $cacheKey = GenerateCacheKey(USER_ITEMS_BY_USER_CACHE, $userId);
        Cache::delete($cacheKey, USER_ITEMS_BY_USER_CACHE_DURATION);
    }

    //---------------------------------------------------------------------------------------------
    function GetUserItemIdsByUser ($userId) {
        CheckNumeric($userId);

        $cacheKey = GenerateCacheKey(USER_ITEMS_BY_USER_CACHE, $userId);
        $userItemIds = Cache::read($cacheKey, USER_ITEMS_BY_USER_CACHE_DURATION);
        if ($userItemIds)
            return $userItemIds;

        $userItemIds = $this->find('all', array(
            'fields' => array(
                'UserItem.id',
            ),
            'conditions' => array(
                'UserItem.user_id' => $userId,
            ),
        ));
        $userItemIds = Set::classicExtract($userItemIds, '{n}.UserItem.id');

        Cache::write($cacheKey, $userItemIds, USER_ITEMS_BY_USER_CACHE_DURATION);
        return $userItemIds;
    }

    //---------------------------------------------------------------------------------------------
    function GetUserItem ($userItemId) {
        CheckNumeric($userItemId);

        $cacheKey = GenerateCacheKey(USER_ITEM_CACHE, $userItemId);
        $item = Cache::read($cacheKey, USER_ITEM_CACHE_DURATION);

        if ($item === false) {
            $item = $this->find('first', array(
                'conditions' => array(
                    'UserItem.id' => $userItemId,
                ),
                'contain' => array(
                    'CharacterEquippedAsWeapon' => array(
                        'fields' => array(
                            'CharacterEquippedAsWeapon.id',
                            'CharacterEquippedAsWeapon.name',
                        ),
                    ),
                    'CharacterEquippedAsArmor' => array(
                        'fields' => array(
                            'CharacterEquippedAsArmor.id',
                            'CharacterEquippedAsArmor.name',
                        ),
                    ),
                ),
            ));

            if ($item === false)
                return false;

            Cache::write($cacheKey, $item, USER_ITEM_CACHE_DURATION);
        }

        $itemData = $this->Item->GetItem($item['UserItem']['item_id']);
        $item['UserItem']['Item'] = $itemData['Item'];

        $itemMods = $this->ItemMod->GetItemMods($userItemId);
        $item['UserItem']['ItemMod'] = $itemMods['ItemMod'];

        // Display only character equipped, because only one can be real (item can't be both weapon
        // and armor and both be equipped).
        if ($item['UserItem']['Item']['weapon_type_id'] != '') {
            $item['CharacterEquipped'] = $item['CharacterEquippedAsWeapon'];
        } else if ($item['UserItem']['Item']['armor_type_id'] != '') {
            $item['CharacterEquipped'] = $item['CharacterEquippedAsArmor'];
        }
        unset($item['CharacterEquippedAsWeapon']);
        unset($item['CharacterEquippedAsArmor']);

        if (isset($item['CharacterEquipped'])) {
            $item['UserItem']['CharacterEquipped'] = $item['CharacterEquipped'];
            unset($item['CharacterEquipped']);
        }

        // Add refine bonus
        $itemType = '';
        if ($item['UserItem']['Item']['weapon_type_id'] != '')
            $itemType = 'weapon';
        else if ($item['UserItem']['Item']['armor_type_id'] != '')
            $itemType = 'armor';
        else
            $itemType = 'other';

        $refineBonus = $this->RefineTable->GetRefineBonusForLevel($item['UserItem']['refine'], $itemType);
        if ($itemType == 'weapon') {
            // Increases attack by a percent
            $atkBonus = floor($item['UserItem']['Item']['attack'] * $refineBonus / 100);

            // Make a minimum atk bonus of the refine level
            if ($atkBonus < $item['UserItem']['refine'])
                $atkBonus = $item['UserItem']['refine'];

            $item['UserItem']['refine_bonus'] = $atkBonus;
        } else if ($itemType == 'armor') {
            // Flat increase in reduction
            $item['UserItem']['refine_bonus'] = $refineBonus;
        }

        if ($item['UserItem']['refine'] > 0) {
            $item['UserItem']['refine_prefix'] = $this->RefineTable->GetRefinePrefix($item['UserItem']['refine'], $itemType);
        }

        return $item;
    }

    //---------------------------------------------------------------------------------------------
    function GetUserItems ($userItemIds) {

        $data = array();
        foreach ($userItemIds as $userItemId)
            $data[] = $this->GetUserItem($userItemId);

        return $data;
    }

    //---------------------------------------------------------------------------------------------
    function GiveUserItemToUser ($userItemId, $userId) {
        CheckNumeric($userItemId);
        CheckNumeric($userId);

        $item = $this->GetUserItem($userItemId);
        if ($item === false)
            return false;

        // Give item
        unset($item['UserItem']['id']); // Unset id so it makes a new item
        $item['UserItem']['user_id'] = $userId;
        $this->create();
        $this->save($item);

        if (isset($item['UserItem']['ItemMod'])) {
            foreach ($item['UserItem']['ItemMod'] as $itemMod) {
                $itemMod['user_item_id'] = $this->id;
                unset($itemMod['id']); // Unset id to create new item mods

                $this->ItemMod->create();
                $this->ItemMod->save($itemMod);
            }
        }

        // Invalidate cache
        $this->ClearUserItemCacheByUser($userId);

        return $this->id;
    }
}

?>