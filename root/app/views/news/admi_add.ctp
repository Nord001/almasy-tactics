<?php echo $form->create('News');?>
    <fieldset>
        <legend>Post News</legend>
    <?php
        echo $form->input('title', array('style' => 'width: 600px'));
        echo $form->input('content', array('style' => 'width: 600px; height: 500px'));
    ?>
    </fieldset>
<?php echo $form->end('Submit');?>
