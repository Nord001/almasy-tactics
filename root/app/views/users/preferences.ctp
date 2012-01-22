<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2($a_user['User']['username'], array('controller' => 'users', 'action' => 'profile')); ?> |
        Preferences
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        <ul>
            <li>
                <?= $html->link2('Change Portrait', array('controller' => 'users', 'action' => 'change_portrait')); ?>
            </li>
            <li>
                <?= $html->link2('Change Email', array('controller' => 'users', 'action' => 'change_email')); ?>
            </li>
            <li>
                <?= $html->link2('Change Password', array('controller' => 'users', 'action' => 'change_password')); ?>
            </li>
            <? if (false): ?>
                <li>
                    <?= $html->link2('Reset Account', array('controller' => 'users', 'action' => 'reset')); ?>
                </li>
            <? endif; ?>
        </ul>

        <?= $form->create('User', array('action' => 'preferences')); ?>
        <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

        <input type = 'hidden' name = 'data[has_data]' value = '1' />

        <div style = 'height: 50px; position: relative;'>
            <div style = 'position: absolute; left: 25px; top: 0px;'>
                <label class = 'LabelClass' for = "UserHideHelpBar">Hide Help Bar</label>
            </div>

            <div style = 'position: absolute; left: 0px; top: 2px;'>
                <input type = "checkbox" name = "data[User][hide_help_bar]" style = 'width: auto;' value = "1" id = "UserHideHelpBar" <?= $a_user['User']['hide_help_bar'] ? 'checked' : ''; ?> />
            </div>

            <div style = 'position: absolute; top: 25px; left: 2px;'>
                Hides the help bar that appears at the top of some pages.
            </div>
        </div>

        <div style = 'height: 50px; position: relative;'>
            <div style = 'position: absolute; left: 25px; top: 0px;'>
                <label class = 'LabelClass' for = "UserDisableShortcuts">Disable Keyboard Shortcuts</label>
            </div>

            <div style = 'position: absolute; left: 0px; top: 2px;'>
                <input type = "checkbox" name = "data[User][disable_shortcuts]" style = 'width: auto;' value = "1" id = "UserDisableShortcuts" <?= $a_user['User']['disable_shortcuts'] ? 'checked' : ''; ?> />
            </div>

            <div style = 'position: absolute; top: 25px; left: 2px;'>
                Disables keyboard shortcuts for the site.
            </div>
        </div>

        <label class = 'LabelClass' for = "ProfileText">Profile Info</label>
        <textarea style = 'width: 600px; height: 200px' maxlength = 500 name = 'data[User][profile_text]'><?= h($a_user['User']['profile_text']); ?></textarea>

        <?= $form->end('Save!'); ?>
    </div>
</div>
