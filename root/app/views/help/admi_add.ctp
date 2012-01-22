<form method = 'POST' action = '/admi/help/add'>
    <fieldset>
        <legend>New File</legend>
    <?php
        echo $form->input('file');
    ?>
    </fieldset>
<?php echo $form->end('Submit');?>
