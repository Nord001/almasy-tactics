<h2>Imbue Mod Pool</h2>

<table class = 'data'>
<tr>
    <th><?= $paginator->sort('bonus_type_id');?></th>
    <th><?= $paginator->sort('min_amount'); ?></th>
    <th><?= $paginator->sort('max_amount'); ?></th>
    <th><?= $paginator->sort('min_duration'); ?></th>
    <th><?= $paginator->sort('max_duration'); ?></th>
    <th><?= $paginator->sort('weight'); ?></th>
    <th><?= $paginator->sort('item_type'); ?></th>
    <th class = "actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($imbueMods as $imbueMod):
    $class = null;
    if ($i++ % 2 == 0) {
        $class = ' class="altrow"';
    }
?>
    <tr<?= $class;?>>
        <td>
            <?= $imbueMod['BonusType']['name']; ?>
        </td>
        <td>
            <?= $imbueMod['ImbueMod']['min_amount']; ?>
        </td>
        <td>
            <?= $imbueMod['ImbueMod']['max_amount']; ?>
        </td>
        <td>
            <?= $imbueMod['ImbueMod']['min_duration']; ?>
        </td>
        <td>
            <?= $imbueMod['ImbueMod']['max_duration']; ?>
        </td>
        <td>
            <?= $imbueMod['ImbueMod']['weight']; ?>
        </td>
        <td>
            <?= Inflector::humanize($imbueMod['ImbueMod']['item_type']); ?>
        </td>
        <td class = "actions">
            <?= $html->link('Edit', array('action' => 'edit', $imbueMod['ImbueMod']['id'])); ?>
            <?= $html->link('Delete', array('action' => 'delete', $imbueMod['ImbueMod']['id']), null, 'Are you sure you want to delete this imbue mod?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>