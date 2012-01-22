<form method = 'POST' action = '/admi/help/edit'>
    <fieldset>
        <legend>Edit File</legend>
    <?php
        echo $form->hidden('file', array('value' => $file));

        echo $form->textarea('data', array('style' => 'width: 900px; height: 600px; font-family: courier new; font-size: 10pt', 'value' => $contents));
    ?>
    </fieldset>
<?php echo $form->end('Submit');?>
