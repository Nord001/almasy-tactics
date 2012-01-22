<?= $form->create('WeaponType');?>
    <fieldset>
        <legend>Edit Weapon Type</legend>
    <?php
        echo $form->input('id');
        echo $form->input('name');
        echo $form->input('attack_type');
        echo $form->input('sprite');
    ?>
    </fieldset>
<?= $form->end('Submit');?>
