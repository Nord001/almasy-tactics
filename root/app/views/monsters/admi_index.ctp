<h1>Monsters</h1>

<form>
    <input type = 'text' id = 'MonsterSearchInput' />
    <input type = 'submit' style = 'width: auto; height: auto; font-size: 10pt;' value = 'Quick Find' id = 'MonsterSearchButton' />
</form>

<script type = 'text/javascript'>
    var url = '<?= $html->url(array('controller' => 'c_monsters', 'action' => 'view')); ?>';
    $(document).ready(function() {
        $('#MonsterSearchButton').click(function(event) {
            event.preventDefault();
            window.location = url + '/' + $('#MonsterSearchInput').val();
        });

        $('#MonsterSearchInput').autocomplete({
            source: <?= json_encode($monsterNames); ?>,
            minLength: 2
        });
    });
</script>

<?= $paginator->counter("%count% monsters in database."); ?>

<table class = 'data'>
<tr class = 'header'>
    <th><?= $paginator->sort('name'); ?></th>
    <th><?= $paginator->sort('Range', 'max_range'); ?></th>
    <th><?= $paginator->sort('Speed', 'speed'); ?></th>
    <th><?= $paginator->sort('Bonus', 'bonus_name'); ?></th>
    <th class = 'actions'>Actions</th>
</tr>
<?php
$i = 0;
foreach ($monsters as $monster):
    $css = null;
    if ($i++ % 2 == 0) {
        $css = ' class = "altrow"';
    }
?>
    <tr<?= $css;?>>
        <td><?= $html->link($monster['Monster']['name'], array('controller' => 'monsters', 'action' => 'view', $monster['Monster']['id'])); ?></td>
        <td>
            <?= $monster['Monster']['min_range']; ?>-<?= $monster['Monster']['max_range']; ?>
        </td>
        <td><?= $monster['Monster']['speed']; ?></td>
        <td><?= $monster['Monster']['bonus_name'] ? $monster['Monster']['bonus_name'] : ''; ?></td>
        <td class = "actions">
            <?= $html->link('View', array('action' => 'view', $monster['Monster']['id'])); ?>
            <?= $html->link('Edit', array('action' => 'edit', $monster['Monster']['id'])); ?>
            <? $html->link('Delete', array('action' => 'delete', $monster['Monster']['id']), null, 'Are you sure you want to delete this monster?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>
