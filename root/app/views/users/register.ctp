<style type = 'text/css'>
    .Tip {
        margin-top: -2px;
        text-align: right;
        font-size: 90%;
        width: 300px;
    }

    .PageContent label {
        font-size: 130%;
        margin-bottom: -1px;
    }

    .PageContent input {
        width: 300px;
        margin-top: 3px;
    }

    .PageContent input[type=text], .PageContent input[type=password] {
        font-size: 140%;
        padding: 3px;
        margin-bottom: 10px;
    }

    .ReferTr td {
        font-size: 140%;
        font-weight: bold;
        color: rgb(0, 0, 50);
    }

    #FormTable tr td {
        vertical-align: top;
    }

    #FormTable {
        margin-top: 15px;
    }

    .LabelTd {
        height: 80px;
        width: 325px;
        text-align: right;
    }

</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        Start Playing Almasy! :D
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>

        <div style = 'position: absolute; top: 50px; left: 725px;'>
            <?= $html->image('/img/savant.png'); ?>
        </div>

        <form style="margin-top: -10px" id="UserRegisterForm" method="post" action="<?= h($this->here); ?>">

            <table id = 'FormTable'>
                <? if (isset($referringUser)): ?>
                    <tr class = 'ReferTr'>
                        <td></td>
                        <td>Referred by <?= $referringUser['User']['username']; ?></td>
                    </tr>

                    <?= $form->hidden('referring_id', array('value' => $referringUser['User']['id'])); ?>
                <? endif; ?>

                <tr>
                    <td class = 'LabelTd'>
                        <label for = "Username">Username</label>
                        <div class = 'Tip'>
                            Your username can only contain alphanumeric characters.
                        </div>
                    </td>
                    <td>
                        <input
                            name = "data[User][reg_username]"
                            maxlength = "<?= USERNAME_MAX_CHARS; ?>"
                            value = "<?= !empty($this->data) ? $this->data['User']['reg_username'] : ''; ?>"
                            id = "Username"
                            type = "text"
                        >
                        <div id = 'UsernameAvailableLabel' style = 'margin-top: -3px; font-size: 80%;'>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class = 'LabelTd'>
                        <label for = "Password">Password</label>
                        <div class = 'Tip'>
                            Your password must be at least six characters. A good password
                            should have punctuation marks, capital letters, and numbers.
                        </div>
                    </td>
                    <td>
                        <input
                            name = "data[User][reg_password]"
                            maxlength = "25"
                            value = "<?= !empty($this->data) ? $this->data['User']['reg_password'] : ''; ?>"
                            id = "Password"
                            type = "password"
                        />
                    </td>
                </tr>

                <tr>
                    <td class = 'LabelTd'>
                        <label for = "Email">Email</label>
                        <div class = 'Tip'>
                            Your email will never be disclosed and will not be made public.
                        </div>
                    </td>
                    <td>
                        <input
                            name = "data[User][email]"
                            value = "<?= !empty($this->data) ? $this->data['User']['email'] : ''; ?>"
                            id = "Email"
                            type = "text"
                        >
                        <span id = 'InvalidEmailLabel' style = 'display: none; font-size: 80%; color: rgb(200, 0, 0)'>
                            Email invalid.
                        </span>
                    </td>
                </tr>

                <tr>
                    <td class = 'LabelTd'>
                        <label for = "CharacterName">First Character Name</label>
                        <div class = 'Tip'>
                            Character names can only contain alphanumeric characters and spaces.
                        </div>
                    </td>
                    <td>
                        <input
                            name = "data[Character][name]"
                            maxlength = "<?= CHARACTER_NAME_MAX_CHARS; ?>"
                            value = "<?= !empty($this->data) ? $this->data['Character']['name'] : ''; ?>"
                            id = "CharacterName"
                            type = "text"
                        >
                        <span id = 'InvalidCharacterNameLabel' style = 'display: none; font-size: 80%; color: rgb(200, 0, 0)'>
                            Character name invalid.
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>By creating an account, you affirm that you have read and agreed to follow the Almasy Tactics <a href = '/help/tos' target = '_blank'>Terms of Service</a>.</td>
                    <td><input value = 'Make my account!' type = 'submit' id = 'AcceptButton' /></td>
                </tr>
            </table>
        </form>
    </div>
</div>

<script type = 'text/javascript'>

    var validChars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    function validates (email) {
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        return reg.test(email) == false;
    }

    $(document).ready(function() {
        $('#Username').keyup(function() {
            var isBad = false;
            var str = $('#Username').val();
            for (var i = 0; i < str.length; i++) {
                if (validChars.indexOf(str.charAt(i)) == -1)
                    isBad = true;
            }

            if (isBad) {
                $(this).css('background-color', 'rgb(255, 230, 230)');
            } else {
                $(this).css('background-color', '');
            }
        });

        $('#Username').focus(function() {
            $('#UsernameAvailableLabel').text('');
        });

        $('#Username').blur(function() {
            var username = $('#Username').val();

            if (username.length == 0)
                return;

            $('#UsernameAvailableLabel').html(
                '<?= $html->image('cycle.gif', array('style' => 'margin-right: 2px; vertical-align: middle;')); ?>Is that username available?');

            $('#UsernameAvailableLabel').css('color', 'inherit');

            $.post(
                '<?= $this->base; ?>/users/usernameAvailable',
                {
                    username: username
                },
                function (result) {
                    if (result == -1) {
                        $('#UsernameAvailableLabel').text('Error.');
                    } else if (result == 0) {
                        $('#UsernameAvailableLabel').text('Nope, that username is taken :(');
                        $('#UsernameAvailableLabel').css('color', 'rgb(125, 0, 0)');
                    } else if (result == 1) {
                        $('#UsernameAvailableLabel').text('Yes, that username is available!');
                        $('#UsernameAvailableLabel').css('color', 'rgb(0, 125, 0)');
                    }
                }
            );
        });

        $('#Password').keyup(function() {
            var goodCharacters = 0;
            var str = $('#Password').val();

            if (str.length < 6) {
                $(this).css('background-color', 'rgb(255, 230, 230)');
            } else {
                $(this).css('background-color', '');
            }
        });

        $('#Email').keyup(function() {

            var str = $('#Email').val();
            var isBad = validates(str);

            if (isBad) {
                $(this).css('background-color', 'rgb(255, 230, 230)');
            } else {
                $(this).css('background-color', '');
            }
        });

        $('#CharacterName').keyup(function() {
            var isBad = false;
            var str = $('#CharacterName').val();
            for (var i = 0; i < str.length; i++) {
                if (validChars.indexOf(str.charAt(i)) == -1 && str.charAt(i) != ' ')
                    isBad = true;
            }

            if (isBad) {
                $(this).css('background-color', 'rgb(255, 230, 230)');
            } else {
                $(this).css('background-color', '');
            }
        });
    });
</script>
