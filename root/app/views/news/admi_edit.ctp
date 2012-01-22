<?php echo $form->create('News');?>
    <fieldset>
        <legend>Edit News</legend>
    <?php
        echo $form->input('id');
        echo $form->input('title');
        echo $form->input('content', array('style' => 'width: 600px; height: 500px'));
        echo $form->input('date_posted');
        echo $form->input('user_id');
    ?>
    </fieldset>
<?php echo $form->end('Submit');?>
