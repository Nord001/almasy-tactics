<h2>Weapon Types</h2>

<?= $paginator->counter("%count% weapon types in database."); ?>

<table class = 'data'>
<tr>
    <th><?= $paginator->sort('name');?></th>
    <th><?= $paginator->sort('attack_type');?></th>
    <th><?= $paginator->sort('sprite'); ?>
    <th class = "actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($weaponTypes as $weaponType):
    $class = null;
    if ($i++ % 2 == 0) {
        $class = ' class="altrow"';
    }
?>
    <tr<?= $class;?>>
        <td>
            <?= $weaponType['WeaponType']['name']; ?>
        </td>
        <td>
            <?= Inflector::humanize($weaponType['WeaponType']['attack_type']); ?>
        </td>
        <td>
            <?= $html->image('sprites/' . $weaponType['WeaponType']['sprite'] . '.png', array('alt' => $weaponType['WeaponType']['sprite'])); ?>
        </td>
        <td class = "actions">
            <?= $html->link('View', array('action' => 'view', $weaponType['WeaponType']['id'])); ?>
            <?= $html->link('Edit', array('action' => 'edit', $weaponType['WeaponType']['id'])); ?>
            <?= $html->link('Delete', array('action' => 'delete', $weaponType['WeaponType']['id']), null, 'Are you sure you want to delete this weapon type?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>