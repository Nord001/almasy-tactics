<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2($guild['Guild']['name'], array('controller' => 'guilds', 'action' => 'view', $guild['Guild']['id'])); ?> | Dissolve Guild
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        Are you sure you want to dissolve the guild? This is final!
        <?= $form->create('Guild', array('action' => 'dissolve')); ?>
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
            <input type = 'submit' value = 'Dissolve Guild' style = 'width: 200px' />
            <input type = 'button' value = 'Cancel' style = 'width: 200px; font-size: 12pt; height: 30px;' class = 'LinkButton' href = '<?= $html->url(array('controller' => 'guilds', 'action' => 'view')); ?>' />
        </form>
    </div>
</div>