<?

define('PROMOTION_CLASS_CACHE', 'promotion_classes');
define('PROMOTION_CLASS_CACHE_DURATION', 'long');

define('WEAPON_EQUIP_CACHE', 'weapon_equips');
define('WEAPON_EQUIP_CACHE_DURATION', 'long');

define('CLASS_CACHE', 'class');
define('CLASS_CACHE_DURATION', 'long');

define('CLASS_LISTING_CACHE', 'class_listing');
define('CLASS_LISTING_CACHE_DURATION', 'long');

define('CLASS_TREE_CACHE', 'class_tree');
define('CLASS_TREE_CACHE_DURATION', 'long');

define('BASE_CLASS_CACHE', 'class_base');
define('BASE_CLASS_CACHE_DURATION', 'long');

class CClass extends AppModel {
    var $displayField = 'name';
    var $useTable = 'classes';

    var $hasMany = array(
        'Bonus',
        'Character',
    );

    var $belongsTo = array(
        'CClass1' => array(
            'className' => 'CClass',
            'foreignKey' => 'promote_class_1_id',
        ),
        'CClass2' => array(
            'className' => 'CClass',
            'foreignKey' => 'promote_class_2_id',
        ),
        'CClass3' => array(
            'className' => 'CClass',
            'foreignKey' => 'promote_class_3_id',
        ),
        'CClass4' => array(
            'className' => 'CClass',
            'foreignKey' => 'promote_class_4_id',
        ),
    );

    var $hasAndBelongsToMany = array(
        'WeaponType' => array(
            'className' => 'WeaponType',
            'joinTable' => 'classes_weapon_use',
            'foreignKey' => 'class_id',
            'associationForeignKey' => 'weapon_type_id',
            'unique' => true
        ),
    );

