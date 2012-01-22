<?php echo $form->create('MissionReward');?>
    <fieldset>
        <legend>Add Mission Reward</legend>

        <?
            echo $form->hidden('mission_id', array('value' => $missionId));
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