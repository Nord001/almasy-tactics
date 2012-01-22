<h1>Experience</h1>
<table class = 'data'>
<tr class = 'header'>
    <th>Level</th>
    <th>Experience to Next Level</th>
    <th class = 'actions'>Actions</th>
</tr>
<?php
$i = 0;
foreach ($experience as $level):
    $css = null;
    if ($i++ % 2 == 0) {
        $css = ' class = "altrow"';
    }
?>
    <tr<?= $css;?>>
        <td><?= $level['Experience']['id']; ?></td>
        <td><?= $level['Experience']['value']; ?></td>
        <td class = "actions">
            <?= $html->link('Edit', array('action' => 'edit', $level['Experience']['id'])); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>