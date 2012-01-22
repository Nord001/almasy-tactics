<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2($a_user['User']['username'], array('controller' => 'users', 'action' => 'profile')); ?> |
        Change Password
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        <?php echo $form->create('User', array('action' => 'change_password'));?>
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

            <?php
                echo $form->label('Old Password');
                echo $form->password('old_password');

                echo $form->label('New Password');
                echo $form->password('password_1');

                echo $form->label('New Password (Confirm)');
                echo $form->password('password_2');
            ?>
        <?php echo $form->end('Submit');?>
    </div>
</div>