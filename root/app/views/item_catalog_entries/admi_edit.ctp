<?php echo $form->create('ItemCatalogEntry');?>
    <fieldset>
        <legend>Edit Item Catalog Entry</legend>
    <?php
        echo $form->input('id');

        echo $form->checkbox('use_item_value', array('class' => 'inline'));
        echo $form->label('Use Item Value');

        echo $form->input('cost');
    ?>
    </fieldset>
<?php echo $form->end('Submit');?>
