<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2($a_user['User']['username'], array('controller' => 'users', 'action' => 'profile')); ?> |
        Reset Account
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        Are you sure you want to completely reset your account? This will delete everything!
        <?= $form->create('User', array('action' => 'reset')); ?>
        <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

        <div style = 'margin-bottom: 20px'>
            <label for = 'ResetPassword'>Password</label>
            <input id = 'ResetPassword' type = 'password' value = '' name = 'data[User][password]' />
        </div>

        <input type = 'submit' id = 'ResetUser' value = 'Yes, reset!' style = 'width: 200px' />
        <input type = 'button' id = 'BackButton' value = 'No, take me back!' style = 'width: 200px; font-size: 100%; height: 30px;' />
        </form>
    </div>
</div>

<script type = 'text/javascript'>
$(document).ready(function() {
    $('#BackButton').click(function(event) {
        event.preventDefault();

        window.location = '<?= $html->url(array('controller' => 'users', 'action' => 'profile')); ?>';
    });
});
</script>
