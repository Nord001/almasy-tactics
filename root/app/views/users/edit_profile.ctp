<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2($a_user['User']['username'], array('controller' => 'users', 'action' => 'profile')); ?> |
        Edit Profile
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>

        <?= $form->create('User', array('action' => 'preferences')); ?>
        <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

        <label class = 'LabelClass' for = "ProfileText">Profile Info</label>

        <textarea style = 'width: 600px; height: 200px' maxlength = 1000 name = 'data[User][profile_text]'>
            <?= nl2br($a_user['User']['profile']); ?>
        </textarea>

        <?= $form->end('Save!'); ?>
    </div>
</div>
