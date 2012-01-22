<style type = 'text/css'>
</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Army', array('controller' => 'army', 'action' => 'index')); ?> |
        <?= $html->link2($character['Character']['name'], array('controller' => 'characters', 'action' => 'view', $character['Character']['id'])); ?> |
        Change Name
    </div>

    <div class = 'PageContent'>
        <div style = 'margin-bottom: 5px'>
            You can change this character<?= "'"; ?>s name only once. Choose wisely!
        </div>

        <form id = 'Form_ChangeName' method = 'POST' action = '<?= h($this->here); ?>'>
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
            <?= $form->hidden('character_id', array('name' => 'data[Character][id]', 'value' => $character['Character']['id'])); ?>
            <input
                name = "data[Character][name]"
                maxlength = "<?= CHARACTER_NAME_MAX_CHARS; ?>"
                value = "<?= !empty($this->data) ? $this->data['Character']['name'] : $character['Character']['name']; ?>"
                id = "CharacterName"
                type = "text"
            >
            <input value = 'Rename!' type = 'submit' id = 'AcceptButton' />
        </form>
    </div>
</div>

<script type = 'text/javascript'>
    var validChars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    $(document).ready(function() {
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