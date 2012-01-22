<h2>Items</h2>

<?= $paginator->counter("%count% items in database."); ?>

<table class = 'data'>
<tr>
    <th><?= $paginator->sort('id');?></th>
    <th><?= $paginator->sort('name');?></th>
    <th><?= $paginator->sort('attack');?></th>
    <th><?= $paginator->sort('req_lvl');?></th>
    <th><?= $paginator->sort('sprite');?></th>
    <th><?= $paginator->sort('weapon_type_id');?></th>
    <th><?= $paginator->sort('strikes');?></th>
    <th><?= $paginator->sort('critical');?></th>
    <th><?= $paginator->sort('Phys. R.', 'phys_reduction');?></th>
    <th><?= $paginator->sort('Phys. D.', 'phys_defense');?></th>
    <th><?= $paginator->sort('Mag. R.', 'mag_reduction');?></th>
    <th><?= $paginator->sort('Mag. D.', 'mag_defense');?></th>
    <th><?= $paginator->sort('armor_type_id');?></th>
    <th class = "actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($items as $item):
    $class = null;
    if ($i++ % 2 == 0) {
        $class = ' class="altrow"';
    }
?>
    <tr<?= $class;?>>
        <td>
            <?= $item['Item']['id']; ?>
        </td>
        <td>
            <?= $item['Item']['name']; ?>
        </td>
        <td>
            <?= $item['Item']['attack']; ?>
        </td>
        <td>
            <?= $item['Item']['req_lvl']; ?>
        </td>
        <td>
            <?= $item['Item']['sprite']; ?>
        </td>
        <td>
            <?= $item['WeaponType']['name']; ?>
        </td>
        <td>
            <?= $item['Item']['strikes']; ?>
        </td>
        <td>
            <?= $item['Item']['critical']; ?>
        </td>
        <td>
            <?= $item['Item']['phys_reduction']; ?>
        </td>
        <td>
            <?= $item['Item']['phys_defense']; ?>
        </td>
        <td>
            <?= $item['Item']['mag_reduction']; ?>
        </td>
        <td>
            <?= $item['Item']['mag_defense']; ?>
        </td>
        <td>
            <?= $item['ArmorType']['name']; ?>
        </td>
        <td class = "actions">
            <?= $html->link('View', array('action' => 'view', $item['Item']['id'])); ?>
            <?= $html->link('Edit', array('action' => 'edit', $item['Item']['id'])); ?>
            <? $html->link('Delete', array('action' => 'delete', $item['Item']['id']), null, 'Are you sure you want to delete this item?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>
