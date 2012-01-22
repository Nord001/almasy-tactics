<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2($a_user['User']['username'], array('controller' => 'users', 'action' => 'profile')); ?> |
        Change Email
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        <?php echo $form->create('User', array('action' => 'change_email'));?>
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

            <?php
                echo $form->label('Current Email');
                echo $form->text('old_email');

                echo $form->label('New Email');
                echo $form->text('email');

                echo $form->label('Password');
                echo $form->password('password');
            ?>
        <?php echo $form->end('Submit');?>
    </div>
</div>