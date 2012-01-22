<h1>Missions</h1>

<form>
    <input type = 'text' id = 'MissionSearchInput' />
    <input type = 'submit' style = 'width: auto; height: auto; font-size: 10pt;' value = 'Quick Find' id = 'MissionSearchButton' />
</form>

<script type = 'text/javascript'>
    var url = '<?= $html->url(array('controller' => 'c_missions', 'action' => 'view')); ?>';
    $(document).ready(function() {
        $('#MissionSearchButton').click(function(event) {
            event.preventDefault();
            window.location = url + '/' + $('#MissionSearchInput').val();
        });

        $('#MissionSearchInput').autocomplete({
            source: <?= json_encode($missionNames); ?>,
            minLength: 2
        });
    });
</script>

<?= $paginator->counter("%count% missions in database."); ?>

<table class = 'data'>
<tr class = 'header'>
    <th><?= $paginator->sort('id'); ?></th>
    <th><?= $paginator->sort('name'); ?></th>
    <th><?= $paginator->sort('prereqs'); ?></th>
    <th><?= $paginator->sort('restrictions'); ?></th>
    <th>Rewards</th>
    <th class = 'actions'>Actions</th>
</tr>
<?php
$i = 0;
foreach ($missions as $mission):
    $css = null;
    if ($i++ % 2 == 0) {
        $css = ' class = "altrow"';
    }
?>
    <tr<?= $css;?>>
        <td><?= $mission['Mission']['id']; ?></td>
        <td><?= $html->link($mission['Mission']['name'], array('controller' => 'missions', 'action' => 'view', $mission['Mission']['id'])); ?></td>
        <td><?= $mission['Mission']['prereqs']; ?></td>
        <td><?= $mission['Mission']['restrictions']; ?></td>
        <td>
            <? $rewards = array(); ?>
            <? foreach ($mission['MissionReward'] as $reward): ?>
                <?
                    $chance = $reward['chance'] < 1.0 ? sprintf('(%.2f%%)', $reward['chance'] * 100) : '';
                    $str = '';
                    if ($reward['type'] == 'exp')
                        $str = $reward['value'] . ' exp';
                    else if ($reward['type'] == 'money')
                        $str = $reward['value'] . ' yb';
                    else if ($reward['type'] == 'item')
                        $str = 'item: ' . $reward['value'];
                    else if ($reward['type'] == 'character')
                        $str = 'character: ' . $reward['value'];
                $rewards[] = $str . ($chance != '' ? ' ' . $chance : '');
                ?>
            <? endforeach; ?>
            <?= implode(', ' , $rewards); ?>
        </td>
        <td class = "actions">
            <?= $html->link('View', array('action' => 'view', $mission['Mission']['id'])); ?>
            <?= $html->link('Edit', array('action' => 'edit', $mission['Mission']['id'])); ?>
            <? $html->link('Delete', array('action' => 'delete', $mission['Mission']['id']), null, 'Are you sure you want to delete this mission?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>

<h2>Mission Groups</h2>

<table class = 'data'>
<tr class = 'header'>
    <th>Name</th>
    <th>Description</th>
    <th class = 'actions'>Actions</th>
</tr>
<?php
$i = 0;
foreach ($missionGroups as $missionGroup):
    $css = null;
    if ($i++ % 2 == 0) {
        $css = ' class = "altrow"';
    }
?>
    <tr<?= $css;?>>
        <td><?= $missionGroup['MissionGroup']['name']; ?></td>
        <td><?= $missionGroup['MissionGroup']['description']; ?></td>
        <td class = "actions">
            <?= $html->link('Edit', array('action' => 'edit', $missionGroup['MissionGroup']['id'])); ?>
            <? $html->link('Delete', array('action' => 'delete', $missionGroup['MissionGroup']['id']), null, 'Are you sure you want to delete this mission group?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>
