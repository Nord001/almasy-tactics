<style type = 'text/css'>
    .Bad {
        color: rgb(255, 0, 0);
    }

    .Good {
        color: rgb(0, 150, 0);
    }

    .Unavailable {
        background-color: rgb(128, 128, 128);
    }
</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Formations', array('controller' => 'formations', 'action' => 'index')); ?> |
        Editing <?= $formation['Formation']['name']; ?>
    </div>

    <form id = 'Form_DeleteFormation' method = 'POST' action = '<?= $html->url(array('controller' => 'formations', 'action' => 'delete')); ?>'>
        <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
        <input type = 'hidden' name = 'data[Formation][id]' value = '<?= $formation['Formation']['id']; ?>' />
    </form>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        <form id = 'FormationEditForm' method = 'POST' action = '<?= h($this->here); ?>'>
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>

            <input type = 'button' id = 'DeleteFormationButton' value = 'Delete Formation' class = 'AlarmButton' />

            <input type = 'hidden' name = 'data[Formation][id]' value = '<?= $formation['Formation']['id']; ?>' />

            <div class = 'input text'>
                <label for = 'FormationName' style = 'font-size: 120%'>Name</label>
                <input name = 'data[Formation][name]' type = 'text' maxlength = '<?= FORMATION_NAME_MAX_CHARS; ?>' value = '<?= !empty($this->data) ? $this->data['Formation']['name'] : $formation['Formation']['name']; ?>' id = 'FormationName' />
                <span id = 'InvalidFormationNameLabel' style = 'display: none; font-size: 80%; color: rgb(200, 0, 0)'>
                    Formation name invalid.
                </span>
            </div>

            <div style = 'margin-top: 20px'>
                <input type = 'hidden' name = 'data[Formation][can_spar]' value = '0' />
                <?
                    $canSpar = !empty($this->data) ? $this->data['Formation']['can_spar'] : $formation['Formation']['can_spar'];
                    $checked = $canSpar ? 'checked' : '';
                ?>
                <input type = 'checkbox' name = 'data[Formation][can_spar]' class = 'inline'  value = '1' <?= $checked; ?> id = 'FormationCanSpar' />
                <label for = 'FormationCanSpar'>Available for Sparring</label>

                <input type = 'hidden' name = 'data[Formation][active]' value = '0' />
                <?
                    $active = !empty($this->data) ? $this->data['Formation']['active'] : $formation['Formation']['active'];
                    $checked = $active ? 'checked' : '';
                ?>
                <input type = 'checkbox' name = 'data[Formation][active]' class = 'inline'  value = '1' <?= $checked; ?> id = 'FormationActive' />
                <label for = 'FormationActive'>Active for Battle</label>
            </div>


            <div style = 'margin-top: 20px; font-size: 120%; margin-bottom: 3px;'>
                Membership (<span id = 'Span_CharCount'>0</span>/7)
            </div>

            <table>
                <? define('TABLE_WIDTH', 4); ?>

                <? $characterIdsInFormation = !empty($this->data) ? $this->data['Formation']['characterIds'] : $characterIdsInFormation; ?>
                <? for ($i = 0; $i < ceil(count($characters) / TABLE_WIDTH); $i++): ?>
                    <tr>
                        <? for ($j = 0; $j < TABLE_WIDTH; $j++): ?>
                            <? $index = $i * TABLE_WIDTH + $j; ?>
                            <? if ($index < count($characters)): ?>
                                <? $character =& $characters[$index]; ?>

                                <? $id = 'Checkbox' . $index; ?>

                            <? $isAvailable = ($character['Character']['formation_id'] === false || $character['Character']['formation_id'] == $formation['Formation']['id']); ?>
                            <td class = 'CharacterBox <?= $isAvailable ? '' : 'Unavailable'; ?>' style = 'border: 1px solid; width: 200px; height: 30px; padding: 5px;'>
                                    <? if ($isAvailable): ?>
                                        <input
                                            type = 'checkbox'
                                            name = 'data[Formation][characterIds][]'
                                            value = '<?= $character['Character']['id']; ?>'
                                            <?= !empty($characterIdsInFormation) && in_array($character['Character']['id'], $characterIdsInFormation) ? 'checked' : ''; ?>
                                            class = 'inline'
                                            id = '<?= $id; ?>'
                                        />
                                    <? endif; ?>
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

            <div style = 'color: rgb(200, 0, 0)'>Warning: If you change the characters in your formation, your formation's reputation and bounty will be reset!</div>

            <input type = 'submit' id = 'SaveFormationSubmit' value = 'Save Formation' style = 'width: 200px' />
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
            if (checkbox) {
                var checked = $(checkbox).is(':checked');
                if (checked) {
                    $(this).css('background-color', 'rgb(220, 255, 220)');
                } else {
                    $(this).css('background-color', 'inherit');
                }
            }
        });
    }

    // Disables submit if there's too many in the formation
    function UpdateSubmitButton () {
        var maxCharacters = <?= MAX_CHARACTERS_PER_FORMATION; ?>;
        var numChecked = $('td.CharacterBox input[type = checkbox]:checked').length;
        var submitButton = $('#SaveFormationSubmit');
        if (numChecked > maxCharacters) {
            submitButton.attr('disabled', 'disabled');
            submitButton.attr('value', 'Too many characters!');
        } else if (numChecked == 0) {
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
        var chars = $('td.CharacterBox').not('.Unavailable');
        chars.click(function(event) {
            var checkbox = $(this).find('input');

            if (checkbox.attr('checked')) {
                checkbox.removeAttr('checked');
            } else {
                checkbox.attr('checked', 'checked');
            }

            Update();
        });

        chars.hoverPointer();

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

        $('#DeleteFormationButton').click(function(event) {
            event.preventDefault();

            if (confirm('Are you sure you want to delete this formation?')) {
                $('#Form_DeleteFormation').submit();
            }
        });
    });
</script>