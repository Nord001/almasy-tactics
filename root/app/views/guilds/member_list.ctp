<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2($guild['Guild']['name'], array('controller' => 'guilds', 'action' => 'view', $guild['Guild']['id'])); ?> | Member List
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        <table style = 'width: 100%'>
            <tr>
                <th style = 'width: 200px'>Member</th>
                <th style = 'width: 200px'>Position</th>
                <th>Join Date</th>
                <th>Balance</th>
                <th>Invite</th>
                <th>Expel</th>
                <th>Transfer Funds</th>
                <th>Actions</th>
            </tr>
            <? foreach ($guild['Guild']['GuildMembership'] as $membership): ?>
                <tr>
                    <td>
                        <?= $html->image('sprites/' . $membership['GuildMembership']['User']['portrait'] . '.png', array('style' => 'border: 1px solid; height: 20px; vertical-align: middle;')); ?>
                        <?= $html->link2($membership['GuildMembership']['User']['username'], array('controller' => 'users', 'action' => 'profile', $membership['GuildMembership']['User']['username'])); ?>
                    </td>
                    <td>
                        <?= $membership['GuildMembership']['position']; ?>
                    </td>
                    <td>
                        <?= date('M. d, Y', strtotime($membership['GuildMembership']['join_date'])); ?></span>
                    </td>
                    <td>
                        <?= number_format(intval($membership['GuildMembership']['guild_balance'] / 1000)); ?>k yb
                    </td>
                    <td>
                        <?= $membership['GuildMembership']['can_invite'] ? 'Yes' : 'No'; ?>
                    </td>
                    <td>
                        <?= $membership['GuildMembership']['can_expel'] ? 'Yes' : 'No'; ?>
                    </td>
                    <td>
                        <?= $membership['GuildMembership']['can_transfer_money'] ? 'Yes' : 'No'; ?>
                    </td> <td>
                        <?
                            $actions = array();
                            if ($guild['Guild']['leader_id'] == @$a_user['User']['id'])
                                $actions[] = $html->link2('Edit', array('controller' => 'guilds', 'action' => 'edit_membership', $membership['GuildMembership']['id']));
                            if ($a_user['GuildMembership']['can_expel'] &&
                                $membership['GuildMembership']['user_id'] != $guild['Guild']['leader_id'] &&
                                $membership['GuildMembership']['user_id'] != $a_user['User']['id'])
                                $actions[] = $html->link2('Expel', array('controller' => 'guilds', 'action' => 'expel', $membership['GuildMembership']['user_id']));
                            echo implode(', ', $actions);
                        ?>
                    </td>
                </tr>
            <? endforeach; ?>
        </table>
    </div>
</div>