<?= $form->create('CClass');?>
    <fieldset>
        <legend>Edit Class</legend>
        <?
            echo $form->input('id');
            echo $form->input('name');
            echo $form->input('description');
        ?>

        <!-- Stats -->
        <fieldset>
            <legend>Stats</legend>
        <?
            echo $form->input('growth_str', array('label' => '+STR'));
            echo $form->input('growth_int', array('label' => '+INT'));
            echo $form->input('growth_vit', array('label' => '+VIT'));
            echo $form->input('growth_luk', array('label' => '+LUK'));
            echo $form->input('min_range');
            echo $form->input('max_range');
            echo $form->input('speed');
            echo $form->input('bonus_name');
            echo $form->input('bonus_description');
            echo $form->input('melee_atk_stat', array(
                'type' => 'select',
                'options' => array(
                    'str' => 'STR',
                    'int' => 'INT',
                    'both' => 'Both',
                ),
            ));
            echo $form->input('ranged_atk_stat', array(
                'type' => 'select',
                'options' => array(
                    'str' => 'STR',
                    'int' => 'INT',
                    'both' => 'Both',
                ),
            ));

            echo $form->input('WeaponType', array('multiple' => true));
        ?>
        </fieldset>

        <!-- Promotion -->
        <fieldset>
            <legend>Promotion</legend>
        <?
            echo $form->input('promote_class_1_id', array('options' => $classes, 'empty' => true));
            echo $form->input('promote_class_1_level');
            echo $form->input('promote_class_2_id', array('options' => $classes, 'empty' => true));
            echo $form->input('promote_class_2_level');
            echo $form->input('promote_class_3_id', array('options' => $classes, 'empty' => true));
            echo $form->input('promote_class_3_level');
            echo $form->input('promote_class_4_id', array('options' => $classes, 'empty' => true));
            echo $form->input('promote_class_4_level');
        ?>
        </fieldset>

        <!-- Graphics -->
        <fieldset>
            <legend>Graphics</legend>
        <?
            echo $form->input('battle_icon');
            echo $form->input('face_icon');
        ?>
        </fieldset>

    </fieldset>
<?= $form->end('Submit');?>