<h2><?= $miscItemType['MiscItemType']['name']; ?></h2>
<table class = 'view-table'>
    <tr>
        <td class = 'column-1' />
        <td class = 'column-2' />
    </tr>
    <tr>
    </tr>
    <tr>
        <td>Sprite</td>
        <td><?= $html->image('sprites/' . $miscItemType['MiscItemType']['sprite'] . '.png', array('alt' => $miscItemType['MiscItemType']['sprite'])); ?></td>
    </tr>
</table>

<div class = "actions">
    <ul>
        <li><?= $html->link('Edit MiscItem Type', array('action' => 'edit', $miscItemType['MiscItemType']['id'])); ?> </li>
        <li><?= $html->link('Delete MiscItem Type', array('action' => 'delete', $miscItemType['MiscItemType']['id']), null, 'Are you sure you want to delete this miscItem type?'); ?> </li>
    </ul>
</div>