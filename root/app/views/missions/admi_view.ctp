<h2><?= $mission['Mission']['name']; ?></h2>

<div style = 'width: 48%; float: left'>
    <table class = 'view-table'>
        <tr>
            <td class = 'column-1' />
            <td class = 'column-2' />
        </tr>
        <tr>
            <td>Name</td>
            <td><?= $mission['Mission']['name']; ?></td>
        </tr>
        <tr>
            <td>Mission Group</td>
            <td><?= $mission['MissionGroup']['name']; ?></td>
        </tr>
        <tr>
            <td>Description</td>
            <td><?= $mission['Mission']['description']; ?></td>
        </tr>
        <tr>
            <td>Prereq Missions</td>
            <td><?= $mission['Mission']['prereqs']; ?></td>
        </tr>
        <tr>
            <td>Restrictions</td>
            <td><?= $mission['Mission']['restrictions']; ?></td>
        </tr>
        <tr>
            <td>Restrictions Description</td>
            <td><?= $mission['Mission']['restrictions_desc']; ?></td>
        </tr>
        <tr>
            <td>Enemy Formation</td>
            <td><?= $mission['Mission']['enemy_formation_id']; ?></td>
        </tr>
        <tr>
            <td>Active</td>
            <td><?= $mission['Mission']['active'] ? 'Yes' : 'No'; ?></td>
        </tr>
        <tr>
            <td>Completion Count</td>
            <td><?= $mission['Mission']['completion_count']; ?></td>
        </tr>
        <tr>
            <td>Difficulty Variation</td>
            <td><?= $mission['Mission']['difficulty_variation']; ?></td>
        </tr>
        <tr>
            <td>Only Once Per User?</td>
            <td><?= $mission['Mission']['only_once_per_user'] ? 'Yes' : 'No'; ?></td>
        </tr>
        <tr>
            <td>Final Mission?</td>
            <td><?= $mission['Mission']['is_final_mission'] ? 'Yes' : 'No'; ?></td>
        </tr>
    </table>

    <div class="actions">
        <ul>
            <li><?= $html->link('Edit Mission', array('action' => 'edit', $mission['Mission']['id'])); ?> </li>
            <li><?= $html->link('Delete Mission', array('action' => 'delete', $mission['Mission']['id']), null, 'Are you sure you want to delete this mission?'); ?> </li>
        </ul>
    </div>
</div>

<div style = 'width: 48%; float: right'>
    <h3>Rewards</h3>

    <ul>
        <? foreach ($mission['MissionReward'] as $reward): ?>
            <li>
                <?
                    $chance = $reward['chance'] < 1.0 ? sprintf('(%.2f%%)', $reward['chance'] * 100) : '';
                    $str = '';
                    if ($reward['type'] == 'exp')
                        $str = $reward['value'] . ' exp';
                    else if ($reward['type'] == 'money')
                        $str = $reward['value'] . ' yb';
                    else if ($reward['type'] == 'item')
                        $str = 'item: ' . $html->link($reward['value'], array('controller' => 'user_items', 'action' => 'edit', $reward['value']));
                    else if ($reward['type'] == 'character')
                        $str = 'character: ' . $html->link($reward['value'], array('controller' => 'character', 'action' => 'view', $reward['value']));
                ?>
                <?= $str; ?> <?= $chance; ?>
                (<?= $html->link('Edit', array('controller' => 'mission_rewards', 'action' => 'edit', $reward['id'])); ?>)
                (<?= $html->link('Delete', array('controller' => 'mission_rewards', 'action' => 'delete', $reward['id'])); ?>)
        <? endforeach; ?>
        <li><?= $html->link('New Reward', array('controller' => 'mission_rewards', 'action' => 'add', $mission['Mission']['id'])); ?></li>
    </ul>
</div>

<div style = 'clear: both;'></div>