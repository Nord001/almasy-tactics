<style type = 'text/css'>
    .Bad {
        color: rgb(255, 0, 0);
    }

    .Good {
        color: rgb(0, 150, 0);
    }
</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        Guilds | New Guild
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        You can create a guild here. Guilds allow your to play Almasy as part of a group of people. Managing a guild is expensive, so only do it if you
        have a lot of money. Creating a guild costs <?= number_format(GUILD_CREATION_COST); ?> yb.
        <?= $form->create('Guild', array('action' => 'create'));?>
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

            <div>
                <label for = 'GuildName' style = 'font-size: 120%'>Name</label>
                <input name = 'data[Guild][name]' type = 'text' maxlength = '<?= GUILD_NAME_MAX_CHARS; ?>' value = '<?= !empty($this->data) ? $this->data['Guild']['name'] : ''; ?>' id = 'GuildName' />
                <span id = 'InvalidGuildNameLabel' style = 'display: none; font-size: 80%; color: rgb(200, 0, 0)'>
                    Guild name invalid.
                </span>
            </div>

            <input type = 'submit' value = 'Submit' id = 'GuildCreateSubmitButton' style = 'width: 200px' />
        </form>
    </div>
</div>

<script type = 'text/javascript'>
    var validCharacters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ";

    $(document).ready(function() {
        // Invalid character prevention
        $('#GuildName').keyup(function() {
            var isBad = false;
            var str = $('#GuildName').val();
            for (var i = 0; i < str.length; i++) {
                if (validCharacters.indexOf(str.charAt(i)) == -1)
                    isBad = true;
            }

            if (isBad) {
                $(this).css('background-color', 'rgb(255, 230, 230)');
                $('#InvalidGuildNameLabel').fadeIn(100);
                $('#SaveGuildSubmit').attr('disabled', 'disabled');
            } else {
                $(this).css('background-color', '');
                $('#InvalidGuildNameLabel').fadeOut(100);
                $('#SaveGuildSubmit').attr('disabled', '');
            }
        });
    });
</script>
