<div class = 'DividerHeader'>
    <?= $error['ErrorLog']['error']; ?>
</div>

<table class = 'view-table'>
    <tr>
        <td class = 'column-1' />
        <td class = 'column-2' />
    </tr>
    <tr>
        <th>Info</th>
    </tr>
    <tr>
        <td>Request</td>
        <td><?= $error['ErrorLog']['request']; ?></td>
    </tr>
    <tr>
        <td>File</td>
        <td><? printf('%s (%s)', $error['ErrorLog']['file'], $error['ErrorLog']['line']); ?></td>
    </tr>
    <tr>
        <td>Time</td>
        <td><? printf('%s (%s)', $error['ErrorLog']['time'], $time->GetTimeAgoString(strtotime($error['ErrorLog']['time']))); ?></td>
    </tr>
    <tr>
        <td>Referrer</td>
        <td><?= $error['ErrorLog']['referrer']; ?></td>
    </tr>
    <tr>
        <td>User</td>
        <td>
            <? if (isset($errorUser) && $errorUser !== false): ?>
                <?= $html->link($errorUser['User']['username'], array('controller' => 'users', 'action' => 'view', $error['ErrorLog']['user_id'])); ?>
            <? else: ?>
                None
            <? endif; ?>
        </td>
    </tr>
</table>
<table class = 'view-table'>
    <tr>
        <th>Stack Trace</th>
    </tr>
    <tr>
        <td style = 'white-space: pre; font-family: monospace;'><?= $error['ErrorLog']['stack_trace']; ?></td>
    </tr>

    <tr>
        <th>Data</th>
    </tr>
    <tr>
        <td style = 'white-space: pre; font-family: monospace;'><?= $error['ErrorLog']['data']; ?></td>
    </tr>

    <tr>
        <th>GET</th>
    </tr>
    <tr>
        <td style = 'white-space: pre; font-family: monospace;'><?= $error['ErrorLog']['get']; ?></td>
    </tr>

    <tr>
        <th>POST</th>
    </tr>
    <tr>
        <td style = 'white-space: pre; font-family: monospace;'><?= $error['ErrorLog']['post']; ?></td>
    </tr>

    <tr>
        <th>COOKIE (filtered)</th>
    </tr>
    <tr>
        <td style = 'white-space: pre; font-family: monospace;'><?= $error['ErrorLog']['cookie']; ?></td>
    </tr>

    <tr>
        <th>SESSION</th>
    </tr>
    <tr>
        <td style = 'white-space: pre; font-family: monospace;'><?= $error['ErrorLog']['session']; ?></td>
    </tr>

    <tr>
        <th>SERVER</th>
    </tr>
    <tr>
        <td style = 'white-space: pre; font-family: monospace;'><?= $error['ErrorLog']['server']; ?></td>
    </tr>

    <tr>
        <th>View Variables</th>
    </tr>
    <tr>
        <td style = 'white-space: pre; font-family: monospace;'><?= $error['ErrorLog']['view_vars']; ?></td>
    </tr>

    <tr>
        <th>Context</th>
    </tr>
    <tr>
        <td style = 'white-space: pre; font-family: monospace;'><?= $error['ErrorLog']['context']; ?></td>
    </tr>
</table>