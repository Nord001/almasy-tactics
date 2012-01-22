<h1>Help Files</h1>

<?= count($files); ?> files in database.

<table class = 'data'>
<tr class = 'header'>
    <th>Name</th>

    <th class = 'actions'>Actions</th>
</tr>
<?php
$i = 0;
foreach ($files as $file):
    $css = null;
    if ($i++ % 2 == 0) {
        $css = ' class = "altrow"';
    }
?>
    <tr<?= $css;?>>
        <td><?= $file; ?></td>
        <td class = "actions">
            <?= $html->link('Edit', array('action' => 'edit', $file)); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>