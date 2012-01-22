<h2><?= $monster['Monster']['name']; ?></h2>

<div style = 'width: 48%; float: left'>
    <table class = 'view-table'>
        <tr>
            <td class = 'column-1' />
            <td class = 'column-2' />
        </tr>
        <tr>
            <td>Name</td>
            <td><?= $monster['Monster']['name']; ?></td>
        </tr>
        <tr>
            <td>Description</td>
            <td><?= $monster['Monster']['description']; ?></td>
        </tr>

        <!-- Stats -->
        <tr>
            <th>Stats</th>
        </tr>
        <tr>
            <td>Min Range</td>
            <td><?= $monster['Monster']['min_range']; ?></td>
        </tr>
        <tr>
            <td>Max Range</td>
            <td><?= $monster['Monster']['max_range']; ?></td>
        </tr>
        <tr>
            <td>Speed</td>
            <td><?= $monster['Monster']['speed']; ?></td>
        </tr>
        <tr>
            <td>Melee Attack Stat</td>
            <td><?= up($monster['Monster']['melee_atk_stat']); ?></td>
        </tr>
        <tr>
            <td>Ranged Attack Stat</td>
            <td><?= up($monster['Monster']['ranged_atk_stat']); ?></td>
        </tr>

        <!-- Graphics -->
        <tr>
            <th>Graphics</th>
        </tr>
        <tr>
            <td>Battle Icon</td>
            <td>
                <?
                    if ($monster['Monster']['battle_icon'])
                        echo $html->image('sprites/' . $monster['Monster']['battle_icon'] . '.png');
                    else
                        echo 'None';
                ?>
            </td>
        </tr>
        <tr>
            <td>Face Icon</td>
            <td>
                <?
                    if ($monster['Monster']['face_icon'])
                        echo $html->image('sprites/' . $monster['Monster']['face_icon'] . '.png');
                    else
                        echo 'None';
                ?>
            </td>
        </tr>
    </table>

    <div class ="actions">
        <ul>
            <li><?= $html->link('Edit Monster', array('action' => 'edit', $monster['Monster']['id'])); ?> </li>
            <li><?= $html->link('Delete Monster', array('action' => 'delete', $monster['Monster']['id']), null, 'Are you sure you want to delete this monster?'); ?> </li>
        </ul>
    </div>
</div>

<div style = 'width: 48%; float: right'>
    <h3>Bonus: <?= $monster['Monster']['bonus_name']; ?></h3>

    Description: <br />
    <?= str_replace('<name>', '&lt;name&gt;', $monster['Monster']['bonus_description']); ?>

    <br />
    <br />

    <div style = 'width: 100px'>
        <? $ui->locationGrid($monster['Monster']['bonus_locations']); ?>
    </div>

    <ul>
        <? foreach($monster['Bonus'] as $bonus): ?>
            <li>
                <?
                    $durationStr = $bonus['duration'] != '' ? sprintf(' for %s rounds', $bonus['duration']) : '';
                    $bonusName = $bonus['BonusType']['name'];
                    if ($bonusName{0} != '%')
                        $bonusName = ' ' . $bonusName;

                    printf("%s: %+d%s%s (%s) (%s)",
                        implode(',', $bonus['locations']),
                        $bonus['amount'],
                        $bonusName,
                        $durationStr,
                        $html->link('Edit', array('controller' => 'bonuses', 'action' => 'edit', $bonus['id'])),
                        $html->link('Delete', array('controller' => 'bonuses', 'action' => 'delete', $bonus['id']), null, 'Are you sure you want to delete this bonus?')
                    );
                ?>
            </li>
        <? endforeach; ?>
        <li><?= $html->link('New Bonus', array('controller' => 'bonuses', 'action' => 'add', $monster['Monster']['id'])); ?></li>
    </ul>
</div>

<div style = 'clear: both;'></div>