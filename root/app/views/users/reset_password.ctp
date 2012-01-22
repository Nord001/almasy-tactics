<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        Reset Password For <?= $user['User']['username']; ?>
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        <?php echo $form->create('User', array('action' => 'reset_password'));?>
        <?php
            echo $form->hidden('id', array('value' => $user['User']['id']));
            echo $form->hidden('reset_key', array('value' => $user['User']['reset_key']));
            echo $form->label('New Password');
            echo $form->password('password');
        ?>
        <?php echo $form->end('Submit');?>
    </div>
</div>