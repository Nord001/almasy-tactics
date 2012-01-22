<?= $form->create('MiscItemType');?>
    <fieldset>
        <legend>Edit Misc Item Type</legend>
    <?php
        echo $form->input('id');
        echo $form->input('name');
        echo $form->input('sprite');
    ?>
    </fieldset>
<?= $form->end('Submit');?>
