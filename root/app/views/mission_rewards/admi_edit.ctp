<?php echo $form->create('MissionReward');?>
    <fieldset>
        <legend>Edit Mission Reward</legend>

        <?
            echo $form->input('id');
            echo $form->hidden('mission_id');
            echo $form->input('type', array(
                'type' => 'select',
                'options' => array(
                    'character' => 'Character',
                    'exp' => 'EXP',
                    'item' => 'Item',
                    'money' => 'Yuanbao',
                ),
            ));
            echo $form->input('value');
            echo $form->input('chance');
        ?>
    </fieldset>
<?php echo $form->end('Submit');?>