    //--------------------------------------------------------------------------------------------
    // Returns an associative array with each entry: class_id => array of weapon type ids that it can equip
    function GetWeaponEquipInfo () {
        $cacheKey = GenerateCacheKey(WEAPON_EQUIP_CACHE);
        $weaponEquips = Cache::read($cacheKey, WEAPON_EQUIP_CACHE_DURATION);
        if ($weaponEquips)
            return $weaponEquips;

        $weaponEquips = $this->query('
            SELECT
                `class_id`,
                `weapon_type_id`
            FROM
                `classes_weapon_use`
        ');

        $data = array();
        foreach ($weaponEquips as $entry) {
            $data[$entry['classes_weapon_use']['class_id']][] = $entry['classes_weapon_use']['weapon_type_id'];
        }

        Cache::write($cacheKey, $data, WEAPON_EQUIP_CACHE_DURATION);

        return $data;
    }

    //--------------------------------------------------------------------------------------------
    function GetBonusLocations ($bonuses) {
        $locations = Set::classicExtract($bonuses, '{n}.locations');
        $locationSet = array();
        foreach ($locations as $location)
            $locationSet = array_merge($locationSet, $location);

        return $locationSet;
    }

    //--------------------------------------------------------------------------------------------
    // Gets the classes that can promote from the given class, along with their required levels.
    function GetPromotionClasses ($classId) {
        CheckNumeric($classId);

        $cacheKey = GenerateCacheKey(PROMOTION_CLASS_CACHE, $classId);
        $promotionClasses = Cache::read($cacheKey, PROMOTION_CLASS_CACHE_DURATION);
        if ($promotionClasses !== false)
            return $promotionClasses;

        $this->id = $classId;

        $promotionClassData = array(
            $this->field('promote_class_1_id') => $this->field('promote_class_1_level'),
            $this->field('promote_class_2_id') => $this->field('promote_class_2_level'),
            $this->field('promote_class_3_id') => $this->field('promote_class_3_level'),
            $this->field('promote_class_4_id') => $this->field('promote_class_4_level'),
        );

        $promotionClasses = $this->find('all', array(
            'conditions' => array(
                'CClass.id' => array_keys($promotionClassData),
                'CClass.monster' => 0,
            ),
            'fields' => array(
                'CClass.id',
                'CClass.name',
                'CClass.description',
                'CClass.bonus_name',
                'CClass.bonus_description',
                'CClass.growth_str',
                'CClass.growth_int',
                'CClass.growth_vit',
                'CClass.growth_luk',
                'CClass.face_icon',
            ),
            'contain' => array(
                'Bonus' => array(
                    'BonusType',
                ),
                'WeaponType',
            ),
        ));

        // Build list of locations that the bonuses affect
        foreach ($promotionClasses as &$class) {
            $class['CClass']['bonus_locations'] = $this->GetBonusLocations($class['Bonus']);
        }

        // Set the classes's required level to be what it takes to get there from the previous class.
        // Promotion Class data stores ids => required levels, so we look up the class there.
        foreach ($promotionClasses as &$class)
            $class['CClass']['required_level'] = $promotionClassData[$class['CClass']['id']];

        Cache::write($cacheKey, $promotionClasses, PROMOTION_CLASS_CACHE_DURATION);

        return $promotionClasses;
    }

    //--------------------------------------------------------------------------------------------
    function GetClassByName ($name) {
        $class = $this->find('first', array(
            'fields' => array(
                'CClass.id',
            ),
            'conditions' => array(
                'CClass.name' => $name,
                'CClass.monster' => 0,
            ),
        ));

        if ($class === false)
            return false;

        return $this->GetClass($class['CClass']['id']);
    }

    //--------------------------------------------------------------------------------------------
    function GetClass ($classId) {
        CheckNumeric($classId);

        $cacheKey = GenerateCacheKey(CLASS_CACHE, $classId);
        $class = Cache::read($cacheKey, CLASS_CACHE_DURATION);
        if ($class === false) {
            $class = $this->find('first', array(
                'conditions' => array(
                    'CClass.id' => $classId,
                    'CClass.monster' => 0,
                ),
                'contain' => array(
                    'Bonus' => array(
                        'BonusType',
                    ),
                    'WeaponType',
                ),
            ));

            if ($class === false)
                return false;

            $class['CClass']['Bonus'] = $class['Bonus'];
            $class['CClass']['WeaponType'] = $class['WeaponType'];
            unset($class['Bonus']);
            unset($class['WeaponType']);

            $class['CClass']['bonus_locations'] = $this->GetBonusLocations($class['CClass']['Bonus']);

            Cache::write($cacheKey, $class, CLASS_CACHE_DURATION);
        }

        return $class;
    }

    //--------------------------------------------------------------------------------------------
    function GetClassTree () {
        $classes = Cache::read(CLASS_TREE_CACHE, CLASS_TREE_CACHE_DURATION);
        if ($classes === false || true) {

            $classes = $this->find('all', array(
                'fields' => array(
                    'CClass.id',
                    'CClass.name',
                    'CClass.face_icon',
                    'CClass.promote_class_1_id',
                    'CClass.promote_class_2_id',
                    'CClass.promote_class_3_id',
                    'CClass.promote_class_4_id',
                    'CClass.promote_class_1_level',
                    'CClass.promote_class_2_level',
                    'CClass.promote_class_3_level',
                    'CClass.promote_class_4_level',
                ),
                'conditions' => array(
                            'CClass.id >=' => 115,
                            'CClass.id <=' => 127
                ),
            ));

            foreach ($classes as &$class) {
                $class['CClass']['promotions'] = array();
                for ($i = 1; $i <= 4; $i++) {
                    if (is_numeric($class['CClass']['promote_class_' . $i . '_id'])) {
                        $class['CClass']['promotions'][] = array(
                            'id' => $class['CClass']['promote_class_' . $i . '_id'],
                            'level' => $class['CClass']['promote_class_' . $i . '_level'],
                        );
                    }
                    unset($class['CClass']['promote_class_' . $i . '_id']);
                    unset($class['CClass']['promote_class_' . $i . '_level']);
                }
            }

            $ids = Set::classicExtract($classes, '{n}.CClass.id');
            $indexesByIds = array_flip($ids);

            foreach ($classes as &$class) {
                foreach ($class['CClass']['promotions'] as &$promotionClass) {
                    $index = $indexesByIds[$promotionClass['id']];
                    $classes[$index]['CClass']['required_level'] = $promotionClass['level'];
                    $promotionClass['index'] = $index;
                }
            }

            $buckets = array_fill_keys(range(0, 10), 0);
            foreach ($classes as &$class) {
                if (!isset($class['CClass']['required_level'])) {
                    $class['CClass']['x'] = 0;
                    $class['CClass']['y'] = 0;
                } else {
                    $bucket = intval($class['CClass']['required_level'] / 20);
                    $class['CClass']['y'] = ($class['CClass']['required_level'] - 11) * 10;
                    $class['CClass']['x'] = $buckets[$bucket] * 130;
                    $buckets[$bucket]++;
                }
            }

            Cache::write(CLASS_TREE_CACHE, $classes, CLASS_TREE_CACHE_DURATION);
        }

        return $classes;
    }

    //--------------------------------------------------------------------------------------------
    function GetClassListing () {
        $classes = Cache::read(CLASS_LISTING_CACHE, CLASS_LISTING_CACHE_DURATION);
        if ($classes !== false)
            return $classes;

        $classData = $this->find('all', array(
            'fields' => array(
                'CClass.id',
                'CClass.name',
                'CClass.face_icon',
            ),
            'conditions' => array(
                'CClass.monster' => 0,
            ),
        ));

        $classes = array();
        foreach ($classData as $class)
            $classes[$class['CClass']['id']] = array('name' => $class['CClass']['name'], 'face_icon' => $class['CClass']['face_icon']);

        unset($classes['128']); // Remove Alex
        asort($classes);

        Cache::write(CLASS_LISTING_CACHE, $classes, CLASS_LISTING_CACHE_DURATION);

        return $classes;
    }

    //--------------------------------------------------------------------------------------------
    // Returns the base classes for a given class. For example, a Master Knight's base classes are
    // Swordsman and Knight. A Swordsman's base classes is just Swordsman.
    function GetBaseClasses ($classId) {
        CheckNumeric($classId);

        $cacheKey = GenerateCacheKey(BASE_CLASS_CACHE, $classId);
        $baseClasses = Cache::read($cacheKey, BASE_CLASS_CACHE_DURATION);
        if ($baseClasses === false) {

            $baseClasses = array();

            // If class is already one of the base, just set first and exit.
            if ($classId == SWORDSMAN_CLASS_ID ||
                $classId == SPELLCASTER_CLASS_ID ||
                $classId == TRAINEE_CLASS_ID) {
                    // Found stopping point.
                    $class = $this->GetClass($classId);
                    $baseClasses['first'] = $class['CClass']['name'];
            }

            // Search back through classes for a class that promotes to this class, and repeat until you hit novice.
            while(true && empty($baseClasses)) {
                $nextClass = $this->find('first', array(
                    'fields' => array(
                        'CClass.id',
                    ),
                    'conditions' => array(
                        'OR' => array(
                            'CClass.promote_class_1_id' => $classId,
                            'CClass.promote_class_2_id' => $classId,
                            'CClass.promote_class_3_id' => $classId,
                            'CClass.promote_class_4_id' => $classId,
                        ),
                    ),
                ));

                if ($nextClass === false)
                    break;

                if ($nextClass['CClass']['id'] == SWORDSMAN_CLASS_ID ||
                    $nextClass['CClass']['id'] == SPELLCASTER_CLASS_ID ||
                    $nextClass['CClass']['id'] == TRAINEE_CLASS_ID) {
                        // Found stopping point.
                        $class = $this->GetClass($nextClass['CClass']['id']);
                        $baseClasses['first'] = $class['CClass']['name'];

                        $class = $this->GetClass($classId);
                        $baseClasses['second'] = $class['CClass']['name'];
                        break;
                }

                $classId = $nextClass['CClass']['id'];
            }

            Cache::write($cacheKey, $baseClasses, BASE_CLASS_CACHE_DURATION);
        }

        return $baseClasses;
    }
}

?>
