<?php echo $form->create('User');?>
    <fieldset>
        <legend>Edit User</legend>
    <?php
        echo $form->input('id');
        echo $form->input('username');
        echo $form->input('email');
        echo $form->input('money');
        echo $form->input('level');
        echo $form->input('exp');
        echo $form->input('zeal');
        echo $form->input('greed');
        echo $form->input('ambition');
        echo $form->input('stat_points');
        echo $form->input('portrait');
        echo $form->input('admin');
        echo $form->input('first_character_name');
        echo $form->input('reset_key');
        echo $form->input('profile_text');
    ?>
    </fieldset>
<?php echo $form->end('Submit');?>
