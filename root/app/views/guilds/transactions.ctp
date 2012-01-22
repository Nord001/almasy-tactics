<style type = 'text/css'>
    .Header {
        border-bottom: 1px dotted;
        font-size: 140%;
        margin-top: 10px;
    }
</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2($guild['Guild']['name'], array('controller' => 'guilds', 'action' => 'view', $guild['Guild']['id'])); ?> | Transactions
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        <div class = 'Header'>
            Deposit to Guild Funds
        </div>
        <form method = 'POST' action = '<?= $html->url(array('controller' => 'guilds', 'action' => 'deposit_money')); ?>'>
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

            <div>
                <label>Amount</label>
                <input type = 'text' name = 'data[amount]' value = '' />
            </div>

            <input type = 'submit' value = 'Deposit' style = 'width: 200px' />
        </form>

        <? if ($a_user['GuildMembership']['can_transfer_money']): ?>
            <div class = 'Header'>
                Withdraw from Guild Funds
            </div>
            <form method = 'POST' action = '<?= $html->url(array('controller' => 'guilds', 'action' => 'withdraw_money')); ?>'>
                <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

                <div>
                    <label>Recipient</label>
                    <input type = 'text' name = 'data[recipient]' value = '' />
                </div>

                <div>
                    <label>Amount</label>
                    <input type = 'text' name = 'data[amount]' value = '' />
                </div>

                <input type = 'submit' value = 'Withdraw' style = 'width: 200px' />
            </form>
        <? endif; ?>

        <div class = 'Header'>
            History
        </div>
        <table style = 'width: 100%'>
            <tr>
                <th>Initiator</th>
                <th>Receiver</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Time</th>
            </tr>
            <? foreach ($transactions as $transaction): ?>
                <tr>
                    <td>
                        <?= $html->image('sprites/' . $transaction['GuildTransaction']['Initiator']['portrait'] . '.png', array('style' => 'border: 1px solid; height: 20px; vertical-align: middle;')); ?>
                        <?= $html->link2($transaction['GuildTransaction']['Initiator']['username'], array('controller' => 'users', 'action' => 'profile', $transaction['GuildTransaction']['Initiator']['username'])); ?>
                    </td>
                    <td>
                        <? if ($transaction['GuildTransaction']['receiver_id'] != ''): ?>
                            <?= $html->image('sprites/' . $transaction['GuildTransaction']['Receiver']['portrait'] . '.png', array('style' => 'border: 1px solid; height: 20px; vertical-align: middle;')); ?>
                            <?= $html->link2($transaction['GuildTransaction']['Receiver']['username'], array('controller' => 'users', 'action' => 'profile', $transaction['GuildTransaction']['Receiver']['username'])); ?>
                        <? else: ?>
                            Guild
                        <? endif; ?>
                    </td>
                    <td>
                        <?
                            $amount = -$transaction['GuildTransaction']['amount'];
                            if ($transaction['GuildTransaction']['type'] == 'deposit')
                                $amount = -$amount;
                            echo number_format($amount);
                        ?> yb
                    </td>
                    <td>
                        <?= ucfirst($transaction['GuildTransaction']['type']); ?>
                    </td>
                    <td>
                        <?= date('M. d, Y', strtotime($transaction['GuildTransaction']['time'])); ?></span>
                    </td>
                </tr>
            <? endforeach; ?>
        </table>
    </div>
</div>