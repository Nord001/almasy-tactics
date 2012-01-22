<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2($guild['Guild']['name'], array('controller' => 'guilds', 'action' => 'view', $guild['Guild']['id'])); ?> | Guild Invitation
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        <?= $form->create('Guild', array('url' => $this->here)); ?>
            You have been invited to join the guild <b><?= $guild['Guild']['name']; ?></b>.
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

            <input type = 'hidden' name = 'data[invite_id]' value = '<?= $invite['GuildInvite']['id']; ?>' />

            <? if ($invite['GuildInvite']['message'] != ''): ?>
                <div>
                    Message from guildmember <b><?= $inviter['User']['username']; ?></b>:
                    <?= $invite['GuildInvite']['message']; ?>
                </div>
            <? endif; ?>

            <div>
                <input type = 'submit' name = 'data[response]' value = 'Accept Invitation' style = 'width: 200px' />
                <input type = 'submit' value = 'Reject Invitation' style = 'width: 200px' />
            </div>
        </form>
    </div>
</div>