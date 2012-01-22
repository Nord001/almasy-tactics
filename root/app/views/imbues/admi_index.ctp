<h2>Imbues</h2>

<?= $paginator->counter("%count% imbues in database."); ?>

<table class = 'data'>
<tr>
    <th><?= $paginator->sort('name');?></th>
    <th><?= $paginator->sort('item_type'); ?></th>
    <th class = "actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($imbues as $imbue):
    $class = null;
    if ($i++ % 2 == 0) {
        $class = ' class="altrow"';
    }
?>
    <tr<?= $class;?>>
        <td>
            <?= $imbue['Imbue']['name']; ?>
        </td>
        <td>
            <?= Inflector::humanize($imbue['Imbue']['item_type']); ?>
        </td>
        <td class = "actions">
            <?= $html->link('View', array('action' => 'view', $imbue['Imbue']['id'])); ?>
            <?= $html->link('Edit', array('action' => 'edit', $imbue['Imbue']['id'])); ?>
            <?= $html->link('Delete', array('action' => 'delete', $imbue['Imbue']['id']), null, 'Are you sure you want to delete this imbue?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>