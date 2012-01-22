<?

define('MISC_ITEM_TYPE_CACHE', 'misc_item_types');
define('MISC_ITEM_TYPE_CACHE_DURATION', 'long');

class MiscItemType extends AppModel {
    var $hasMany = array('Item');

    //---------------------------------------------------------------------------------------------
    // Uses get all types, this should probably be the other way around, but it saves
    // more cache entries.
    function GetMiscItemTypeName ($typeId) {
        CheckNumeric($typeId);

        $types = $this->GetMiscItemTypes();
        foreach ($types as $type)
            if ($type['MiscItemType']['id'] == $typeId)
                return $type['MiscItemType']['name'];

        return null;
    }

    //---------------------------------------------------------------------------------------------
    function GetMiscItemTypes () {
        $cacheKey = GenerateCacheKey(MISC_ITEM_TYPE_CACHE);

        $types = Cache::read($cacheKey, MISC_ITEM_TYPE_CACHE_DURATION);
        if ($types)
            return $types;

        $types = $this->find('all');

        Cache::write(MISC_ITEM_TYPE_CACHE, $types, MISC_ITEM_TYPE_CACHE_DURATION);

        return $types;
    }
}

?>