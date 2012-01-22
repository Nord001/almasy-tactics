<? define('NUM_VIEWS', 4); ?>

<style type = 'text/css'>

.CharacterBox {
    <? GradientBackground(array(
        array(0, 'hsl(37, 70%, 85%)'),
        array(1, 'hsl(37, 70%, 70%)')
    )); ?>
    background-color: hsl(37, 70%, 85%);

    border: 1px solid;
    height: 70px;
    position: relative;
}

.PromoteBox {
    <? GradientBackground(array(
        array(0, 'hsl(113, 60%, 75%)'),
        array(1, 'hsl(113, 60%, 60%)')
    )); ?>
    background-color: hsl(113, 60%, 70%);
}

.HoverBox {
    <? GradientBackground(array(
        array(0, 'hsl(0, 38%, 85%)'),
        array(1, 'hsl(0, 38%, 65%)')
    )); ?>
    background-color: hsl(0, 38%, 84%);
}

</style>

<div class = 'PageDiv'>
    <div class = 'PageTitle'>
        Your Army

        <? if (false): ?>
        <? $ui->displayHelpTooltip(
                'army-main',
                'This is the army page. Blah blah blah bh!OIJOIEJFOIWFJUWOWIJ SFJIOJDFijf' .
                'ijOFIJFOj FOSIDMONV OGU$HNOLGWGH OGIRJG MGLWIRJGN ORIJRWOFJIGOH'); ?>
        <? endif; ?>

        <div style = 'position: absolute; top: 8px; right: 10px; font-size: 70%'>
            <input type = 'button' value = 'Recruit!' id = 'RecruitButton' href = '<?= $html->url(array('controller' => 'characters', 'action' => 'new_character')); ?>' />
            <script type = 'text/javascript'>
                $('#RecruitButton').linkButton();
            </script>
        </div>
    </div>

    <div class = 'PageContent'>
        <div style = 'text-align: center; margin-bottom: 5px;'>
            <a href = '#' id = 'Link_PrevView'>&lt;&lt;</a>
            <div style = 'display: inline-block; text-align: center; font-weight: bold; width: 150px;' id = 'ViewName'>Standard (1/<?= NUM_VIEWS; ?>)</div>
            <a href = '#' id = 'Link_NextView'>&gt;&gt;</a>
        </div>

        <table style = 'width: 100%'>
            <? for ($i = 0; $i < count($characters); $i += 3): ?>
                <tr>
                    <? for ($index = $i; ($index <= $i + 2) && ($index < count($characters)); $index++): ?>
                        <? $character = $characters[$index]; ?>
                        <td style = 'width: 300px'>
                            <? $promoteClass = @$character['Character']['can_promote'] ? 'PromoteBox' : ''; ?>
                            <div class = 'BorderDiv'>
                                <div class = 'CharacterBox <?= $promoteClass; ?>'>
                                    <div style = 'position: absolute; top: 3px; left: 3px'>
                                        <?
                                            $image = $ui->getFaceIcon($character['CClass']['face_icon']);
                                            echo $html->image($image, array(
                                                'width' => '60',
                                                'class' => 'face-icon'
                                            ));
                                        ?>
                                    </div>

                                    <div style = 'margin-left: 70px; position: relative; height: 100%'>
                                        <div class = 'View' viewName = 'Standard' viewNum = '1'>

                                            <? if (@$character['Character']['can_promote']): ?>
                                                <div style = 'position: absolute; right: 38px; bottom: 11px; font-size: 80%; font-style: italic;'>
                                                    Promotion Available!
                                                </div>
                                            <? endif; ?>

                                            <div style = 'border-bottom: 1px dotted; margin-right: 5px;'>
                                                <div style = 'width: 180px; overflow: hidden'>
                                                    <?= $html->link2($character['Character']['name'], array('controller' => 'characters', 'action' => 'view', $character['Character']['id'])); ?>
                                                </div>
                                                <span style = 'position: absolute; right: 3px; top: 0px;'>
                                                    Lv. <?= $character['Character']['level']; ?>
                                                </span>
                                            </div>

                                            <span style = 'position: absolute; left: 0px; top: 21px;'>
                                                <?= $character['CClass']['name']; ?>
                                            </span>

                                            <span style = 'position: absolute; right: 3px; top: 24px;'>
                                                <?= $ui->displayAffinitySprite($character['Character']['affinity']); ?>
                                            </span>

                                            <? if ($character['Character']['level'] < CHARACTER_MAX_LEVEL): ?>
                                                <div style = 'position: absolute; left: 1px; bottom: 0px; font-size: 80%;'>
                                                    EXP
                                                </div>
                                                <div style = 'position: absolute; left: 30px; bottom: 4px;'>
                                                    <?
                                                        $width = 195;
                                                        $percent = intval($character['Character']['exp'] / $character['Character']['total_exp_to_next_level'] * 100);
                                                    ?>
                                                    <div style = 'width: <?= $width; ?>px; border: 1px solid; height: 5px; background-color: rgb(205, 205, 220)'>
                                                        <? if ($percent > 0): ?>
                                                            <div style = 'width: <?= $percent ?>%; height: 5px; background-color: rgb(10, 10, 150); border-right: 1px solid;'>
                                                            </div>
                                                        <? endif; ?>
                                                    </div>
                                                </div>
                                            <? endif; ?>
                                        </div>
                                        <div class = 'View' viewName = 'Stats' viewNum = '2' style = 'display: none'>
                                            <div>
                                                <?= $html->link2($character['Character']['name'], array('controller' => 'characters', 'action' => 'view', $character['Character']['id'])); ?>
                                            </div>
                                            <table style = 'width: 100%; font-size: 80%; height: 45px;'>
                                                <?
                                                    $stats = array(
                                                        'str',
                                                        'vit',
                                                        'int',
                                                        'luk',
                                                    );
                                                ?>
                                                <? for ($statRow = 0; $statRow < 2; $statRow++): ?>
                                                    <tr>
                                                        <? for ($statCol = 0; $statCol < 2; $statCol++): ?>
                                                            <? $stat = $stats[$statRow * 2 + $statCol]; ?>
                                                            <? $change = $character['Character']['Stats'][$stat] - $character['Character'][$stat]; ?>

                                                            <td style = 'font-weight: bold; text-align: left; width: 30px;'>
                                                                <?= strtoupper($stat); ?>
                                                            </td>
                                                            <td style = 'text-align: center; width: 30px;'>
                                                                <? $style = $change != 0 ? 'color: rgb(95, 95, 245);' : ''; ?>
                                                                <span style = '<?= $style; ?>'>
                                                                    <?= $ui->displayStat($character['Character']['Stats'][$stat]); ?>
                                                                </span>
                                                            </td>
                                                            <td style = 'text-align: center; width: 30px;'>
                                                                <?= $ui->displayGrowth($character['Character']['growth_' . $stat]); ?>
                                                            </td>
                                                        <? endfor; ?>
                                                    </tr>
                                                <? endfor; ?>
                                            </table>
                                        </div>
                                        <div class = 'View' viewName = 'Substats 1' viewNum = '3' style = 'display: none'>
                                            <div style = 'width: 150px; overflow: hidden'>
                                                <?= $html->link2($character['Character']['name'], array('controller' => 'characters', 'action' => 'view', $character['Character']['id'])); ?>
                                            </div>
                                            <span style = 'position: absolute; right: 3px; top: 0px;'>
                                                HP: <?= intval($character['Character']['Stats']['maxHp']); ?>
                                            </span>
                                            <table style = 'width: 100%; font-size: 70%; height: 45px;'>

                                                <tr>
                                                    <td>Damage</td>
                                                    <td>
                                                        <?
                                                            $damageString = '';
                                                            if ($character['Character']['Stats']['meleeAtk'] != $character['Character']['Stats']['rangedAtk'])
                                                                $damageString = sprintf('%s/%s', intval($character['Character']['Stats']['meleeAtk']), intval($character['Character']['Stats']['rangedAtk']));
                                                            else
                                                                $damageString = intval($character['Character']['Stats']['meleeAtk']);

                                                            $strikes = $character['Character']['Stats']['numStrikes'];
                                                            if ($strikes < 1)
                                                                $strikes = 1;
                                                            printf('%s x %s', $damageString, $strikes);
                                                        ?>
                                                    </td>
                                                    <td>Range</td>
                                                    <td>
                                                        <?
                                                            if ($character['Character']['Stats']['minRange'] == $character['Character']['Stats']['maxRange'])
                                                                echo $character['Character']['Stats']['minRange'];
                                                            else
                                                                printf('%s - %s', $character['Character']['Stats']['minRange'], $character['Character']['Stats']['maxRange']);
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Phys. Def.</td>
                                                    <td>
                                                        <?= intval($character['Character']['Stats']['physReduction']); ?>%
                                                        <?= sprintf("%+d", $character['Character']['Stats']['physDefense']); ?>
                                                    </td>
                                                    <td>Mag. Def.</td>
                                                    <td>
                                                        <?= intval($character['Character']['Stats']['magReduction']); ?>%
                                                        <?= sprintf("%+d", $character['Character']['Stats']['magDefense']); ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class = 'View' viewName = 'Substats 2' viewNum = '4' style = 'display: none'>
                                            <div style = 'width: 180px; overflow: hidden'>
                                                <?= $html->link2($character['Character']['name'], array('controller' => 'characters', 'action' => 'view', $character['Character']['id'])); ?>
                                            </div>
                                            <table style = 'width: 100%; font-size: 80%; height: 45px;'>
                                                <tr>
                                                    <td>Critical</td>
                                                    <td><?= intval($character['Character']['Stats']['crit']); ?>%</td>

                                                    <td>Dodge</td>
                                                    <td><?= intval($character['Character']['Stats']['luckyDodge']); ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td>HP Regen</td>
                                                    <td><?= intval($character['Character']['Stats']['hpRegen']); ?>% / Round</td>

                                                    <td>Speed</td>
                                                    <td><?= intval($character['Character']['Stats']['speed']); ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    <? endfor; ?>
                </tr>
            <? endfor; ?>
        </table>
    </div>
</div>

<script type = 'text/javascript'>
    $(document).ready(function() {
        var curView = 1;
        var numViews = <?= NUM_VIEWS; ?>;

        function UpdateView () {
            $('.View').hide();

            var showView = $('.View[viewNum=' + curView + ']');
            showView.show();
            $('#ViewName').text(showView.attr('viewName') + ' (' + curView + '/' + numViews + ')');
        }

        $('#Link_NextView').click(function() {
            curView++;
            if (curView > numViews)
                curView = numViews;
            UpdateView();

        });

        $('#Link_PrevView').click(function() {
            curView--;
            if (curView < 1)
                curView = 1;
            UpdateView();
        });

        // Setup hover
        $('.CharacterBox').hover(
            function() {
                $(this).addClass('HoverBox');
                $('body').css('cursor', 'pointer');
            },
            function() {
                $(this).removeClass('HoverBox');
                $('body').css('cursor', 'auto');
            }
        );

        $('.CharacterBox').click(function() {
            window.location = $(this).find('a').attr('href');
        });
    });
</script>
