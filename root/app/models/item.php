<?

// Caches individual items
define('ITEM_CACHE', 'item');
define('ITEM_CACHE_DURATION', 'long');

// Represents an item 'class.' Items don't exists unless they are instanced in UserItem.
class Item extends AppModel {

    var $hasMany = array(
        'UserItem',
    );

    var $belongsTo = array(
        'WeaponType',
        'ArmorType',
        'MiscItemType',
    );

    var $validate = array(
        'req_lvl' => array(
            'rule' => 'numeric',
            'allowEmpty' => false,
            'message' => 'This field is required.',
        ),
    );

    //---------------------------------------------------------------------------------------------
    // Clears the cache of an item.
    function ClearItemCache ($itemId) {
        CheckNumeric($itemId);

        $cacheKey = GenerateCacheKey(ITEM_CACHE, $itemId);
        Cache::delete($cacheKey, ITEM_CACHE_DURATION);
    }

    //---------------------------------------------------------------------------------------------
    function GetItem ($itemId) {
        CheckNumeric($itemId);

        $cacheKey = GenerateCacheKey(ITEM_CACHE, $itemId);
        $item = Cache::read($cacheKey, ITEM_CACHE_DURATION);
        if ($item)
            return $item;

        $item = $this->find('first', array(
            'conditions' => array(
                'Item.id' => $itemId,
            ),
            'contain' => array(
                'WeaponType',
                'ArmorType',
                'MiscItemType',
            ),
        ));
        if ($item === false)
            return false;

        $item['Item']['WeaponType'] = $item['WeaponType'];
        unset($item['WeaponType']);
        $item['Item']['ArmorType'] = $item['ArmorType'];
        unset($item['ArmorType']);
        $item['Item']['MiscItemType'] = $item['MiscItemType'];
        unset($item['MiscItemType']);


        $item['Item']['sell_price'] = $item['Item']['value'] > 0 ? ceil($item['Item']['value'] / 2) : 1;

        Cache::write($cacheKey, $item, ITEM_CACHE_DURATION);
        return $item;
    }

    //---------------------------------------------------------------------------------------------
    function GetItems ($itemIds) {

        $data = array();
        foreach ($itemIds as $itemId)
            $data[] = $this->GetItem($itemId);

        return $data;
    }

    //---------------------------------------------------------------------------------------------
    function GiveItemToUser ($itemId, $userId, $quantity = 1) {
        CheckNumeric($itemId);
        CheckNumeric($userId);
        CheckNumeric($quantity);

        $item = G($this->GetItem($itemId));
        if ($item['Item']['weapon_type_id'] != '' || $item['Item']['armor_type_id'] != '')
            IERR('Cannot give equipment to user, only misc items.');

        if ($quantity <= 0)
            IERR('Invalid quantity.');

        $userItem = $this->UserItem->find('first', array(
            'conditions' => array(
                'UserItem.user_id' => $userId,
                'UserItem.item_id' => $itemId,
            ),
        ));
        if ($userItem) {
            $this->UserItem->id = $userItem['UserItem']['id'];
            G(
                $this->UserItem->fastSave('quantity', $userItem['UserItem']['quantity'] + $quantity),
                'Failed to update stackable user item.'
            );
            $this->UserItem->ClearUserItemCache($this->UserItem->id);
        } else {
            $this->UserItem->create();
            $success = $this->UserItem->save(array(
                'item_id' => $itemId,
                'user_id' => $userId,
                'name' => $item['Item']['name']
            ));
            if (!$success)
                IERR('Failed to save new stackable user item.');
            $this->UserItem->ClearUserItemCacheByUser($userId);
        }
    }
}

?>