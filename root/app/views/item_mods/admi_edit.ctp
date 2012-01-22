<?= $form->create('ItemMod');?>
    <fieldset>
        <legend>Edit Item Mod</legend>
    <?php
        echo $form->input('id');
        echo $form->hidden('user_item_id', array('value' => $this->data['ItemMod']['user_item_id']));
        echo $form->input('bonus_type_id');
        echo $form->input('amount');
        echo $form->input('duration');
        echo $form->input('native');
    ?>
    </fieldset>
<?= $form->end('Submit'); ?>