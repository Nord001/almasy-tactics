<?= $form->create('File', array('url' => $html->url(array('controller' => 'sprites', 'action' => 'add')), 'type' => 'file')); ?>
    <fieldset>
        <legend>Upload Sprite</legend>

    <?= $form->file('file'); ?>
    </fieldset>
<?= $form->end('Upload'); ?>

