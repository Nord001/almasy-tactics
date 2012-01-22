<h2>Armor Types</h2>

<?= $paginator->counter("%count% armor types in database."); ?>

<table class = 'data'>
<tr>
    <th><?= $paginator->sort('name');?></th>
    <th><?= $paginator->sort('speed_mod');?></th>
    <th><?= $paginator->sort('sprite');?></th>
    <th class = "actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($armorTypes as $armorType):
    $class = null;
    if ($i++ % 2 == 0) {
        $class = ' class="altrow"';
    }
?>
    <tr<?= $class;?>>
        <td>
            <?= $armorType['ArmorType']['name']; ?>
        </td>
        <td>
            <? printf("%+d", $armorType['ArmorType']['speed_mod']); ?>
        </td>
        <td>
            <?= $html->image('sprites/' . $armorType['ArmorType']['sprite'] . '.png', array('alt' => $armorType['ArmorType']['sprite'])); ?>
        </td>
        <td class = "actions">
            <?= $html->link('View', array('action' => 'view', $armorType['ArmorType']['id'])); ?>
            <?= $html->link('Edit', array('action' => 'edit', $armorType['ArmorType']['id'])); ?>
            <?= $html->link('Delete', array('action' => 'delete', $armorType['ArmorType']['id']), null, 'Are you sure you want to delete this armor type?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>