<?php echo $form->create('Monster');?>
    <fieldset>
        <legend>Add Monster</legend>

        <?
            echo $form->input('name');
            echo $form->input('description');
        ?>

        <!-- Stats -->
        <fieldset style = 'margin-top: 10px'>
            <legend>Stats</legend>
        <?
            echo $form->input('min_range', array('value' => 1));
            echo $form->input('max_range', array('value' => 1));
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
<?php echo $form->end('Submit');?>