<?= $form->create('ArmorType');?>
    <fieldset>
        <legend>Edit Armor Type</legend>
    <?php
        echo $form->input('id');
        echo $form->input('name');
        echo $form->input('speed_mod');
        echo $form->input('sprite');
    ?>
    </fieldset>
<?= $form->end('Submit');?>
