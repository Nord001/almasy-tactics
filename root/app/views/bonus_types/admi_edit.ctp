<?= $form->create('BonusType');?>
    <fieldset>
        <legend>Edit Bonus Type</legend>
    <?php
        echo $form->input('id');
        echo $form->input('name');
    ?>
    </fieldset>
<?= $form->end('Submit');?>
