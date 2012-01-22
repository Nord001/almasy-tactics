<style type = 'text/css'>
    .Td_Label {
        font-weight: bold;
    }

    .GrowthImg {
        vertical-align: middle;
    }
</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Army', array('controller' => 'army', 'action' => 'index')); ?> |
        Recruit New Character
    </div>

    <div class = 'PageContent' style = 'position: relative; height: 350px;'>

        <input type = 'button' value = 'Recruit New Character (<?= $rollCost; ?> yb)' id = 'RollCharacterButton' style = 'font-size: 120%; height: 40px' />
        <?= $html->image('cycle.gif', array('style' => 'margin-right: 2px; vertical-align: middle; display: none;', 'id' => 'Img_RecruitLoading')); ?>

        <div id = 'CharacterPane' style = 'margin-bottom: 25px'>
        </div>

        <div style = 'position: absolute; right: 15px; top: 15px'>
            <?= $html->image('novice_small.png'); ?>
        </div>
    </div>
</div>

<script type = 'text/javascript'>
    function UpdateButton() {
        if (GetMoney() < <?= $rollCost; ?>)
            $('#RollCharacterButton').attr('disabled', 'disabled');
        else
            $('#RollCharacterButton').attr('disabled', '');
    }

    $(document).ready(function() {
        UpdateButton();
        $('#RollCharacterButton').click(function() {
            $('#Img_RecruitLoading').show();
            RunCaptcha(function() {
                $.post(
                    '/characters/roll_new_character',
                    null,
                    function (data) {
                        $('#Img_RecruitLoading').hide();
                        if (data == '<?= AJAX_ERROR_CODE; ?>') {
                            alert('An error has occurred. Sorry!');
                            return;
                        }

                        if (data == '<?= AJAX_INSUFFICIENT_FUNDS; ?>') {
                            alert('You don\'t have enough money.');
                            return;
                        }
                        $('#CharacterPane').html(data);
                        UpdateMoneyDisplay(UpdateButton);
                    }
                );
            });
        });
    });
</script>
