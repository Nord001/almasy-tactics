<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2($guild['Guild']['name'], array('controller' => 'guilds', 'action' => 'view', $guild['Guild']['id'])); ?> | Invite to Guild
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        <?= $form->create('Guild', array('url' => $this->here)); ?>
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

            <div>
                <label>Username</label>
                <input type = 'text' name = 'data[username]' value = '<?= @$user['User']['username']; ?>' />
            </div>
            <div>
                <label>Optional Message</label>
                <textarea name = 'data[message]' style = 'height: 100px'></textarea>
            </div>
            <input type = 'submit' value = 'Send Invitation' style = 'width: 200px' />
            <input type = 'button' value = 'Cancel' style = 'width: 200px; font-size: 12pt; height: 30px;' class = 'LinkButton' href = '<?= $html->url(array('controller' => 'guilds', 'action' => 'view')); ?>' />
        </form>
    </div>
</div>