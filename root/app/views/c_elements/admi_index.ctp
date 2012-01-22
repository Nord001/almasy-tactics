<h1>Elements</h1>
<table class = 'data'>
<tr class = 'header'>
    <th>Attacking Element</th>
    <th>% On Fire</th>
    <th>% On Steel</th>
    <th>% On Wood</th>
    <th>% On Earth</th>
    <th>% On Water</th>
    <th class = 'actions'>Actions</th>
</tr>
<?php
$i = 0;
foreach ($elements as $element):
    $css = null;
    if ($i++ % 2 == 0) {
        $css = ' class = "altrow"';
    }
?>
    <tr<?= $css;?>>
        <td><?= Inflector::humanize($element['CElement']['name']); ?></td>
        <td><?= $element['CElement']['fire']; ?></td>
        <td><?= $element['CElement']['steel']; ?></td>
        <td><?= $element['CElement']['wood']; ?></td>
        <td><?= $element['CElement']['earth']; ?></td>
        <td><?= $element['CElement']['water']; ?></td>
        <td class = "actions">
            <?= $html->link('Edit', array('action' => 'edit', $element['CElement']['id'])); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>