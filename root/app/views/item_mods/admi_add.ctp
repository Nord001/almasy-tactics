<?= $form->create('ItemMod');?>
    <fieldset>
        <legend>Add Item Mod</legend>
    <?php
        echo $form->hidden('user_item_id', array('value' => $userItemId));
        echo $form->input('bonus_type_id');
        echo $form->input('amount');
        echo $form->input('duration');
        echo $form->input('native');
    ?>
    </fieldset>
<?= $form->end('Submit');?>
