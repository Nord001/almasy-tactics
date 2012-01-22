<?= $form->create('UserItem');?>
    <fieldset>
        <legend>Give Item to User</legend>

        <?php
            echo $form->hidden('user_id', array('value' => $user_id));

            echo $form->input('item_id');
            echo $form->input('name');
            echo $form->input('refine', array('value' => 0));
            echo $form->input('rarity');
        ?>
    </fieldset>
<?= $form->end('Submit');?>
