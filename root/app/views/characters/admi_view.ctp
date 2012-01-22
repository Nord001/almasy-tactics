<h2><?= $character['Character']['name']; ?></h2>

<div style = 'width: 48%; float: left'>
    <table class = 'view-table'>
        <tr>
            <td class = 'column-1' />
            <td class = 'column-2' />
        </tr>
        <tr>
            <td>Name</td>
            <td><?= $character['Character']['name']; ?></td>
        </tr>
        <tr>
            <td>STR</td>
            <td><?= $character['Character']['str']; ?></td>
        </tr>
        <tr>
            <td>INT</td>
            <td><?= $character['Character']['int']; ?></td>
        </tr>
        <tr>
            <td>VIT</td>
            <td><?= $character['Character']['vit']; ?></td>
        </tr>
        <tr>
            <td>LUK</td>
            <td><?= $character['Character']['luk']; ?></td>
        </tr>
        <tr>
            <td>Class</td>
            <td>
                <? if ($character['CClass']['monster']): ?>
                    <?= $html->link($character['CClass']['name'], array('controller' => 'monsters', 'action' => 'view', $character['CClass']['id'])); ?>
                <? else: ?>
                    <?= $html->link($character['CClass']['name'], array('controller' => 'c_classes', 'action' => 'view', $character['CClass']['id'])); ?>
                <? endif; ?>
            </td>
        </tr>
        <tr>
            <td>Affinity</td>
            <td><?= AffinityNameFromAffinity($character['Character']['affinity']); ?></td>
        </tr>
        <tr>
            <td>Level</td>
            <td><?= $character['Character']['level']; ?></td>
        </tr>
    </table>

    <div class ="actions">
        <ul>
            <li><?= $html->link('Edit Character', array('action' => 'edit', $character['Character']['id'])); ?> </li>
        </ul>
    </div>
</div>

<div style = 'clear: both;'></div>