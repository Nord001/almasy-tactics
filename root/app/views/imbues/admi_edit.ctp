<?php echo $form->create('Imbue');?>
    <fieldset>
        <legend>Edit Imbue</legend>
    <?php
        echo $form->input('id');
        echo $form->input('name');
        echo $form->input('item_type');
    ?>
    </fieldset>
<?php echo $form->end('Submit');?>
