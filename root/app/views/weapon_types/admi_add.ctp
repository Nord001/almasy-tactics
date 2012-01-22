<?= $form->create('WeaponType');?>
    <fieldset>
        <legend>Add Weapon Type</legend>
    <?php
        echo $form->input('name');
        echo $form->input('attack_type');
        echo $form->input('sprite');
    ?>
    </fieldset>
<?= $form->end('Submit');?>
