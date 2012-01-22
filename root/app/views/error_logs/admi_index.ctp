<style type = 'text/css'>
    #ErrorTable th {
        text-align: center;
        font-size: 12pt;
    }

    #ErrorTable td {
        text-align: left;
    }

    #ErrorTable {
        width: 100%;
        font-size: 80%;
    }
</style>

<div class = 'DividerHeader'>
    Error Log
</div>

<div>
    <?= $numRecentErrors; ?> errors in last 24 hours.
</div>

<table id = 'ErrorTable'>
    <tr>
        <th>Error</th>
        <th>File</th>
        <th>Time</th>
    </tr>
    <? foreach ($errorList as $error): ?>
        <tr>
            <?
                $errorString = $error['ErrorLog']['error'];
                if (strlen($errorString) > 50)
                    $errorString = substr($errorString, 0, 47) . '...';
            ?>
            <td><?= $html->link(h($errorString), array('controller' => 'error_logs', 'action' => 'view', $error['ErrorLog']['id'])); ?></td>
            <td><? printf('%s (%s)', $error['ErrorLog']['file'], $error['ErrorLog']['line']); ?></td>
            <td><? printf('%s (%s)', $error['ErrorLog']['time'], $time->GetTimeAgoString(strtotime($error['ErrorLog']['time']))); ?></td>
        </tr>
    <? endforeach; ?>
</table>