<h2>User Item: <?= $item['UserItem']['name']; ?></h2>

<div style = 'width: 48%; float: left'>
    <table class = 'view-table'>
        <tr>
            <td class = 'column-1' />
            <td class = 'column-2' />
        </tr>
        <tr>
            <td>User</td>
            <td>
                <?= $html->link($item['User']['username'], array('controller' => 'users', 'action' => 'view', $item['User']['id'])); ?>
            </td>
        </tr>
        <tr>
            <td>Item</td>
            <td>
                <?= $html->link($item['Item']['name'], array('controller' => 'items', 'action' => 'view', $item['Item']['id'])); ?>
            </td>
        </tr>
        <tr>
            <td>Refine</td>
            <td><?= $item['UserItem']['refine']; ?></td>
        </tr>
        <tr>
            <td>Rarity</td>
            <td><?= Inflector::humanize($item['UserItem']['rarity']); ?></td>
        </tr>
    </table>

    <div class = "actions">
        <ul>
            <li><?= $html->link('Edit Item', array('action' => 'edit', $item['UserItem']['id'])); ?> </li>
            <li><?= $html->link('Delete Item', array('action' => 'delete', $item['UserItem']['id']), null, 'Are you sure you want to delete this item?'); ?> </li>
        </ul>
    </div>
</div>

<div style = 'width: 48%; float: right'>
    <h3>Mods</h3>
    <ul>
        <? foreach($item['ItemMod'] as $mod): ?>
            <li>
                <?
                    $durationStr = $mod['duration'] != '' ? sprintf(' for %s rounds', $mod['duration']) : '';
                    $modName = $mod['BonusType']['name'];
                    if ($modName{0} != '%')
                        $modName = ' ' . $modName;

                    printf("%+d%s%s (%s) (%s) (%s)",
                        $mod['amount'],
                        $modName,
                        $durationStr,
                        $mod['native'] ? 'Native' : 'Not Native',
                        $html->link('Edit', array('controller' => 'item_mods', 'action' => 'edit', $mod['id'])),
                        $html->link('Delete', array('controller' => 'item_mods', 'action' => 'delete', $mod['id']), null, 'Are you sure you want to delete this mod?')
                    );
                ?>
            </li>
        <? endforeach; ?>
        <li><?= $html->link('New Mod', array('controller' => 'item_mods', 'action' => 'add', $item['UserItem']['id'])); ?></li>
    </ul>
</div>

<div style = 'clear: both;'></div>