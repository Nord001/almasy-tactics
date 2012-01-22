<h2>News</h2>
<p>
<?php
echo $paginator->counter(array(
'format' => 'Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'
));
?></p>
<table class = 'data'>
<tr>
    <th><?= $paginator->sort('id');?></th>
    <th><?= $paginator->sort('title');?></th>
    <th><?= $paginator->sort('content');?></th>
    <th><?= $paginator->sort('date_posted');?></th>
    <th><?= $paginator->sort('user_id');?></th>
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
            <?= $news['News']['id']; ?>
        </td>
        <td>
            <?= $news['News']['title']; ?>
        </td>
        <td>
            <?= $news['News']['content']; ?>
        </td>
        <td>
            <?= $news['News']['date_posted']; ?>
        </td>
        <td>
            <?= $news['News']['user_id']; ?>
        </td>
        <td class = "actions">
            <?= $html->link2('View', array('action' => 'view', $news['News']['id'])); ?>
            <?= $html->link2('Edit', array('action' => 'edit', $news['News']['id'])); ?>
            <?= $html->link2('Delete', array('action' => 'delete', $news['News']['id']), null, 'Are you sure you want to delete this news?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>

<div class = "pagination">
    <?= $paginator->prev('<< '.'previous', array(), null, array('class'=>'disabled'));?>
 |     <?= $paginator->numbers();?>
    <?= $paginator->next('next'.' >>', array(), null, array('class' => 'disabled'));?>
</div>
<div class = "actions">
    <ul>
        <li><?= $html->link2('New News', array('action' => 'add')); ?></li>
    </ul>
</div>
