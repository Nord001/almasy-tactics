<h2>Misc Item Types</h2>

<?= $paginator->counter("%count% misc item types in database."); ?>

<table class = 'data'>
<tr>
    <th><?= $paginator->sort('name');?></th>
    <th><?= $paginator->sort('sprite');?></th>
    <th class = "actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($miscItemTypes as $miscItemType):
    $class = null;
    if ($i++ % 2 == 0) {
        $class = ' class="altrow"';
    }
?>
    <tr<?= $class;?>>
        <td>
            <?= $miscItemType['MiscItemType']['name']; ?>
        </td>
        <td>
            <?= $html->image('sprites/' . $miscItemType['MiscItemType']['sprite'] . '.png', array('alt' => $miscItemType['MiscItemType']['sprite'])); ?>
        </td>
        <td class = "actions">
            <?= $html->link('View', array('action' => 'view', $miscItemType['MiscItemType']['id'])); ?>
            <?= $html->link('Edit', array('action' => 'edit', $miscItemType['MiscItemType']['id'])); ?>
            <?= $html->link('Delete', array('action' => 'delete', $miscItemType['MiscItemType']['id']), null, 'Are you sure you want to delete this misc item type?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>