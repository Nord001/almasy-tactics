<?

define('ARMOR_TYPE_CACHE', 'armor_types');
define('ARMOR_TYPE_CACHE_DURATION', 'long');

class ArmorType extends AppModel {
    var $hasMany = array('Item');

    //---------------------------------------------------------------------------------------------
    // Uses get all types, this should probably be the other way around, but it saves
    // more cache entries.
    function GetArmorTypeName ($typeId) {
        CheckNumeric($typeId);

        $types = $this->GetArmorTypes();
        foreach ($types as $type)
            if ($type['ArmorType']['id'] == $typeId)
                return $type['ArmorType']['name'] . ' Armor';

        return null;
    }

    //---------------------------------------------------------------------------------------------
    function GetArmorTypes () {
        $cacheKey = GenerateCacheKey(ARMOR_TYPE_CACHE);

        $types = Cache::read($cacheKey, ARMOR_TYPE_CACHE_DURATION);
        if ($types)
            return $types;

        $types = $this->find('all');

        Cache::write(ARMOR_TYPE_CACHE, $types, ARMOR_TYPE_CACHE_DURATION);

        return $types;
    }
}

?>