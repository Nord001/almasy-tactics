<h2>Store</h2>

<?= $paginator->counter("%count% items in store."); ?>

<table class = 'data'>
<tr>
    <th><?= $paginator->sort('item_id');?></th>
    <th><?= $paginator->sort('cost');?></th>
    <th><?= $paginator->sort('sales');?></th>
    <th class = "actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($itemCatalogEntries as $itemCatalogEntry):
    $class = null;
    if ($i++ % 2 == 0) {
        $class = ' class="altrow"';
    }
?>
    <tr<?= $class;?>>
        <td>
            <?= $html->link($itemCatalogEntry['UserItem']['name'], array('controller' => 'items', 'action' => 'view', $itemCatalogEntry['UserItem']['Item']['id'])); ?>
        </td>
        <td>
            <?= $itemCatalogEntry['ItemCatalogEntry']['cost']; ?>
        </td>
        <td>
            <?= $itemCatalogEntry['ItemCatalogEntry']['sales']; ?>
        </td>
        <td class = 'actions'>
            <? $html->link('View', array('action' => 'view', $itemCatalogEntry['ItemCatalogEntry']['id'])); ?>
            <?= $html->link('Edit', array('action' => 'edit', $itemCatalogEntry['ItemCatalogEntry']['id'])); ?>
            <?= $html->link('Delete', array('action' => 'delete', $itemCatalogEntry['ItemCatalogEntry']['id']), null, 'Are you sure you want to delete this item catalog entry?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>