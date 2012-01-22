<h2>Users</h2>
<p>
<?php
echo $paginator->counter(array(
'format' => 'Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'
));
?></p>
<table class = 'data'>
<tr>
    <th><?= $paginator->sort('id');?></th>
    <th><?= $paginator->sort('username');?></th>
    <th><?= $paginator->sort('password');?></th>
    <th><?= $paginator->sort('email');?></th>
    <th><?= $paginator->sort('money');?></th>
    <th><?= $paginator->sort('level');?></th>
    <th><?= $paginator->sort('exp');?></th>
    <th><?= $paginator->sort('zeal');?></th>
    <th><?= $paginator->sort('greed');?></th>
    <th><?= $paginator->sort('ambition');?></th>
    <th><?= $paginator->sort('stat_points');?></th>
    <th><?= $paginator->sort('date_created');?></th>
    <th><?= $paginator->sort('last_action');?></th>
    <th><?= $paginator->sort('admin');?></th>
    <th class = "actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($users as $user):
    $class = null;
    if ($i++ % 2 == 0) {
        $class = ' class="altrow"';
    }
?>
    <tr<?= $class;?>>
        <td>
            <?= $user['User']['id']; ?>
        </td>
        <td>
            <?= $user['User']['username']; ?>
        </td>
        <td>
            <?= $user['User']['password']; ?>
        </td>
        <td>
            <?= $user['User']['email']; ?>
        </td>
        <td>
            <?= $user['User']['money']; ?>
        </td>
        <td>
            <?= $user['User']['level']; ?>
        </td>
        <td>
            <?= $user['User']['exp']; ?>
        </td>
        <td>
            <?= $user['User']['zeal']; ?>
        </td>
        <td>
            <?= $user['User']['greed']; ?>
        </td>
        <td>
            <?= $user['User']['ambition']; ?>
        </td>
        <td>
            <?= $user['User']['stat_points']; ?>
        </td>
        <td>
            <?= $user['User']['date_created']; ?>
        </td>
        <td>
            <?= $user['User']['last_action']; ?>
        </td>
        <td>
            <?= $user['User']['admin'] ? 'Yes' : 'No'; ?>
        </td>
        <td class = "actions">
            <?= $html->link('View', array('action' => 'view', $user['User']['id'])); ?>
            <?= $html->link('Edit', array('action' => 'edit', $user['User']['id'])); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>

<div class = "pagination">
    <?= $paginator->prev('<< '.'previous', array(), null, array('class'=>'disabled'));?>
 |  <?= $paginator->numbers();?>
    <?= $paginator->next('next'.' >>', array(), null, array('class' => 'disabled'));?>
</div>