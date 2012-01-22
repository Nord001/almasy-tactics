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
        <?= $html->link2('Formations', array('controller' => 'formations', 'action' => 'index')); ?> |
        New Formation
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        <?= $form->create('Formation', array('action' => 'create'));?>
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

            <div>
                <label for = 'FormationName' style = 'font-size: 120%'>Name</label>
                <input name = 'data[Formation][name]' type = 'text' maxlength = '<?= FORMATION_NAME_MAX_CHARS; ?>' value = '<?= !empty($this->data) ? $this->data['Formation']['name'] : ''; ?>' id = 'FormationName' />
                <span id = 'InvalidFormationNameLabel' style = 'display: none; font-size: 80%; color: rgb(200, 0, 0)'>
                    Formation name invalid.
                </span>
            </div>

            <div style = 'margin-top: 20px'>
                <input type = 'hidden' name = 'data[Formation][can_spar]' value = '0' />
                <?
                    $canSpar = !empty($this->data) ? $this->data['Formation']['can_spar'] : 1;
                    $checked = $canSpar ? 'checked' : '';
                ?>
                <input type = 'checkbox' name = 'data[Formation][can_spar]' class = 'inline'  value = '1' <?= $checked; ?> id = 'FormationCanSpar' />
                <label for = 'FormationCanSpar'>Available for Sparring</label>
            </div>


            <div style = 'margin-top: 20px; font-size: 120%; margin-bottom: 3px;'>
                Membership (<span id = 'Span_CharCount'>0</span>/7)
            </div>

            <table>
                <? define('TABLE_WIDTH', 4); ?>

                <? for ($i = 0; $i < ceil(count($characters) / TABLE_WIDTH); $i++): ?>
                    <tr>
                        <? for ($j = 0; $j < TABLE_WIDTH; $j++): ?>
                            <td class = 'CharacterBox' style = 'border: 1px solid; width: 160px; height: 30px; padding: 5px;'>
                                <? $index = $i * TABLE_WIDTH + $j; ?>
                                <? if ($index < count($characters)): ?>
                                    <? $character =& $characters[$index]; ?>

                                    <? $id = 'Checkbox' . $index; ?>

                                    <input
                                        type = 'checkbox'
                                        name = 'data[Formation][characterIds][]'
                                        value = '<?= $character['Character']['id']; ?>'
                                        class = 'inline'
                                        <?= !empty($this->data) && isset($this->data['Formation']['characterIds']) && in_array($character['Character']['id'], $this->data['Formation']['characterIds']) ? 'checked' : ''; ?>
                                        id = '<?= $id; ?>'
                                    />
                                    <div>
                                        <?
                                            $icon = $ui->getFaceIcon($character['CClass']['face_icon']);
                                            echo $html->image($icon, array('style' => 'width: 20px; border: 1px solid'));
                                        ?>
                                        <?= $character['Character']['name']; ?>
                                    </div>

                                <? endif; ?>
                            </td>
                        <? endfor; ?>
                    </tr>
                <? endfor; ?>
            </table>

            <div style = 'color: rgb(200, 0, 0)'>Warning: If you later change the characters in your formation, your formation's reputation will be reset!</div>

            <input type = 'submit' value = 'Submit' id = 'FormationCreateSubmitButton' style = 'width: 200px' />
        </form>
    </div>
</div>

<script type = 'text/javascript'>
    function UpdateCharCount () {
        var numChecked = $('td.CharacterBox input[type = checkbox]:checked').length;
        var span = $('#Span_CharCount');

        span.removeClass('Bad');
        span.removeClass('Good');
        span.text(numChecked);

        if (numChecked == 0 || numChecked > 7)
            span.addClass('Bad');
        else if (numChecked == 7)
            span.addClass('Good');
    }

    var validCharacters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ";

    function UpdateCheckboxes () {
        $('td.CharacterBox').each(function() {
            var checkbox = $(this).children('input').get(0);
            var checked = $(checkbox).is(':checked');
            if (checked) {
                $(this).css('background-color', 'rgb(220, 255, 220)');
            } else {
                $(this).css('background-color', 'inherit');
            }
        });
    }

    // Disables submit if there's too many in the formation
    function UpdateSubmitButton () {
        var maxCharacters = <?= MAX_CHARACTERS_PER_FORMATION; ?>;
        var numChecked = $('td.CharacterBox input[type = checkbox]:checked').length;
        var submitButton = $('#FormationCreateSubmitButton');
        if (numChecked > maxCharacters) {
            submitButton.attr('disabled', 'disabled');
            submitButton.attr('value', 'Too many characters!');
        } else if (numChecked == -1) {
            submitButton.attr('disabled', 'disabled');
            submitButton.attr('value', 'Select characters!');
        } else {
            submitButton.attr('disabled', '');
            submitButton.attr('value', 'Save Formation');
        }
    }

    function Update () {
        UpdateCheckboxes();
        UpdateSubmitButton();
        UpdateCharCount();
    }

    $(document).ready(function() {
        Update();

        $('.CharacterBox input').click(function(event) {
            event.stopPropagation();
            Update();
        });

        // Interactivity
        $('td.CharacterBox').click(function(event) {
            var checkbox = $(this).find('input');

            if (checkbox.attr('checked')) {
                checkbox.removeAttr('checked');
            } else {
                checkbox.attr('checked', 'checked');
            }

            Update();
        });

        $('td.CharacterBox').hover(
            function() {
                $('body').css('cursor', 'pointer');
            },
            function() {
                $('body').css('cursor', 'auto');
            }
        );

        // Invalid character prevention
        $('#FormationName').keyup(function() {
            var isBad = false;
            var str = $('#FormationName').val();
            for (var i = 0; i < str.length; i++) {
                if (validCharacters.indexOf(str.charAt(i)) == -1)
                    isBad = true;
            }

            if (isBad) {
                $(this).css('background-color', 'rgb(255, 230, 230)');
                $('#InvalidFormationNameLabel').fadeIn(100);
                $('#SaveFormationSubmit').attr('disabled', 'disabled');
            } else {
                $(this).css('background-color', '');
                $('#InvalidFormationNameLabel').fadeOut(100);
                $('#SaveFormationSubmit').attr('disabled', '');
            }
        });
    });
</script>
