<?= $form->create('Item');?>
    <fieldset>
        <legend>Add Item</legend>

        <?php
            echo $form->input('name');
            echo $form->input('req_lvl', array('value' => 1));
            echo $form->input('value');
            echo $form->input('sprite');
            echo $form->input('weapon_type_id', array(
                'empty' => '--- Armor/Misc ---',
            ));
            echo $form->input('armor_type_id', array(
                'empty' => '--- Weapon/Misc ---',
            ));
            echo $form->input('misc_item_type_id', array(
                'empty' => '--- Equip ---',
            ));
            echo $form->input('description');
        ?>

        <fieldset style = 'margin-top: 10px'>
            <legend>Weapon Data</legend>

            <?php
                echo $form->input('attack');
                echo $form->input('strikes');
                echo $form->input('critical');
            ?>
        </fieldset>

        <fieldset>
            <legend>Armor Data</legend>

            <?php
                echo $form->input('phys_reduction');
                echo $form->input('phys_defense');
                echo $form->input('mag_reduction');
                echo $form->input('mag_defense');
            ?>
        </fieldset>
    </fieldset>
<?= $form->end('Submit');?>
