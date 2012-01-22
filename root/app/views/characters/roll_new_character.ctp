<div>
    You've found a new character with the following stats:
</div>

<div style = 'position: relative; height: 140px;'>
    <div style = 'position: absolute; top: 15px; left: 0px'>
        <?= $html->image('sprites/Novice.png', array('class' => 'face-icon')); ?>
    </div>

    <table style = 'width: 225px; position: absolute; top: 5px; left: 120px;'>
        <tr>
            <td class = 'Td_Label'>Affinity</td>
            <td />
            <td style = 'text-align: right'><?= $ui->displayAffinitySprite($character['affinity']); ?></td>
        </tr>
        <tr>
            <td class = 'Td_Label'>STR</td>
            <td style = 'text-align: right'><?= $character['str']; ?></td>
            <td style = 'text-align: right'>+<img class = 'GrowthImg' src = 'data:image/png;base64,<?= base64_encode($character['growth_str_img']); ?>' /></td>
        </tr>
        <tr>
            <td class = 'Td_Label'>VIT</td>
            <td style = 'text-align: right'><?= $character['vit']; ?></td>
            <td style = 'text-align: right'>+<img class = 'GrowthImg' src = 'data:image/png;base64,<?= base64_encode($character['growth_vit_img']); ?>' /></td>
        </tr>
        <tr>
            <td class = 'Td_Label'>INT</td>
            <td style = 'text-align: right'><?= $character['int']; ?></td>
            <td style = 'text-align: right'>+<img class = 'GrowthImg' src = 'data:image/png;base64,<?= base64_encode($character['growth_int_img']); ?>' /></td>
        </tr>
        <tr>
            <td class = 'Td_Label'>LUK</td>
            <td style = 'text-align: right'><?= $character['luk']; ?></td>
            <td style = 'text-align: right'>+<img class = 'GrowthImg' src = 'data:image/png;base64,<?= base64_encode($character['growth_luk_img']); ?>' /></td>
        </tr>
        <tr>
            <td class = 'Td_Label'>Basic Rating</td>
            <td colspan = 2 style = 'text-align: right; font-weight: bold;'>
                <?
                    $sum = $character['growth_str'] + $character['growth_vit'] + $character['growth_int'] + $character['growth_luk'] * 0.8;
                    if ($sum >= 20)
                        echo 'Great';
                    else if ($sum >= 16)
                        echo 'Good';
                    else if ($sum >= 12)
                        echo 'Okay';
                    else
                        echo 'Awful';
                ?>
            </td>
        </tr>
        <tr>
            <td class = 'Td_Label'>Suggestion</td>
            <td colspan = 2 style = 'text-align: right; font-weight: bold;'>
                <?
                    if ($character['growth_str'] > $character['growth_int'] + .6)
                        echo 'Swordsman';
                    else if ($character['growth_int'] > $character['growth_str'] + .6)
                        echo 'Spellcaster';
                    else
                        echo 'Anything';
                ?>
            </td>
        </tr>
    </table>

    <div style = 'position: absolute; top: 5px; left: 350px'>
        <div style = 'width: 350px'>
            Want to keep him? Give him a name! Choose wisely, because you won't be able to change his name later.
        </div>


        <?= $form->create('Character', array('action' => 'new_character'));?>
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

            <div class="input text">
                <label for="CharacterName">Name</label>
                <input name="data[Character][name]" maxlength="<?= CHARACTER_NAME_MAX_CHARS; ?>" value="" id="CharacterName" type="text">
                <span id = 'InvalidCharacterNameLabel' style = 'display: none; font-size: 80%; color: rgb(200, 0, 0)'>
                    Character name invalid.
                </span>
            </div>

            <div class="submit">
                <input value = "Accept (<?= CHARACTER_KEEP_COST; ?> yb)" style = 'width: 200px' type="submit" id = 'AcceptButton'>
            </div>
        </form>
    </div>
</div>

<script type = 'text/javascript'>

    var validCharacters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ";

    function checkValidity() {
            var isBad = false;
            var str = $('#CharacterName').val();
            for (var i = 0; i < str.length; i++) {
                if (validCharacters.indexOf(str.charAt(i)) == -1)
                    isBad = true;
            }

            var isEmpty = (str.length == 0);

            if (isEmpty) {
                $('#AcceptButton').attr('disabled', 'disabled');
            } else {
                if (isBad) {
                    $('#InvalidCharacterNameLabel').fadeIn(100);
                    $('#AcceptButton').attr('disabled', 'disabled');
                } else {
                    $('#InvalidCharacterNameLabel').fadeOut(100);
                    $('#AcceptButton').attr('disabled', '');
                }
            }
        }

    $(document).ready(function() {
        checkValidity();
        $('#CharacterName').keyup(checkValidity);
    });
</script>
