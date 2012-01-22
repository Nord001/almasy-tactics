<?php echo $form->create('ItemCatalogEntry');?>
    <fieldset>
        <legend>Add Item Catalog Entry</legend>
    <?php
        echo $form->input('user_item_id');
        echo $form->input('cost');
    ?>
    </fieldset>
<?php echo $form->end('Submit');?>
