<h2><?= $weaponType['WeaponType']['name']; ?></h2>
<table class = 'view-table'>
    <tr>
        <td class = 'column-1' />
        <td class = 'column-2' />
    </tr>
    <tr>
    </tr>
    <tr>
        <td>Attack Type</td>
        <td><?= Inflector::humanize($weaponType['WeaponType']['attack_type']); ?></td>
    </tr>
    <tr>
        <td>Sprite</td>
        <td><?= $html->image('sprites/' . $weaponType['WeaponType']['sprite'] . '.png', array('alt' => $weaponType['WeaponType']['sprite'])); ?></td>
    </tr>
</table>

<div class = "actions">
    <ul>
        <li><?= $html->link('Edit Weapon Type', array('action' => 'edit', $weaponType['WeaponType']['id'])); ?> </li>
        <li><?= $html->link('Delete Weapon Type', array('action' => 'delete', $weaponType['WeaponType']['id']), null, 'Are you sure you want to delete this weapon type?'); ?> </li>
    </ul>
</div>