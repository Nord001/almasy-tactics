<h2><?= $class['CClass']['name']; ?></h2>

<div style = 'width: 48%; float: left'>
    <table class = 'view-table'>
        <tr>
            <td class = 'column-1' />
            <td class = 'column-2' />
        </tr>
        <tr>
            <td>Name</td>
            <td><?= $class['CClass']['name']; ?></td>
        </tr>
        <tr>
            <td>Description</td>
            <td><?= $class['CClass']['description']; ?></td>
        </tr>

        <!-- Stats -->
        <tr>
            <th>Stats</th>
        </tr>
        <tr>
            <td>+STR</td>
            <td><?= $class['CClass']['growth_str']; ?></td>
        </tr>
        <tr>
            <td>+INT</td>
            <td><?= $class['CClass']['growth_int']; ?></td>
        </tr>
        <tr>
            <td>+VIT</td>
            <td><?= $class['CClass']['growth_vit']; ?></td>
        </tr>
        <tr>
            <td>+LUK</td>
            <td><?= $class['CClass']['growth_luk']; ?></td>
        </tr>
        <tr>
            <td>Min Range</td>
            <td><?= $class['CClass']['min_range']; ?></td>
        </tr>
        <tr>
            <td>Max Range</td>
            <td><?= $class['CClass']['max_range']; ?></td>
        </tr>
        <tr>
            <td>Speed</td>
            <td><?= $class['CClass']['speed']; ?></td>
        </tr>
        <tr>
            <td>Melee Attack Stat</td>
            <td><?= up($class['CClass']['melee_atk_stat']); ?></td>
        </tr>
        <tr>
            <td>Ranged Attack Stat</td>
            <td><?= up($class['CClass']['ranged_atk_stat']); ?></td>
        </tr>
        <tr>
            <td>Uses: </td>
            <td><?= empty($class['CClass']['weapon_use']) ? 'Nothing' : implode(", ", $class['CClass']['weapon_use']); ?></td>
        </tr>


        <!-- Promotion -->
        <tr>
            <th>Promotion</th>
        </tr>
        <tr>
            <td>Promote Class 1</td>
            <td>
                <?
                    if ($class['CClass']['promote_class_1_id']) {
                        echo $html->link($class['CClass1']['name'], array('controller' => 'c_classes', 'action' => 'view', $class['CClass1']['id']));
                        echo sprintf(' (Level %s)', $class['CClass']['promote_class_1_level']);
                    } else {
                        echo 'None';
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td>Promote Class 2</td>
            <td>
                <?
                    if ($class['CClass']['promote_class_2_id']) {
                        echo $html->link($class['CClass2']['name'], array('controller' => 'c_classes', 'action' => 'view', $class['CClass2']['id']));
                        echo sprintf(' (Level %s)', $class['CClass']['promote_class_2_level']);
                    } else {
                        echo 'None';
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td>Promote Class 3</td>
            <td>
                <?
                    if ($class['CClass']['promote_class_3_id']) {
                        echo $html->link($class['CClass3']['name'], array('controller' => 'c_classes', 'action' => 'view', $class['CClass3']['id']));
                        echo sprintf(' (Level %s)', $class['CClass']['promote_class_3_level']);
                    } else {
                        echo 'None';
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td>Promote Class 4</td>
            <td>
                <?
                    if ($class['CClass']['promote_class_4_id']) {
                        echo $html->link($class['CClass4']['name'], array('controller' => 'c_classes', 'action' => 'view', $class['CClass4']['id']));
                        echo sprintf(' (Level %s)', $class['CClass']['promote_class_4_level']);
                    } else {
                        echo 'None';
                    }
                ?>
            </td>
        </tr>

        <!-- Graphics -->
        <tr>
            <th>Graphics</th>
        </tr>
        <tr>
            <td>Battle Icon</td>
            <td>
                <?
                    if ($class['CClass']['battle_icon'])
                        echo $html->image('sprites/' . $class['CClass']['battle_icon'] . '.png');
                    else
                        echo 'None';
                ?>
            </td>
        </tr>
        <tr>
            <td>Face Icon</td>
            <td>
                <?
                    if ($class['CClass']['face_icon'])
                        echo $html->image('sprites/' . $class['CClass']['face_icon'] . '.png');
                    else
                        echo 'None';
                ?>
            </td>
        </tr>
    </table>

    <div class="actions">
        <ul>
            <li><?= $html->link('Edit Class', array('action' => 'edit', $class['CClass']['id'])); ?> </li>
            <li><?= $html->link('Delete Class', array('action' => 'delete', $class['CClass']['id']), null, 'Are you sure you want to delete this class?'); ?> </li>
        </ul>
    </div>
</div>

<div style = 'width: 48%; float: right'>
    <h3>Bonus: <?= $class['CClass']['bonus_name']; ?></h3>

    Description: <br />
    <?= str_replace('<name>', '&lt;name&gt;', $class['CClass']['bonus_description']); ?>

    <br />
    <br />

    <div style = 'width: 100px'>
        <? $ui->locationGrid($class['CClass']['bonus_locations']); ?>
    </div>

    <ul>
        <? foreach($class['Bonus'] as $bonus): ?>
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
        <li><?= $html->link('New Bonus', array('controller' => 'bonuses', 'action' => 'add', $class['CClass']['id'])); ?></li>
    </ul>
</div>

<div style = 'clear: both;'></div>