<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        Forgot your Password?
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        Enter your username and email you used to register and we will email you instructions to reset your password!

        <?= $form->create('User', array('action' => 'forgot')); ?>

        <?= $form->input('username'); ?>
        <?= $form->input('email'); ?>

        <br />

        <input type = 'submit' id = 'ResetUser' value = 'Done!' style = 'width: 200px' />
        <input type = 'button' id = 'BackButton' value = 'Never mind...' style = 'width: 200px; font-size: 100%; height: 30px;' />
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