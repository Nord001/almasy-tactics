<h2>Bonus Types</h2>

<?= $paginator->counter("%count% bonus types in database."); ?>

<table class = 'data'>
<tr>
    <th><?= $paginator->sort('name');?></th>
    <th class = "actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($bonusTypes as $bonusType):
    $class = null;
    if ($i++ % 2 == 0) {
        $class = ' class="altrow"';
    }
?>
    <tr<?= $class;?>>
        <td>
            <?= $bonusType['BonusType']['name']; ?>
        </td>
        <td class = "actions">
            <?= $html->link('View', array('action' => 'view', $bonusType['BonusType']['id'])); ?>
            <?= $html->link('Edit', array('action' => 'edit', $bonusType['BonusType']['id'])); ?>
            <?= $html->link('Delete', array('action' => 'delete', $bonusType['BonusType']['id']), null, 'Are you sure you want to delete this bonus type?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>
