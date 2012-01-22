<h2><?= $user['User']['username']; ?> <span style = 'color: rgb(125, 0, 0)'><?= $user['User']['state'] > 0 ? '(Banned)' : ''; ?></span></h2>
<table class = 'view-table'>
    <tr>
        <td class = 'column-1' />
        <td class = 'column-2' />
    </tr>
    <tr>
    </tr>
    <tr>
        <td>Username</td>
        <td><?= $user['User']['username']; ?></td>
    </tr>
    <tr>
        <td>Password</td>
        <td><?= $user['User']['password']; ?></td>
    </tr>
    <tr>
        <td>Email</td>
        <td><?= $user['User']['email']; ?></td>
    </tr>
    <tr>
        <td>Money</td>
        <td><?= $user['User']['money']; ?></td>
    </tr>
    <tr>
        <td>Level</td>
        <td><?= $user['User']['level']; ?></td>
    </tr>
    <tr>
        <td>Exp</td>
        <td><?= $user['User']['exp']; ?></td>
    </tr>
    <tr>
        <td>Zeal</td>
        <td><?= $user['User']['zeal']; ?></td>
    </tr>
    <tr>
        <td>Greed</td>
        <td><?= $user['User']['greed']; ?></td>
    </tr>
    <tr>
        <td>Ambition</td>
        <td><?= $user['User']['ambition']; ?></td>
    </tr>
    <tr>
        <td>Stat Points</td>
        <td><?= $user['User']['stat_points']; ?></td>
    </tr>
    <tr>
        <td>Date Created</td>
        <td><?= $user['User']['date_created']; ?></td>
    </tr>
    <tr>
        <td>Last Action</td>
        <td><?= $user['User']['last_action']; ?></td>
    </tr>
    <tr>
        <td>Last Login</td>
        <td><?= $user['User']['last_login']; ?></td>
    </tr>
    <tr>
        <td>Admin</td>
        <td><?= $user['User']['admin'] ? 'Yes' : 'No'; ?></td>
    </tr>
    <tr>
        <td>Portrait</td>
        <td><?= $html->image('sprites/' . $user['User']['portrait'] . '.png'); ?></td>
    </tr>
    <tr>
        <td>Reset Key</td>
        <td><?= $user['User']['reset_key']; ?></td>
    </tr>
    <tr>
        <td>First Character Name</td>
        <td><?= $user['User']['first_character_name']; ?></td>
    </tr>
    <tr>
        <td>Profile</td>
        <td><?= h($user['User']['profile_text']); ?></td>
    </tr>
    <tr>
        <td>User Agent</td>
        <td><?= $user['User']['user_agent']; ?></td>
    </tr>
</table>

<div class = "actions">
    <ul>
        <li><?= $html->link('Dupe Item For User', array('action' => 'give_item', $user['User']['id'])); ?> </li>
        <li><?= $html->link('Create New Item For User', array('controller' => 'user_items', 'action' => 'add', $user['User']['id'])); ?> </li>
        <li><?= $html->link('Give Stackable Item To User', array('action' => 'give_stackable', $user['User']['id'])); ?> </li>
        <li><?= $html->link('Impersonate User', array('action' => 'impersonate', $user['User']['id'])); ?> </li>
        <li><?= $html->link('Ban User', array('action' => 'ban', $user['User']['id']), null, 'Ban this user?'); ?> </li>
        <li><?= $html->link('Unban User', array('action' => 'unban', $user['User']['id']), null, 'Unban this user?'); ?> </li>
        <li><?= $html->link('Edit User', array('action' => 'edit', $user['User']['id'])); ?> </li>
    </ul>
</div>

<h3>Characters</h3>

This user has <?= count($user['Character']); ?> characters.

<table class = 'data'>
<tr>
    <th>Name</th>
    <th class = "actions">Actions</th>
</tr>
<?php
$i = 0;

foreach ($user['Character'] as $character):
    $class = null;
    if ($i++ % 2 == 0) {
        $class = ' class="altrow"';
    }
?>
    <tr<?= $class;?>>
        <td>
            <?= $character['name']; ?>
        </td>
        <td class = "actions">
            <?= $html->link('View', array('controller' => 'characters', 'action' => 'view', $character['id'])); ?>
            <?= $html->link('Edit', array('controller' => 'characters', 'action' => 'edit', $character['id'])); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>


<h3>Items</h3>

This user has <?= count($user['UserItem']); ?> items.

<table class = 'data'>
<tr>
    <th>User Item Id</th>
    <th>Name</th>
    <th class = "actions">Actions</th>
</tr>
<?php
$i = 0;

foreach ($user['UserItem'] as $item):
    $class = null;
    if ($i++ % 2 == 0) {
        $class = ' class="altrow"';
    }
?>
    <tr<?= $class;?>>
        <td>
            <?= $item['id']; ?>
        </td>
        <td>
            <?= $item['name']; ?>
        </td>
        <td class = "actions">
            <?= $html->link('View', array('controller' => 'user_items', 'action' => 'view', $item['id'])); ?>
            <?= $html->link('Edit', array('controller' => 'user_items', 'action' => 'edit', $item['id'])); ?>
            <?= $html->link('Delete', array('controller' => 'user_items', 'action' => 'delete', $item['id']), null, 'Are you sure you want to delete this item?'); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>