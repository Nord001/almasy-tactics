<style type = 'text/css'>

.Div_Panel {
    margin-bottom: 10px;
}

.Div_InnerPanel {
    border: 1px solid;
}

.Div_PanelHeader {
    border-bottom: 1px dotted;
    font-size: 120%;
    padding: 2px 5px 2px 8px;
}

.NewsItemContent {
    padding: 5px;
    font-size: 14px;
    display: none;
    border-bottom: 1px solid;
    white-space: pre-line;
}

.NewsItem {
    padding-left: 10px;
    padding-right: 10px;
    padding-top: 5px;
}

.NewsItemTitle {
    margin-top: -5px;
    font-weight: bold;
    padding-bottom: 3px;
    position: relative;
}

.NewsItem .NewsItemTime {
    margin-bottom: 8px;
    font-size: 80%;
    font-weight: normal;
    position: absolute;
    top: 0px;
    right: 0px;
}

.NewsItemTime {
    font-style: italic;
}

.NewNewsItem {
    background-color: hsl(51, 85%, 60%);
    <? GradientBackground(array(
        array(0, 'hsl(51, 70%, 65%)'),
        array(1, 'hsl(51, 80%, 55%)')
    )); ?>
}

#Div_SpeechBubble {
    background-color: rgb(255, 255, 255);
    border: 1px solid;
    -moz-border-radius: 5px;
    border-radius: 5px;
    position: absolute;
    top: 0px;
    left: 60px;
    padding: 5px;
    width: 300px;
    display: none;
}

.FontPart1 {
    font-family: palatino linotype, lucida, serif;
}

.FontPart2 {
    font-family: comic sans ms, sans-serif;
}

.ArmyPanel {
    background-color: hsl(34, 78%, 85%);
    <? GradientBackground(array(
        array(0, 'hsl(34, 78%, 85%)'),
        array(1, 'hsl(34, 78%, 72%)')
    )); ?>
}

.NewsPanel {
    background-color: hsl(24, 5%, 59%);
    <? GradientBackground(array(
        array(0, 'hsl(24, 5%, 80%)'),
        array(0.8, 'hsl(24, 5%, 65%)')
    )); ?>
}

.TopFormationsPanel {
    background-color: hsl(200, 67%, 68%);
    <? GradientBackground(array(
        array(0, 'hsl(200, 67%, 78%)'),
        array(1, 'hsl(200, 67%, 58%)')
    )); ?>
}

.TopBountyPanel {
    background-color: hsl(0, 67%, 77%);
    <? GradientBackground(array(
        array(0, 'hsl(0, 67%, 77%)'),
        array(1, 'hsl(0, 67%, 60%)')
    )); ?>
}

</style>

