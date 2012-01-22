<?php echo $form->create('Mission');?>
    <fieldset>
        <legend>Add Mission</legend>

        <?
            echo $form->input('name');
            echo $form->input('description');
            echo $form->input('mission_group');
            echo $form->input('prereqs');
            echo $form->input('restrictions');
            echo $form->input('enemy_formation_id');
            echo 'This randomly scales the HP and Damage of the enemy formation within this amount in percent.';
            echo $form->input('difficulty_variation');
            echo $form->input('only_once_per_user');
            echo 'If selected, this mission can only be done once per user.';
            echo $form->input('is_final_mission');
            echo 'The final mission resolves the mission group and allows the missions in the group to be restarted.';
        ?>
    </fieldset>
<?php echo $form->end('Submit');?>