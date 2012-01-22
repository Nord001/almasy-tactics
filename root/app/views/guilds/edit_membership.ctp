<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2($guild['Guild']['name'], array('controller' => 'guilds', 'action' => 'view', $guild['Guild']['id'])); ?> | Edit Membership for <?= $user['User']['username']; ?>
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        <?= $form->create('GuildMembership', array('url' => $this->here)); ?>
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

            <input type = 'hidden' name = 'data[GuildMembership][id]' value = '<?= $membership['GuildMembership']['id']; ?>' />

            <div>
                <label>Position</label>
                <input type = 'text' name = 'data[GuildMembership][position]' value = '<?= $membership['GuildMembership']['position']; ?>' />
            </div>

            <table>
                <tr>
                    <td>
                        Can Invite
                    </td>
                    <td>
                        <input type = 'checkbox' name = 'data[GuildMembership][can_invite]' class = 'Normal'
                               value = '1' <?= $membership['GuildMembership']['can_invite'] ? 'checked' : ''; ?> />
                    </td>
                </tr>
                <tr>
                    <td>
                        Can Expel
                    </td>
                    <td>
                        <input type = 'checkbox' name = 'data[GuildMembership][can_expel]' class = 'Normal'
                               value = '1' <?= $membership['GuildMembership']['can_expel'] ? 'checked' : ''; ?> />
                    </td>
                </tr>
                <tr>
                    <td>
                        Can Transfer Funds
                    </td>
                    <td>
                        <input type = 'checkbox' name = 'data[GuildMembership][can_transfer_money]' class = 'Normal'
                            value = '1' <?= $membership['GuildMembership']['can_transfer_money'] ? 'checked' : ''; ?> />
                    </td>
                </tr>
            </table>

            <div>
                <input type = 'submit' value = 'Save' style = 'width: 200px' />
            </div>
        </form>
    </div>
</div>