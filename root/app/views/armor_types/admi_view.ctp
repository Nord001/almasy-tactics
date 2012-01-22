<h2><?= $armorType['ArmorType']['name']; ?></h2>
<table class = 'view-table'>
    <tr>
        <td class = 'column-1' />
        <td class = 'column-2' />
    </tr>
    <tr>
    </tr>
    <tr>
        <td>Speed Mod</td>
        <td><? printf("%+d", $armorType['ArmorType']['speed_mod']); ?></td>
    </tr>
    <tr>
        <td>Sprite</td>
        <td><?= $html->image('sprites/' . $armorType['ArmorType']['sprite'] . '.png', array('alt' => $armorType['ArmorType']['sprite'])); ?></td>
    </tr>
</table>

<div class = "actions">
    <ul>
        <li><?= $html->link('Edit Armor Type', array('action' => 'edit', $armorType['ArmorType']['id'])); ?> </li>
        <li><?= $html->link('Delete Armor Type', array('action' => 'delete', $armorType['ArmorType']['id']), null, 'Are you sure you want to delete this armor type?'); ?> </li>
    </ul>
</div>