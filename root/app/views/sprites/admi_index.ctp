<h1>Sprites</h1>

<?= count($sprites); ?> sprites in database.

<table class = 'data'>
<tr class = 'header'>
    <th>Name</th>
    <th>Thumbnail</th>

    <th class = 'actions'>Actions</th>
</tr>
<?php
$i = 0;
foreach ($sprites as $sprite):
    $css = null;
    if ($i++ % 2 == 0) {
        $css = ' class = "altrow"';
    }
?>
    <tr<?= $css;?>>
        <td><?= $sprite; ?></td>
        <td>
            <?=
                $html->link(
                    $html->image($imgDir . $sprite, array('border' => '0px')),
                    '/img/' . $imgDir . $sprite,
                    null,
                    null,
                    false // Don't escape
                );
            ?>
        </td>
        <td class = "actions">
            <?= $html->link('Delete', array('action' => 'delete', $sprite), null, 'Are you sure you want to delete this sprite?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>