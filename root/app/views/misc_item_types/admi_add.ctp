<?= $form->create('MiscItemType');?>
    <fieldset>
        <legend>Add Misc Item Type</legend>
    <?php
        echo $form->input('name');
        echo $form->input('sprite');
    ?>
    </fieldset>
<?= $form->end('Submit');?>
