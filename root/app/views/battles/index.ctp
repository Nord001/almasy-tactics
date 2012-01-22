<style type = 'text/css'>
    #SparDiv, #BattleDiv {
        width: 500px;
    }

    #SparDiv {
        position: absolute;
        right: 15px;
        top: 340px;
    }

    #SparDivContent, #BattleDivContent {
        border: 1px solid rgb(75, 75, 75);
        padding: 7px;
    }

    #SparDivContent {
        background-color: hsl(120, 100%, 80%);
        <? GradientBackground(array(
            array(0, 'hsl(120, 60%, 75%)'),
            array(1, 'hsl(120, 60%, 65%)')
        )); ?>
    }

    #BattleDiv {
        position: absolute;
        top: 30px;
        left: 15px;
    }

    #BattleDivContent {
        background-color: hsl(60, 100%, 80%);
        <? GradientBackground(array(
            array(0, 'hsl(60, 60%, 80%)'),
            array(1, 'hsl(60, 60%, 65%)')
        )); ?>
    }

    .BattleHeader {
        font-size: 140%;
        border-bottom: 1px dotted;
        margin-bottom: 5px;
    }
</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        War Room

        <div style = 'position: absolute; top: 8px; right: 10px; font-size: 70%'>
            <input type = 'button' value = 'Battle History' id = 'HistoryButton' />
            <script type = 'text/javascript'>
                $('#HistoryButton').click(function(event) {
                    event.preventDefault();

                    window.location = '<?= $html->url(array('controller' => 'battles', 'action' => 'history')); ?>';
                });
            </script>
        </div>
    </div>

    <div class = 'PageContent'>

        <div style = 'height: 600px'></div>

        <div id = 'SparDiv' class = 'BorderDiv'>
            <div id = 'SparDivContent'>
                <div class = 'BattleHeader'>
                    Spar!
                </div>
                Maybe you don<?= "'"; ?>t want to battle for real. Maybe you just want to have fun and see who would win in
                a nice friendly fight. Sparring consumes no battles, and you gain no experience, yuanbao, or
                reputation regardless of the outcome.

                <?= $form->create('Battle', array('action' => 'fight'));?>
                <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
                <input type = 'hidden' name = 'data[Battle][battle_type]' value = 'spar' />

                <div style = 'width: 450px'>
                    <div style = 'float: left; width: 200px;'>
                        <div class = "input text">
                            <label for = "FormationName">Who do you want to spar with?</label>
                            <input name = "data[Battle][defender_name]" maxlength = "<?=  FORMATION_NAME_MAX_CHARS + 20; ?>" value = "" id = "DefenderName" type = "text" autoselect = 0>
                        </div>
                    </div>

                    <div style = 'float: right; width: 200px;'>
                        <div class = "input text">
                            <label for = 'AttackerId'>What formation do you want to use?</label>
                            <select name = "data[Battle][attacker_id]" id = "AttackerId" style = 'font-size: 90%;'>
                                <? foreach ($activeFormations as $formation): ?>
                                    <option value = '<?= $formation['Formation']['id']; ?>'>
                                        <?= $formation['Formation']['name']; ?>
                                    </option>
                                <? endforeach; ?>

                                <? foreach ($inactiveFormations as $formation): ?>
                                    <option value = '<?= $formation['Formation']['id']; ?>'>
                                        <?= $formation['Formation']['name']; ?>
                                    </option>
                                <? endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style = 'clear: both;'></div>
                </div>

                <div class = "submit">
                    <input value = "Fight!" type = "submit" />
                </div>

                </form>
            </div>
        </div>

        <div style = 'position: absolute; top: 20px; right: 70px'>
            <?= $html->image('warrior.png'); ?>
        </div>

        <div id = 'BattleDiv' class = 'BorderDiv'>
            <div id = 'BattleDivContent'>
                <div class = 'BattleHeader'>
                    Battle!
                </div>
                Face another formation in real battle, down to the last survivor.
                Your reputation is on the stake, and you will gain riches, experience, and reputation points if you win.
                Battling consumes one battle. Battling always uses your active formation.

                <div style = 'margin-top: 10px; font-size: 100%;'>
                    Entering battle with:
                    <select name = "data[Battle][attacker_id]" id = "MatchmakeFormationId" style = 'font-size: 90%; width: 300px;'>
                        <? foreach ($activeFormations as $formation): ?>
                            <option value = '<?= $formation['Formation']['id']; ?>' <?= $formation['Formation']['id'] == $a_user['User']['last_battle_formation_id'] ? 'selected' : ''; ?>>
                                <?= $formation['Formation']['name']; ?>,
                                reputation <?= $formation['Formation']['reputation']; ?>,
                                bounty <?= $formation['Formation']['bounty']; ?>
                            </option>
                        <? endforeach; ?>
                    </select>
                </div>

                <input
                    <?= $a_user['User']['num_battles'] > 0 ? '' : 'disabled'; ?>
                    value = "<?= $a_user['User']['num_battles'] > 0 ? 'To War!' : 'You need battles.'; ?>"
                    id = 'MatchmakeButton'
                    type = "button"
                    style = 'margin-top: 2px; height: 30px; width: 300px; font-size: 100%'
                    href = '<?= $html->url(array('controller' => 'battles', 'action' => 'matchmake')); ?>' />
            </div>
        </div>

        <div style = 'position: absolute; top: 300px; left: 80px'>
            <?= $html->image('mastersmith.png'); ?>
        </div>
    </div>
</div>

<script type = 'text/javascript'>
    $(document).ready(function() {
        $('#MatchmakeButton').click(function(event) {
            event.preventDefault();
            var url = $(this).attr('href');
            url = url + '/' + $('#MatchmakeFormationId').val();
            window.location = url;
        });
    });
</script>
