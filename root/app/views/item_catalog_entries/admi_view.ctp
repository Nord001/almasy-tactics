<h2>Selling <?= $itemCatalogEntry['UserItem']['name']; ?></h2>
<table class = 'view-table'>
    <tr>
        <td class = 'column-1' />
        <td class = 'column-2' />
    </tr>
    <tr>
    </tr>
    <tr>
        <td>Item</td>
        <td>
            <?= $html->link($itemCatalogEntry['UserItem']['name'], array('controller' => 'items', 'action' => 'view', $itemCatalogEntry['UserItem']['Item']['id'])); ?>
        </td>
    </tr>
    <tr>
        <td>Cost</td>
        <td><?= $itemCatalogEntry['ItemCatalogEntry']['cost']; ?></td>
    </tr>
    <tr>
        <td>Sales</td>
        <td><?= $itemCatalogEntry['ItemCatalogEntry']['sales']; ?></td>
    </tr>
</table>

<div class = "actions">
    <ul>
        <li><?= $html->link('Edit Item Catalog Entry', array('action' => 'edit', $itemCatalogEntry['ItemCatalogEntry']['id'])); ?> </li>
        <li><?= $html->link('Delete Item Catalog Entry', array('action' => 'delete', $itemCatalogEntry['ItemCatalogEntry']['id']), null, 'Are you sure you want to delete this item catalog entry?'); ?> </li>
    </ul>
</div>