<?= $form->create('CElement', array('action' => 'edit'));?>
    <fieldset>
        <legend>Edit Element - <?= Inflector::humanize($this->data['CElement']['name']); ?></legend>
        <?
            echo $form->input('id');
            echo $form->input('fire', array('label' => '% On Fire'));
            echo $form->input('steel', array('label' => '% On Steel'));
            echo $form->input('wood', array('label' => '% On Wood'));
            echo $form->input('earth', array('label' => '% On Earth'));
            echo $form->input('water', array('label' => '% On Water'));
        ?>

    </fieldset>
<?= $form->end('Submit');?>