<?= $form->create('UserItem');?>
    <fieldset>
        <legend>Edit Item</legend>

        <?php
            echo $form->input('id');
            echo $form->input('item_id');
            echo $form->input('name');
            echo $form->input('refine');
            echo $form->input('rarity');
        ?>
    </fieldset>
<?= $form->end('Submit');?>
