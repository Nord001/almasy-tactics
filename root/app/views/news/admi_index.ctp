<h2>News</h2>
<p>
<table class = 'data'>
<tr>
    <th><?= $paginator->sort('title');?></th>
    <th><?= $paginator->sort('date_posted');?></th>
    <th><?= $paginator->sort('Posted By', 'User.username');?></th>
    <th class = "actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($news as $news):
    $class = null;
    if ($i++ % 2 == 0) {
        $class = ' class="altrow"';
    }
?>
    <tr<?= $class;?>>
        <td>
            <?= $news['News']['title']; ?>
        </td>
        <td>
            <?= $news['News']['date_posted']; ?>
        </td>
        <td>
            <?= $news['User']['username']; ?>
        </td>
        <td class = "actions">
            <?= $html->link('Edit', array('action' => 'edit', $news['News']['id'])); ?>
            <?= $html->link('Delete', array('action' => 'delete', $news['News']['id']), null, 'Are you sure you want to delete this news post?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>