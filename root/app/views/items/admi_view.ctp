<h2><?= $item['Item']['name']; ?></h2>

<table class = 'view-table'>
    <tr>
        <td class = 'column-1' />
        <td class = 'column-2' />
    </tr>
    <tr>
        <td>Req Lvl</td>
        <td><?= $item['Item']['req_lvl']; ?></td>
    </tr>
    <tr>
        <td>Value</td>
        <td><?= $item['Item']['value']; ?></td>
    </tr>
    <tr>
        <td>Sprite</td>
        <td><?= $item['Item']['sprite'] ? $html->image('sprites/' . $item['Item']['sprite'] . '.png', array('alt' => 'None')) : 'None'; ?></td>
    </tr>
    <tr>
        <td>Description</td>
        <td><?= $item['Item']['description']; ?></td>
    </tr>

    <!-- Weapon Data -->
    <? if ($item['WeaponType']['id'] != ''): ?>
        <tr>
            <th>Weapon Data</th>
        </tr>
        <tr>
            <td>Weapon Type</td>
            <td>
                <?= $item['WeaponType']['id'] != '' ? $html->link($item['WeaponType']['name'], array('controller' => 'weapon_types', 'action' => 'view', $item['WeaponType']['id'])) : 'Armor'; ?>
            </td>
        </tr>
        <tr>
            <td>Attack</td>
            <td><?= $item['Item']['attack']; ?></td>
        </tr>
        <tr>
            <td>Strikes</td>
            <td><?= $item['Item']['strikes']; ?></td>
        </tr>
        <tr>
            <td>Critical</td>
            <td><?= $item['Item']['critical']; ?></td>
        </tr>
    <? endif; ?>

    <!-- Armor Data -->
    <? if ($item['ArmorType']['id'] != ''): ?>
        <tr>
            <th>Armor Data</th>
        </tr>
        <tr>
            <td>Armor Type</td>
            <td>
                <?= $html->link($item['ArmorType']['name'], array('controller' => 'armor_types', 'action' => 'view', $item['ArmorType']['id'])); ?>
            </td>
        </tr>
        <tr>
            <td>Physical Reduction</td>
            <td><?= $item['Item']['phys_reduction']; ?></td>
        </tr>
        <tr>
            <td>Physical Defense</td>
            <td><?= $item['Item']['phys_defense']; ?></td>
        </tr>
        <tr>
            <td>Magic Reduction</td>
            <td><?= $item['Item']['mag_reduction']; ?></td>
        </tr>
        <tr>
            <td>Magic Defense</td>
            <td><?= $item['Item']['mag_defense']; ?></td>
        </tr>
    <? endif; ?>
</table>

<div class = "actions">
    <ul>
        <li><?= $html->link('Edit Item', array('action' => 'edit', $item['Item']['id'])); ?> </li>
        <li><?= $html->link('Delete Item', array('action' => 'delete', $item['Item']['id']), null, 'Are you sure you want to delete this item?'); ?> </li>
    </ul>
</div>