<style type = 'text/css'>

#StatsBox, #MembersBox {
    position: absolute;
}

#StatsBoxContent, #ChatBoxContent, #MembersBoxContent {
    padding: 4px;
    border: 1px solid;
}

#StatsBox {
    width: 190px;
}

#StatsBoxContent {
    <? GradientBackground(array(
        array(0, 'hsl(206, 78%, 81%)'),
        array(1, 'hsl(206, 78%, 70%)')
    )); ?>
    background-color: hsl(206, 78%, 81%);
}

#ChatBox {
    width: 505px;
    margin-left: 205px;
}

#ChatBoxContent {
    <? GradientBackground(array(
        array(0, 'hsl(28, 78%, 75%)'),
        array(1, 'hsl(28, 78%, 65%)')
    )); ?>
    background-color: hsl(28, 78%, 75%);
}

#MembersBox {
    left: 725px;
    width: 235px;
}

#MembersBoxContent {
    <? GradientBackground(array(
        array(0, 'hsl(126, 65%, 65%)'),
        array(1, 'hsl(126, 65%, 55%)')
    )); ?>
    background-color: hsl(126, 65%, 61%);
}

.Header {
    border-bottom: 1px dotted;
    font-weight: bold;
    margin-top: -2px;
}

#GuildInfoTable {
    width: 100%;
}

#GuildInfoTable tr > td:last-child {
    text-align: right;
}

#ChatAreaDiv {
    margin-top: 2px;
    height: 290px;
    overflow: auto;
    background-color: rgb(0, 0, 0);
    color: rgb(255, 255, 255);
    padding: 2px;
    font-size: 10pt;
}

.Online {
    background-color: hsl(126, 82%, 41%);
}

</style>

