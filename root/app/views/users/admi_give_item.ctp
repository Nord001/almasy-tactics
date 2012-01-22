<?php echo $form->create('User', array('action' => 'give_item'));?>
    <fieldset>
        <legend>Dupe Item For User</legend>
    <?php
        echo $form->hidden('id', array('value' => $userId));
        echo $form->input('user_item_id', array('label' => 'User Item Id'));
    ?>
    </fieldset>
<?php echo $form->end('Submit');?>
