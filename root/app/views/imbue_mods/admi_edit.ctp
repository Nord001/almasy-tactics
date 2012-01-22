<?= $form->create('ImbueMod');?>
    <fieldset>
        <legend>Edit Imbue Mod</legend>
    <?php
        echo $form->input('id');
        echo $form->hidden('imbue_id', array('value' => $this->data['ImbueMod']['imbue_id']));
        echo $form->input('bonus_type_id');
        echo $form->input('min_amount');
        echo $form->input('max_amount');
        echo $form->input('min_duration');
        echo $form->input('max_duration');

        if ($this->data['ImbueMod']['imbue_id'] == '') {
            echo $form->input('weight');
            echo $form->input('item_type');
        }
    ?>
    </fieldset>
<?= $form->end('Submit'); ?>