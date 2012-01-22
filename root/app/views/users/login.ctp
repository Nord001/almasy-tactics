<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        Login to Almasy (or <?= $html->link2('are you new?', array('controller' => 'users', 'action' => 'register')); ?>)
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>

        <div style = 'position: absolute; top: 1px; left: 760px;'>
            <?= $html->image('/img/posologist.png'); ?>
        </div>

        <?= $form->create('User', array('action' => 'login', 'style' => 'position: relative')); ?>

            <?= $form->input('username'); ?>
            <?= $form->input('password'); ?>

            <div style = 'position: absolute; top: 108px; left: 20px;'>
                <label for = "UserRememberMe">Remember Me</label>
            </div>

            <div style = 'position: absolute; top: 115px; left: -3px;'>
                <input type = "hidden" name = "data[User][remember_me]" id = "UserRememberMe_" value = "0" /><input type = "checkbox" name = "data[User][remember_me]" style = 'width: auto;' value = "1" id = "UserRememberMe" />
            </div>

            <input type = "submit" value = "Login" style = 'width: 150px; height: 35px; margin-top: 45px;' />
        </form>

    </div>
</div>