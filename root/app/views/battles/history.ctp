<style type = 'text/css'>
    .AltRow td {
        background-color: rgb(240, 235, 220);
    }

    .PageContent td {
        padding: 10px;
    }

    td.Victory {
        background-color: rgb(0, 200, 0);
    }

    td.Loss {
        background-color: rgb(200, 0, 0);
    }

</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('War Room', array('controller' => 'battles', 'action' => 'index')); ?> |
        Battle History
    </div>

    <div class = 'PageContent'>
        <div style = 'position: relative; margin-left: -400px; left: 50%; width: 800px; border: 1px solid;' class = 'rounded-corners'>
            <table style = 'width: 100%'>
                <? $i = 0; ?>
                <? foreach ($battleHistory as $battle): ?>
                    <tr class = '<?= $i++ % 2 == 0 ? 'AltRow' : ''; ?>'>
                        <td>
                            <? $link = sprintf('%s (%s) vs. %s (%s)',
                                $battle['Battle']['attacker_formation_name'],
                                $battle['Battle']['AttackingUser']['username'],
                                $battle['Battle']['defender_formation_name'],
                                $battle['Battle']['DefendingUser']['username']
                                );

                                echo $html->link2($link, array('controller' => 'battles', 'action' => 'fight_result', $battle['Battle']['id']));
                            ?>
                        </td>
                        <td>
                            <?= Inflector::camelize($battle['Battle']['battle_type']); ?>,
                            <?= $time->GetTimeAgoString(strtotime($battle['Battle']['time'])); ?>
                        </td>
                        <?
                            $result = '';
                            if ($battle['Battle']['attacker_user_id'] == $a_user['User']['id'] && $battle['Battle']['victor'] == 'attacker')
                                $result = 'Victory';
                            else if ($battle['Battle']['defender_user_id'] == $a_user['User']['id'] && $battle['Battle']['victor'] == 'defender')
                                $result = 'Victory';
                            else
                                $result = 'Loss';
                        ?>
                        <td style = 'text-align: right; border-left: 1px dashed; width: 60px; font-weight: bold;' class = '<?= $result; ?>'>
                            <?= $result; ?>
                        </td>
                    </tr>
                <? endforeach; ?>
            </table>
        </div>
    </div>
</div>