<div class = 'PageDiv'>
    <div class = 'PageTitle'>
        <?= $guild['Guild']['name']; ?>

        <? if ($guild['Guild']['id'] == @$a_user['GuildMembership']['guild_id']): ?>
            <div style = 'position: absolute; top: 8px; right: 10px; font-size: 70%'>
                <input
                    type = 'button'
                    value = 'Transactions'
                    class = 'LinkButton'
                    href = '<?= $html->url(array('controller' => 'guilds', 'action' => 'transactions')); ?>'
                />
                <input
                    type = 'button'
                    value = 'Member List'
                    class = 'LinkButton'
                    href = '<?= $html->url(array('controller' => 'guilds', 'action' => 'member_list')); ?>'
                />
            </div>
        <? endif; ?>
    </div>

    <div class = 'PageContent'>
        <div class = 'BorderDiv' id = 'StatsBox'>
            <div id = 'StatsBoxContent'>
                <div class = 'Header'>
                    Guild Info
                </div>
                <? if ($guild['Guild']['emblem'] != ''): ?>
                    <div style = 'text-align: center; margin-top: 3px;'>
                        <img src = '<?= $html->url(array('controller' => 'guilds', 'action' => 'emblem', $guild['Guild']['id'])); ?>' class = 'GuildEmblem' />
                    </div>
                <? endif; ?>
                <table id = 'GuildInfoTable'>
                    <tr>
                        <td>Level</td>
                        <td><?= $guild['Guild']['level']; ?></td>
                    </tr>
                    <tr>
                        <td>Members</td>
                        <td><?= count($guild['Guild']['GuildMembership']); ?>/<?= $guild['Guild']['max_size']; ?></td>
                    </tr>
                    <tr>
                        <td>Leader</td>
                        <td>
                            <?= $html->link2(
                                    $guild['Guild']['Leader']['username'],
                                    array('controller' => 'users', 'action' => 'profile', $guild['Guild']['Leader']['username'])
                                );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Formed</td>
                        <td><?= date('M. d, Y', strtotime($guild['Guild']['created'])); ?></td>
                    </tr>
                    <? if ($guild['Guild']['id'] == @$a_user['GuildMembership']['guild_id']): ?>
                        <tr>
                            <td>Funds</td>
                            <td><?= number_format($guild['Guild']['money']); ?> yb</td>
                        </tr>
                    <? endif; ?>
                </table>
                <div style = 'text-align: center'>
                    <? if ($guild['Guild']['id'] == @$a_user['GuildMembership']['guild_id']): ?>
                        <? if ($guild['Guild']['leader_id'] == @$a_user['User']['id']): ?>
                            <?= $html->link2('Upgrade', array('controller' => 'guilds', 'action' => 'upgrade')); ?> <br />
                            <? if ($guild['Guild']['can_have_emblem']): ?>
                                <?= $html->link2('Change Emblem', array('controller' => 'guilds', 'action' => 'change_emblem')); ?> <br />
                            <? endif; ?>
                            <?= $html->link2('Dissolve Guild', array('controller' => 'guilds', 'action' => 'dissolve')); ?> <br />
                        <? endif; ?>

                        <? if (@$a_user['GuildMembership']['can_invite']): ?>
                            <?= $html->link2('Invite Player', array('controller' => 'guilds', 'action' => 'invite')); ?> <br />
                        <? endif; ?>

                        <?= $html->link2('Leave Guild', array('controller' => 'guilds', 'action' => 'leave')); ?> <br />
                    <? endif; ?>
                </div>
            </div>
        </div>

        <? if ($guild['Guild']['id'] == @$a_user['GuildMembership']['guild_id']): ?>
            <div class = 'BorderDiv' id = 'MembersBox'>
                <div id = 'MembersBoxContent'>
                    <div class = 'Header'>
                        Members
                    </div>
                    <div style = 'height: 395px; overflow: auto'>
                        <? foreach ($guild['Guild']['GuildMembership'] as $membership): ?>
                            <? $online = strtotime($membership['GuildMembership']['User']['last_action']) > strtotime('-15 minutes') ? 'Online' : ''; ?>
                            <div class = 'ShrinkText <?= $online; ?>' shrinkOffset = 20>
                                <?= $html->image('sprites/' . $membership['GuildMembership']['User']['portrait'] . '.png', array('style' => 'border: 1px solid; height: 20px; vertical-align: middle;')); ?>
                                <?= $html->link2($membership['GuildMembership']['User']['username'], array('controller' => 'users', 'action' => 'profile', $membership['GuildMembership']['User']['username'])); ?>
                                - <?= $membership['GuildMembership']['position']; ?>
                            </div>
                        <? endforeach; ?>
                    </div>
                </div>
            </div>

            <div class = 'BorderDiv' id = 'ChatBox'>
                <div id = 'ChatBoxContent'>
                    <div class = 'Header'>
                        Messages
                    </div>
                    <? if ($guild['Guild']['announcement'] != ''): ?>

                        <?= $guild['Guild']['announcement']; ?> <span style = 'font-size: 8pt'>on
                        <?= date('M. d', strtotime($guild['Guild']['last_announcement_time'])); ?></span>
                    <? else: ?>
                        No announcements.
                    <? endif; ?>
                    <? if ($guild['Guild']['leader_id'] == @$a_user['User']['id']): ?>
                        <br />
                        <a href = '#' id = 'AnnouncementLink'>Make Announcement</a>
                        <form id = 'AnnouncementForm' method = 'POST' action = '/guilds/change_announcement'>
                            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
                            <input type = 'hidden' id = 'AnnouncementMessage' name = 'data[message]' value = '' />
                        </form>
                    <? endif; ?>

                    <div class = 'Header'>
                        Chat
                    </div>
                    <div id = 'ChatAreaDiv'>
                    </div>
                    <div>
                        <form style = 'width: 100%'>
                            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
                            <input type = 'text' style = 'width: 400px; display: inline;' name = 'data[message]' id = 'ChatMessage' autocomplete = 'off' AUTOCOMPLETE = 'off' />
                            <input type = 'submit' value = 'Chat' style = 'display: inline' id = 'ChatSubmit' />
                            <?= $html->image('cycle.gif', array('style' => 'margin-right: 2px; vertical-align: middle; display: none;', 'id' => 'ChatLoadingImg')); ?>
                        </form>
                    </div>
                </div>
            </div>
        <? else: ?>
            <div style = 'height: 250px'></div>
        <? endif; ?>
    </div>
</div>

<script type = 'text/javascript'>
    $(document).ready(function() {
        $('#ChatAreaDiv').guildChatDisplay({
            'loadingIcon': '#ChatLoadingImg'
        });

        $('#ChatSubmit').click(function(event) {
            event.preventDefault();
            $('#ChatAreaDiv').data('guildChatDisplay').postMessage($('#ChatMessage').val(), <?= $guild['Guild']['id']; ?>);
            $('#ChatMessage').val('');
        });

        $('#AnnouncementLink').click(function(event) {
            event.preventDefault();

            var result = prompt('What do you want to announce?', '');
            $('#AnnouncementMessage').val(result);
            $('#AnnouncementForm').submit();
        });
    });
</script>