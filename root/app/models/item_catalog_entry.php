<?

define('ITEM_CATALOG_CACHE', 'item_catalog_type');
define('ITEM_CATALOG_DURATION', 'long');

define('ITEM_CATALOG_ENTRY_CACHE', 'item_catalog_type_entry');
define('ITEM_CATALOG_ENTRY_DURATION', 'long');

define('ITEM_CATALOG_ENTRY_ITEM_ID_CACHE', 'item_catalog_type_entry_id');
define('ITEM_CATALOG_ENTRY_ITEM_ID_DURATION', 'long');

class ItemCatalogEntry extends AppModel {

    var $useTable = 'item_catalog';

    var $belongsTo = array(
        'UserItem',
    );

    //---------------------------------------------------------------------------------------------
    // Gets item ids in the store of a particular type (weapon/armor) and
    // type id (weapon type or armor type)
    function GetUserItemIdsByType ($type, $typeId) {
        CheckNumeric($typeId);

        $cacheKey = GenerateCacheKey(ITEM_CATALOG_CACHE, $type, $typeId);
        $userItemIds = Cache::read($cacheKey, ITEM_CATALOG_DURATION);

        if ($userItemIds)
            return $userItemIds;

        $items = $this->query("
            SELECT
                `UserItem`.`id`
            FROM
                `item_catalog` AS `ItemCatalogEntry`
            INNER JOIN
                `user_items` AS `UserItem` ON `UserItem`.`id` = `ItemCatalogEntry`.`user_item_id`
            INNER JOIN
                `items` AS `Item` ON `UserItem`.`item_id` = `Item`.`id`
            WHERE
                `Item`.`{$type}_type_id` = {$typeId}
            ORDER BY
                `Item`.`req_lvl`"
        );

        $userItemIds = Set::classicExtract($items, '{n}.UserItem.id');

        Cache::write($cacheKey, $userItemIds, ITEM_CATALOG_DURATION);
        return $userItemIds;
    }

    //---------------------------------------------------------------------------------------------
    function GetItemEntry ($userItemId) {
        CheckNumeric($userItemId);

        $cacheKey = GenerateCacheKey(ITEM_CATALOG_ENTRY_CACHE, $userItemId);
        $entry = Cache::read($cacheKey, ITEM_CATALOG_ENTRY_DURATION);
        if ($entry === false) {
            $entry = $this->find('first', array(
                'conditions' => array(
                    'ItemCatalogEntry.user_item_id' => $userItemId,
                ),
            ));

            Cache::write($cacheKey, $entry, ITEM_CATALOG_ENTRY_DURATION);
        }

        $userItem = $this->UserItem->GetUserItem($userItemId);
        $entry['UserItem'] = $userItem['UserItem'];

        return $entry;
    }

    //---------------------------------------------------------------------------------------------
    // Fetches item entries and user items
    function GetItemEntries ($userItemIds) {
        $items = array();

        foreach ($userItemIds as $id)
            $items[] = $this->GetItemEntry($id);

        return $items;
    }

    //---------------------------------------------------------------------------------------------
    // Gets all items by Iggly that aren't being sold.
    function GetUnsoldUserItemIds () {
        $items = $this->UserItem->find('all', array(
            'fields' => array(
                'UserItem.id',
            ),
            'conditions' => array(
                'user_id' => 1,
                'ItemCatalogEntry.id IS NULL',
            ),
            'contain' => array(
                'ItemCatalogEntry',
            ),
        ));
        $items = Set::classicExtract($items, '{n}.UserItem.id');

        return $items;
    }

    //---------------------------------------------------------------------------------------------
    function PurchaseItem ($userId, $userItemId) {
        CheckNumeric($userId);
        CheckNumeric($userItemId);

        $entry = $this->GetItemEntry($userItemId);
        if ($entry === false)
            return false;

        $item = $this->UserItem->GetUserItem($userItemId);
        if ($item === false)
            return false;

        $cost = $entry['ItemCatalogEntry']['use_item_value'] ? $item['UserItem']['Item']['value'] : $entry['ItemCatalogEntry']['cost'];

        // Ensure they can buy the item
        if (!$this->UserItem->User->DeductMoney($userId, $cost))
            return false;

        $this->UserItem->GiveUserItemToUser($userItemId, $userId);

        // Increment sales
        $this->query("
            UPDATE
                `item_catalog` as `ItemCatalogEntry`
            SET
                `ItemCatalogEntry`.`sales` = `ItemCatalogEntry`.`sales` + 1
            WHERE
                `ItemCatalogEntry`.`id` = {$entry['ItemCatalogEntry']['id']}
        ");

        return true;
    }

    //---------------------------------------------------------------------------------------------
    function SellItem ($userItemId, $quantity) {
        CheckNumeric($userItemId);

        $userItem = G($this->UserItem->GetUserItem($userItemId));

        if ($userItem['UserItem']['quantity'] < $quantity)
            IERR('Insufficient quantity.');

        $userId = $userItem['UserItem']['user_id'];
        G($this->UserItem->User->GiveMoney($userId, $userItem['UserItem']['Item']['sell_price'] * $quantity));
        $newQuantity = $userItem['UserItem']['quantity'] - $quantity;
        if ($newQuantity > 0) {
            $this->UserItem->id = $userItemId;
            G($this->UserItem->fastSave('quantity', $newQuantity), 'Failed to save new quantity.');
            $this->UserItem->ClearUserItemCache($userItemId);
        } else {
            $this->UserItem->User->Character->UnequipItem($userItemId);
            G($this->UserItem->del($userItemId), 'Failed to delete item.');
            $this->UserItem->ClearUserItemCacheByUser($userId);
        }
    }
}

?>