<?php echo $form->create('MissionGroup');?>
    <fieldset>
        <legend>Add Mission Group</legend>

        <?
            echo $form->input('name');
            echo $form->input('description');
            echo $form->input('icon');
        ?>
    </fieldset>
<?php echo $form->end('Submit');?>