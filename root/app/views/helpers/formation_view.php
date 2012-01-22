<?

class FormationViewHelper extends AppHelper {

    var $helpers = array('Html', 'Ui');

    function displayFormation ($formation, $active) { ?>

        <div class = 'FormationDiv BorderDiv'>
            <div class = 'FormationInnerDiv <?= $active ? 'Active' : ''; ?>'>
                <div class = 'FormationHeader' style = 'height: 20px'>
                    <div style = 'width: 160px; height: 20px;' class = 'ShrinkText'>
                        <?= $formation['Formation']['name']; ?>
                    </div>

                    <div style = 'position: absolute; top: 5px; right: 125px'>
                        Rep: <?= $formation['Formation']['reputation']; ?>
                    </div>

                    <? if ($active): ?>
                        <div style = 'position: absolute; top: 5px; right: 225px'>
                            Bounty: <?= $formation['Formation']['bounty']; ?>
                        </div>
                    <? endif; ?>

                    <div style = 'position: absolute; top: 5px; right: 5px; text-align: right; width: 120px;' class = 'ShrinkText'>
                        W<?= $formation['Formation']['battles_won']; ?> - L<?= $formation['Formation']['battles_lost']; ?>
                    </div>
                </div>

                <div class = 'FormationContent'>
                    <div style = 'position: absolute; left: 2px; top: 3px; font-size: 80%;'>
                        <div style = 'position: relative'>
                            <? $n = 0; ?>
                            <? foreach ($formation['Characters'] as $character): ?>
                                <div style = 'left: <?= $n++ * 65; ?>px; position: absolute'>
                                    <div style = 'position: relative; width: 60px; text-align: center;'>
                                        <div style = 'position: absolute; top: 48px; left: 2px; width: 60px; height: 14px; overflow: hidden; font-size: 8pt;'>
                                            <?= $character['CClass']['face_icon'] == '' ? $character['CClass']['name'] : ''; ?>
                                        </div>
                                        <div style = 'position: absolute; top: 0px; right: 0px; font-weight: bold; font-size: 8pt'>
                                            <?= $character['Character']['level']; ?>
                                        </div>
                                        <?
                                            $icon = $this->Ui->getFaceIcon($character['CClass']['face_icon']);
                                            echo $this->Html->image($icon, array('style' => 'width: 60px; border: 1px solid'));
                                        ?>
                                        <div style = 'text-align: center; width: 60px; height: 25px; overflow: hidden; font-size: 14px;' class = 'ShrinkText'>
                                            <?= $character['Character']['name']; ?>
                                        </div>
                                    </div>
                                </div>
                            <? endforeach; ?>
                        </div>
                    </div>

                    <div formationId = '<?= $formation['Formation']['id']; ?>' style = 'position: absolute; top: 83px; left: 2px;'>
                        <input type = 'button' value = 'Organize' class = 'OrganizeButton' />
                        <input type = 'button' value = 'Edit' class = 'EditButton' />
                        <input type = 'button' value = 'Strategy' class = 'StrategyButton' />
                        <? if(false): ?>
                            <input type = 'button' value = 'Missions' class = 'MissionsButton' />
                        <? endif; ?>
                    </div>
                </div>
            </div>
        </div>

    <? }
}