<?php

class CClassesController extends AppController {

    var $pageTitle = 'Classes';

    var $paginate = array(
        'limit' => 0,
        'contain' => array(
            'CClass1',
            'CClass2',
            'CClass3',
            'CClass4',
        ),
    );

    //---------------------------------------------------------------------------------------------
    function admin_index () {
        $this->set('classes', $this->paginate());
    }

    //---------------------------------------------------------------------------------------------
    function admin_view ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid class.');
            $this->redirect(array('action' => 'index'));
        }

        // Accept names too
        if (is_numeric($id))
            $conditions = array('CClass.id' => $id);
        else
            $conditions = array('CClass.name' => $id);

        $class = $this->CClass->find('first', array(
            'conditions' => array(
                $conditions,
            ),
            'contain' => array(
                'CClass1' => array(
                    'fields' => array(
                        'CClass1.id',
                        'CClass1.name',
                    ),
                ),
                'CClass2' => array(
                    'fields' => array(
                        'CClass2.id',
                        'CClass2.name',
                    ),
                ),
                'CClass3' => array(
                    'fields' => array(
                        'CClass3.id',
                        'CClass3.name',
                    ),
                ),
                'CClass4' => array(
                    'fields' => array(
                        'CClass4.id',
                        'CClass4.name',
                    ),
                ),
                'Bonus' => array(
                    'BonusType',
                ),
                'WeaponType',
            ),
        ));

        if (!$class) {
            $this->Session->setFlash('Invalid class.');
            $this->redirect(array('action' => 'index'));
        }

        // Build list of locations that the bonuses affect
        $locations = Set::classicExtract($class, 'Bonus.{n}.locations');
        $locationSet = array();
        foreach ($locations as $location)
            $locationSet = array_merge($locationSet, $location);

        $class['CClass']['bonus_locations'] = $locationSet;

        // Build list of weapon that the class can use.
        $weapons = Set::classicExtract($class, 'WeaponType.{n}.name');
        foreach ($weapons as &$weapon)
            $weapon = Inflector::pluralize($weapon);

        $class['CClass']['weapon_use'] = $weapons;

        $this->set('class', $class);
    }

    //---------------------------------------------------------------------------------------------
    function admin_add () {
        if (!empty($this->data)) {
            $this->CClass->create();
            if ($this->CClass->save($this->data)) {
                $this->Session->setFlash('Class saved.');
                $this->redirect(array('action' => 'view', $this->CClass->id));
            } else {
                $this->Session->setFlash('Could not save class.');
            }
        }
        $classes = $this->CClass->find('list');
        $weaponTypes = $this->CClass->WeaponType->find('list');
        $this->set('classes', $classes);
        $this->set('weaponTypes', $weaponTypes);
    }

    //---------------------------------------------------------------------------------------------
    function admin_edit ($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid class.');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->CClass->save($this->data)) {
                $classId = $this->data['CClass']['id'];

                // BRUTE FORCE!!!!
                $this->CClass->query("DELETE FROM classes_weapon_use WHERE class_id = {$classId}");
                foreach ($this->data['CClass']['WeaponType'] as $id) {
                    $this->CClass->query("INSERT INTO classes_weapon_use (class_id, weapon_type_id) VALUES({$classId}, {$id})");
                }

                $this->Session->setFlash('Class saved.');
                $this->redirect(array('action' => 'view', $this->CClass->id));
            } else {
                $this->Session->setFlash('Could not save class.');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->CClass->find('first', array(
                'conditions' => array(
                    'id' => $id,
                ),
                'contain' => array(
                    'WeaponType',
                ),
            ));
        }
        $classes = $this->CClass->find('list');
        $weaponTypes = $this->CClass->WeaponType->find('list');
        $this->set('classes', $classes);
        $this->set('weaponTypes', $weaponTypes);
    }

    //---------------------------------------------------------------------------------------------
    function admin_delete ($id = null) {
        if (!$id) {
            $this->Session->setFlash('Invalid ID for class.');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->CClass->del($id)) {
            $this->Session->setFlash('Class deleted.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash('Could not delete class.');
            $this->redirect($this->referer());
        }
    }

    function __GetPromotionLevel ($classId, $promotionId) {
        if ($promotionId == 0) return 0;

        $data = $this->CClass->find('first', array(
            'fields' => array(
                'promote_class_1_id',
                'promote_class_2_id',
                'promote_class_3_id',
                'promote_class_4_id',
                'promote_class_1_level',
                'promote_class_2_level',
                'promote_class_3_level',
                'promote_class_4_level',
            ),
            'contain' => array(
            ),
            'conditions' => array(
                'CClass.id' => $classId,
            ),
        ));

        for ($i = 1; $i <= 4; $i++) {
            $id = $data['CClass']['promote_class_' . $i . '_id'];
            if ($id == $promotionId)
                return $data['CClass']['promote_class_' . $i . '_level'];
        }
    }

    //---------------------------------------------------------------------------------------------
    function admin_growth () {

        if (!empty($this->data)) {

            // Simulate leveling up
            $level = 1;
            $growth = array(
                'str' => $this->data['GrowthCalc']['base_growth_str'],
                'vit' => $this->data['GrowthCalc']['base_growth_vit'],
                'int' => $this->data['GrowthCalc']['base_growth_int'],
                'luk' => $this->data['GrowthCalc']['base_growth_luk'],
            );
            $stats = array(
                1 => array(
                    'str' => 0,
                    'vit' => 0,
                    'int' => 0,
                    'luk' => 0,
                    'growth_str' => $this->data['GrowthCalc']['base_growth_str'],
                    'growth_vit' => $this->data['GrowthCalc']['base_growth_vit'],
                    'growth_int' => $this->data['GrowthCalc']['base_growth_int'],
                    'growth_luk' => $this->data['GrowthCalc']['base_growth_luk'],
                ),
            );
            $curClassId = 1;
            $promotionClassIds = array(
                isset($this->data['GrowthCalc']['first_class_id']) ? $this->data['GrowthCalc']['first_class_id'] : 0,
                isset($this->data['GrowthCalc']['second_class_id']) ? $this->data['GrowthCalc']['second_class_id'] : 0,
                isset($this->data['GrowthCalc']['third_class_id']) ? $this->data['GrowthCalc']['third_class_id'] : 0,
                isset($this->data['GrowthCalc']['fourth_class_id']) ? $this->data['GrowthCalc']['fourth_class_id'] : 0,
                isset($this->data['GrowthCalc']['fifth_class_id']) ? $this->data['GrowthCalc']['fifth_class_id'] : 0,
                isset($this->data['GrowthCalc']['sixth_class_id']) ? $this->data['GrowthCalc']['sixth_class_id'] : 0,
                isset($this->data['GrowthCalc']['seventh_class_id']) ? $this->data['GrowthCalc']['seventh_class_id'] : 0,
                isset($this->data['GrowthCalc']['eighth_class_id']) ? $this->data['GrowthCalc']['eighth_class_id'] : 0,
            );
            $promotionClassIndex = 0;
            $promotionClassLevel = $this->__GetPromotionLevel($curClassId, $promotionClassIds[$promotionClassIndex]);

            while ($level++ < 99) {
                $stats[$level]['str'] = $stats[$level - 1]['str'] + $growth['str'];
                $stats[$level]['vit'] = $stats[$level - 1]['vit'] + $growth['vit'];
                $stats[$level]['int'] = $stats[$level - 1]['int'] + $growth['int'];
                $stats[$level]['luk'] = $stats[$level - 1]['luk'] + $growth['luk'];
                $stats[$level]['str'] = $stats[$level]['str'] > 0 ? $stats[$level]['str'] : 0;
                $stats[$level]['vit'] = $stats[$level]['vit'] > 0 ? $stats[$level]['vit'] : 0;
                $stats[$level]['int'] = $stats[$level]['int'] > 0 ? $stats[$level]['int'] : 0;
                $stats[$level]['luk'] = $stats[$level]['luk'] > 0 ? $stats[$level]['luk'] : 0;
                $stats[$level]['growth_str'] = $growth['str'];
                $stats[$level]['growth_int'] = $growth['int'];
                $stats[$level]['growth_vit'] = $growth['vit'];
                $stats[$level]['growth_luk'] = $growth['luk'];

                if ($level == $promotionClassLevel) {

                    // "Promote" class
                    $curClassId = $promotionClassIds[$promotionClassIndex];
                    $promotionClassIndex++;

                    // Set the next class up's level
                    if ($promotionClassIndex < count($promotionClassIds))
                        $promotionClassLevel = $this->__GetPromotionLevel($curClassId, $promotionClassIds[$promotionClassIndex]);
                    else
                        $promotionClassLevel = -1;

                    $growths = $this->CClass->find('first', array(
                        'fields' => array(
                            'growth_str',
                            'growth_vit',
                            'growth_int',
                            'growth_luk',
                        ),
                        'conditions' => array(
                            'CClass.id' => $curClassId,
                        ),
                    ));

                    $growth['str'] += $growths['CClass']['growth_str'];
                    $growth['vit'] += $growths['CClass']['growth_vit'];
                    $growth['int'] += $growths['CClass']['growth_int'];
                    $growth['luk'] += $growths['CClass']['growth_luk'];
                }
            }

            $this->set('stats', $stats);
        }

        // Build table of class -> (promotion ids -> names)
        $promotionData = $this->CClass->find('all', array(
            'fields' => array(
                'CClass.id',
                'CClass.promote_class_1_id',
                'CClass.promote_class_2_id',
                'CClass.promote_class_3_id',
                'CClass.promote_class_4_id',
            ),
        ));

        $names = $this->CClass->find('list');
        $promotions = array();
        foreach ($promotionData as &$class) {
            $promotions[$class['CClass']['id']] = array();

            for ($i = 1; $i <= 4; $i++) {
                if ($class['CClass']['promote_class_' . $i . '_id']) {
                    $promotionId = $class['CClass']['promote_class_' . $i . '_id'];
                    $promotions[$class['CClass']['id']][$promotionId] = $names[$promotionId];
                }
            }
        }
        // Complicated, but you end up with something like
        //Array
        //(
        //    [1 (class id)] => Array
        //        (
        //            [5 (class id of swordsman)] => Swordsman
        //            [53] => Spellcaster
        //        )
        //
        //    [5] => Array
        //        (
        //            [6] => Knight
        //        )
        //)

        $this->set('promotions', $promotions);
    }
}
?>