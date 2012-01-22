<?= $form->create('BonusType');?>
    <fieldset>
        <legend>Add Bonus Type</legend>
    <?php
        echo $form->input('name');
    ?>
    </fieldset>
<?= $form->end('Submit');?>
