<?php echo $form->create('Experience', array('url' => '/admin/experience/edit/'));?>
    <fieldset>
        <legend>Edit Experience Level</legend>
        <?
            echo $form->input('id');
            echo $form->input('value', array('label' => 'Experience to Next Level'));
        ?>

    </fieldset>
<?php echo $form->end('Submit');?>