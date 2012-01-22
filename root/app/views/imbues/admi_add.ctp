<?php echo $form->create('Imbue');?>
    <fieldset>
        <legend>Add Imbue</legend>
    <?php
        echo $form->input('name');
        echo $form->input('item_type');
    ?>
    </fieldset>
<?php echo $form->end('Submit');?>
