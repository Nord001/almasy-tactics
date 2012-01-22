<h2><?= $bonusType['BonusType']['name']; ?></h2>
<table class = 'view-table'>
    <tr>
        <td class = 'column-1' />
        <td class = 'column-2' />
    </tr>
    <tr>
    </tr>
    <tr>
        <td>Name</td>
        <td><?= $bonusType['BonusType']['name']; ?></td>
    </tr>
</table>

<div class = "actions">
    <ul>
        <li><?= $html->link('Edit Bonus Type', array('action' => 'edit', $bonusType['BonusType']['id'])); ?> </li>
        <li><?= $html->link('Delete Bonus Type', array('action' => 'delete', $bonusType['BonusType']['id']), null, 'Are you sure you want to delete this bonus type?'); ?> </li>
    </ul>
</div>