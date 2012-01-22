<?php echo $form->create('Mission');?>
    <fieldset>
        <legend>Edit Mission</legend>

        <?
            echo $form->input('id');
            echo $form->input('name');
            echo $form->input('description');
            echo $form->input('mission_group_id');
            echo $form->input('prereqs');
            echo $form->input('restrictions');
            echo $form->input('enemy_formation_id');
            echo $form->input('is_final_mission');
            echo $form->input('difficulty_variation');
            echo $form->input('only_once_per_user');
        ?>
    </fieldset>
<?php echo $form->end('Submit');?>