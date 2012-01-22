<?= $form->create('ImbueMod');?>
    <fieldset>
        <legend>Add Imbue Mod</legend>
    <?php
        if (isset($imbue_id))
            echo $form->hidden('imbue_id', array('value' => $imbue_id));

        echo $form->input('bonus_type_id');
        echo $form->input('min_amount');
        echo $form->input('max_amount');
        echo $form->input('min_duration', array('value' => 0));
        echo $form->input('max_duration', array('value' => 0));

        if (!isset($imbue_id)) { // If random mod, ask for item type
            echo $form->input('weight', array('value' => '1.0'));
            echo $form->input('item_type');
        }
    ?>
    </fieldset>
<?= $form->end('Submit');?>
