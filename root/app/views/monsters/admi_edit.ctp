<?= $form->create('Monster');?>
    <fieldset>
        <legend>Edit Monster</legend>
        <?
            echo $form->input('id');
            echo $form->input('name');
            echo $form->input('description');
        ?>

        <!-- Stats -->
        <fieldset>
            <legend>Stats</legend>
        <?
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