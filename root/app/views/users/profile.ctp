<style type = 'text/css'>
    #StatsTable {
        width: 100%;
    }

    #StatsTable tr td:first-child {
        color: rgb(50, 50, 50);
    }
    #StatsTable tr td:last-child {
        text-align: right;
    }
</style>

<div class = 'PageDiv'>
    <div class = 'PageTitle'>
        <?= $user['User']['username']; ?>
    </div>

    <div class = 'PageContent'>
        <div style = 'float: left; width: 450px; position: relative;'>
            <div style = 'height: 110px'>
                <div style = 'position: absolute; top: 10px; left: 5px;'>
                    <?= $html->image('sprites/' . $user['User']['portrait'] . '.png', array('class' => 'face-icon')); ?>
                </div>

                <div style = 'position: absolute; top: 0px; left: 125px'>
                    <table id = 'StatsTable'>
                        <tr>
                            <td>
                                Joined
                            </td>
                            <td>
                                <?= date('M. j, Y', strtotime($user['User']['date_created'])); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Battle Record
                            </td>
                            <td>
                                <?= $user['User']['battles_won']; ?> - <?= $user['User']['battles_lost']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Total Earned
                            </td>
                            <td>
                                <?= number_format($user['User']['total_money_earned']); ?> yb
                            </td>
                        </tr>
                        <? if ($a_user['User']['id'] == $user['User']['id']): ?>
                            <tr>
                                <td>
                                    Last Action
                                </td>
                                <td>
                                    <?= date('M. j, Y', strtotime($user['User']['last_action'])); ?>
                                </td>
                            </tr>
                        <? endif; ?>
                        <tr>
                            <td>
                                Guild
                            </td>
                            <td>
                                <? if (isset($guild)): ?>
                                    <? if ($guild['Guild']['emblem'] != ''): ?>
                                        <img src = '<?= $html->url(array('controller' => 'guilds', 'action' => 'emblem', $guild['Guild']['id'])); ?>' class = 'GuildEmblem' style = 'width: 25px' />
                                    <? endif; ?>
                                    <span style = 'vertical-align: center'>
                                        <?= $html->link2($guild['Guild']['name'], array('controller' => 'guilds', 'action' => 'view', $guild['Guild']['id'])); ?>
                                    </span>
                                <? else: ?>
                                    None
                                    <? if (@$a_user['GuildMembership']['can_invite']): ?>
                                        (<?= $html->link2('Invite to Guild', array('controller' => 'guilds', 'action' => 'invite', $user['User']['id'])); ?>)
                                    <? endif; ?>
                                <? endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <? if ($a_user['User']['id'] == $user['User']['id']): ?>
                <div style = 'padding: 5px;'>
                    <? if (isset($referringUser)): ?>
                        <b>You were referred by <?= $referringUser['User']['username']; ?>.</b>
                    <? endif; ?>
                    <ul>
                        <li>
                            <?= $html->link2('Account & Preferences', array('controller' => 'users', 'action' => 'preferences')); ?>
                        </li>
                        <li>
                            <?= $html->link2('Referrals', 'users/referrals'); ?>
                        </li>
                        <li>
                            <?= $html->link2('Inbox', 'messages'); ?>
                        </li>
                        <? if (empty($a_user['GuildMembership'])): ?>
                            <li>
                                <?= $html->link2('Start a Guild', 'guilds/create'); ?>
                            </li>
                        <? endif; ?>
                    </ul>
                </div>
            <? endif; ?>
        </div>

        <div style = 'float: right; width: 500px; margin-right: 10px;'>
            <div style = 'overflow: hidden; height: 250px;'>
                <?= nl2br($user['User']['profile_text']); ?>
            </div>
        </div>

        <div style = 'clear: both;'></div>
    </div>
</div>
