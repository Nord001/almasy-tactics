<div class = 'AccountDiv'>
    <div class = 'AccountHeader'>
        <?= $html->link2($account['User']['username'], array('controller' => 'users', 'action' => 'view', $account['User']['id'])); ?>
    </div>

    <div class = 'AccountContent'>
        <? $i = 0; ?>
        <? foreach ($account['sessions'] as $session): ?>
            <? foreach ($session as $page): ?>
                <?
                    $hash = md5($page['page']);
                    $color = sprintf(
                        'rgb(%d, %d, %d)',
                        200 + ord($hash[0]) / 2,
                        200 + ord($hash[1]) / 2,
                        200 + ord($hash[2]) / 2
                    );

                    $duration = isset($page['duration']) ? $page['duration'] : 0;
                    $height = $duration + 20;
                    if ($height > 200)
                        $height = 200;
                ?>
                <div class = 'PageDiv' style = 'height: <?= $height; ?>px; background-color: <?= $color; ?>'>
                    <table style = 'width: 100%'>
                        <tr>
                            <td><?= $page['page']; ?></td>
                            <td style = 'text-align: right'><?= isset($page['duration']) ? $time->GetHourMinuteSecondString($page['duration']) : 'End'; ?></td>
                        </tr>
                    </table>
                </div>
            <? endforeach; ?>
            <h3>End Session <?= $time->GetHourMinuteSecondString($account['sessionTotals'][$i++]); ?></h3>
        <? endforeach; ?>
    </div>
</div>
