<?php echo $form->create('Character');?>
    <fieldset>
        <legend>Edit Character</legend>

        <?
            echo $form->input('id');
            echo $form->input('name');
            echo $form->input('str');
            echo $form->input('int');
            echo $form->input('vit');
            echo $form->input('luk');
            echo $form->input('class_id');
            echo $form->input('affinity', array(
                'type' => 'select',
                'options' => array(
                    AFFINITY_FIRE => 'Fire',
                    AFFINITY_STEEL => 'Steel',
                    AFFINITY_WOOD => 'Wood',
                    AFFINITY_EARTH => 'Earth',
                    AFFINITY_WATER => 'Water',
                ),
            ));
            echo $form->input('level');
        ?>
    </fieldset>
<?php echo $form->end('Submit');?>