<?php echo $form->create('Bonus');?>
    <fieldset>
        <legend>Edit Bonus</legend>
    <?php
        echo $form->input('id');
        echo $form->input('class_id');

        echo $form->label('Location');
        $ui->locationInputGrid();

        echo $form->input('amount');
        echo $form->input('duration');
        echo $form->input('bonus_type_id');
    ?>
    </fieldset>
<?php echo $form->end('Submit');?>
