<?php echo $form->create('User', array('url' => $this->here));?>
    <fieldset>
        <legend>Give Stackable To User</legend>
    <?php
        echo $form->input('item_id', array('label' => 'Item Id'));
        echo $form->input('quantity', array('label' => 'Qty'));
    ?>
    </fieldset>
<?php echo $form->end('Submit');?>
