<?

define('WEAPON_TYPE_CACHE', 'weapon_types');
define('WEAPON_TYPE_CACHE_DURATION', 'long');

class WeaponType extends AppModel {

    var $hasMany = array(
        'Item',
    );

    var $hasAndBelongsToMany = array(
        'CClass' => array(
            'className' => 'CClass',
            'joinTable' => 'classes_weapon_use',
            'foreignKey' => 'weapon_type_id',
            'associationForeignKey' => 'class_id',
            'unique' => true
        ),
    );

    //---------------------------------------------------------------------------------------------
    // Uses get all types, this should probably be the other way around, but it saves
    // more cache entries.
    function GetWeaponTypeName ($typeId) {
        CheckNumeric($typeId);

        $types = $this->GetWeaponTypes();
        foreach ($types as $type)
            if ($type['WeaponType']['id'] == $typeId)
                return $type['WeaponType']['name'];

        return null;
    }

    //---------------------------------------------------------------------------------------------
    function GetWeaponTypes () {
        $cacheKey = GenerateCacheKey(WEAPON_TYPE_CACHE);

        $types = Cache::read($cacheKey, WEAPON_TYPE_CACHE_DURATION);
        if ($types)
            return $types;

        $types = $this->find('all');

        Cache::write(WEAPON_TYPE_CACHE, $types, WEAPON_TYPE_CACHE_DURATION);

        return $types;
    }
};

?>