<div class = 'PageDiv'>
    <div class = 'PageTitle'>
        Headquarters
    </div>

    <div class = 'PageContent'>

        <div style = 'float: left; width: 475px'>

            <div class = 'Div_Panel BorderDiv' style = 'width: 470px'>
                <div class = 'Div_InnerPanel ArmyPanel'>
                    <div class = 'Div_PanelHeader'>
                        <?= $html->link2('Army', array('controller' => 'army', 'action' => 'index')); ?>
                    </div>

                    <div style = 'font-size: 10pt; padding: 5px;'>
                        <table style = 'width: 100%'>
                            <? for ($i = 0; $i < count($characters); $i += 3): ?>
                                <tr>
                                    <? for ($index = $i; ($index <= $i + 2) && ($index < count($characters)); $index++): ?>
                                        <? $character = $characters[$index]; ?>
                                        <td style = 'width: 300px;'>
                                           <div style = 'position: relative;'>
                                                <?
                                                    $image = $ui->getFaceIcon($character['CClass']['face_icon']);
                                                    echo $html->image($image, array(
                                                        'width' => '25',
                                                        'style' => 'border: 1px solid; vertical-align: middle;'
                                                    ));
                                                ?>
                                                <div style = 'width: 70px; position: absolute; top: 2px; left: 30px;' class = 'ShrinkText'>
                                                    <?= $html->link2($character['Character']['name'], array('controller' => 'characters', 'action' => 'view', $character['Character']['id'])); ?>
                                                </div>
                                                <div style = 'position: absolute; top: 2px; right: 2px;'>
                                                    Lv. <?= $character['Character']['level']; ?>
                                                </div>
                                            </div>
                                        </td>
                                    <? endfor; ?>
                                </tr>
                            <? endfor; ?>
                        </table>
                    </div>
                </div>
            </div>

            <div style = 'text-align: right; position: relative;'>
                <?= $html->image('arbiter_frontpage.png', array('style' => 'width: 100px', 'id' => 'Img_Arbiter')); ?>
                <div id = 'Div_SpeechBubble'>
                    <?
                        $messages = array(
                            "<span class = 'FontPart1'>Hello there. I am Fork, an Arbiter.</span> <span class = 'FontPart2'>Durr hurr.</span>",
                            "<span class = 'FontPart1'>I am a fully qualified Arbiter, ready to</span> <span class = 'FontPart2'>be a fat old man. hurrr.</span>",
                            "<span class = 'FontPart1'>Almasy Penal Code Article 114-2 states that</span> <span class = 'FontPart2'><i>PENAL</i> CODE. HREH.</span>",
                            "<span class = 'FontPart1'>It appears that you are trying to play Almasy. Do you require aid?</span> <span class = 'FontPart2'>im a paperclip lololol!!!1</span>",
                            "<span class = 'FontPart2' style = 'font-style: italic;'>Warp field stabilized!11!</span>",
                            "<span class = 'FontPart1'>If you would care to spar, I daresay my formation could very well defeat yours in a duel.</span> <span class = 'FontPart2'>AHHH PLAGUU!!</span>",
                            "<span class = 'FontPart1'>Greetings, Spoon. What number does the analyzer report for his strength degree?</span> <span class = 'FontPart2'>ITS OVER NINE THOUSANDDDDDDDDDDDDD1!!!</span>",
                            "<span class = 'FontPart1'>This evening, we shall consume comestibles in the underworld!</span>",
                            "<span class = 'FontPart2'>i am teh DESTROYER!</span>",
                            "<span class = 'FontPart1'>A tactician wouldn't reset his account... unless he had something to hide.</span> <span class = 'FontPart2'>YEAHHHHHHHHHHHHHHHHHH</span>",
                            "<span class = 'FontPart2'>Spoon.</span> <span class = 'FontPart1'>Fork</span> <span class = 'FontPart2'>Spoon.</span>",
                        );

                        echo $messages[array_rand($messages)];
                    ?>
                </div>
            </div>
        </div>

        <div style = 'float: right; width: 475px;'>
            <div class = 'Div_Panel BorderDiv' style = 'width: 460px'>
                <div class = 'Div_InnerPanel NewsPanel'>
                    <div class = 'Div_PanelHeader'>
                        News
                    </div>

                    <? foreach ($news as $post): ?>
                        <? $new = strtotime($post['News']['date_posted']) > strtotime('-3 days'); ?>
                        <div class = 'NewsItem <?= $new ? 'NewNewsItem' : ''; ?>'>
                            <div class = 'NewsItemTitle'>
                                <a class = 'Link_NewsItem' href = '#'>
                                    <?= $post['News']['title']; ?>
                                </a>
                                <div class = 'NewsItemTime'>
                                    <? printf("%s on %s", $post['User']['username'], date('M. j, Y', strtotime($post['News']['date_posted']))); ?>
                                </div>
                            </div>

                            <div class = 'NewsItemContent'><?= $post['News']['content']; ?></div>
                        </div>
                    <? endforeach; ?>
                </div>
            </div>

            <div class = 'Div_Panel BorderDiv' style = 'width: 460px'>
                <div class = 'Div_InnerPanel TopFormationsPanel'>
                    <div class = 'Div_PanelHeader' style = 'position: relative'>
                        Almasy's Top Formations
                    </div>

                    <div style = 'padding: 3px;'>
                        <table style = 'width: 100%;'>
                            <? $i = 1; ?>

                            <? foreach ($topFormations as $formation): ?>
                                <tr style = '<?= $i == 1 ? 'font-weight: bold;': ''; ?>'>
                                    <td style = 'font-weight: bold'>
                                        #<?= $i++; ?>
                                    </td>
                                    <td>
                                        <?= $formation['Formation']['name']; ?>
                                    </td>
                                    <td>
                                        <?= $html->image('sprites/' . $formation['User']['portrait'] . '.png', array('style' => 'border: 1px solid; height: 20px; vertical-align: middle;')); ?>
                                        <?= $html->link2($formation['User']['username'], array('controller' => 'users', 'action' => 'profile', $formation['User']['username'])); ?>
                                    </td>
                                    <td style = 'text-align: right'>
                                        <?= number_format($formation['Formation']['reputation']); ?>
                                    </td>
                                </tr>
                            <? endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>

            <div class = 'Div_Panel BorderDiv' style = 'width: 460px'>
                <div class = 'Div_InnerPanel TopBountyPanel'>
                    <div class = 'Div_PanelHeader' style = 'position: relative'>
                        Almasy's Most Wanted Formations
                    </div>

                    <div style = 'padding: 3px;'>
                        <table style = 'width: 100%;'>
                            <? $i = 1; ?>

                            <? foreach ($topFormationsByBounty as $formation): ?>
                                <tr style = '<?= $i == 1 ? 'font-weight: bold;': ''; ?>'>
                                    <td style = 'font-weight: bold'>
                                        #<?= $i++; ?>
                                    </td>
                                    <td>
                                        <?= $formation['Formation']['name']; ?>
                                    </td>
                                    <td>
                                        <?= $html->image('sprites/' . $formation['User']['portrait'] . '.png', array('style' => 'border: 1px solid; height: 20px; vertical-align: middle;')); ?>
                                        <?= $html->link2($formation['User']['username'], array('controller' => 'users', 'action' => 'profile', $formation['User']['username'])); ?>
                                    </td>
                                    <td style = 'text-align: right'>
                                        <?= number_format($formation['Formation']['bounty']); ?>
                                    </td>
                                </tr>
                            <? endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div style = 'clear: both'></div>
    </div>
</div>

<script type = 'text/javascript'>
    $(document).ready(function() {
        $('.Link_NewsItem').click(function(event) {
            event.preventDefault();
            $(this).parent().parent().find('.NewsItemContent').toggle();
        });

        $('#Img_Arbiter').hoverPointer();

        $('#Img_Arbiter').click(function() {
            $('#Div_SpeechBubble').show();
        });
    });
</script>
