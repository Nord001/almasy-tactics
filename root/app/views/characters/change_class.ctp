<style type = 'text/css'>

#Div_DetailsContainer {
    float: right;
    width: 57%;
}

.Div_Details {
    display: none;
    font-size: 120%;
}

.DetailsDivContent {
    border: 1px solid;
    padding: 5px;
    background-color: rgb(255, 188, 188);
    position: relative;
}

.Div_Preview {
    border: 1px solid;
    padding: 5px;
}

.AvailableClass {
    background-color: hsl(110, 57%, 70%);
    <? GradientBackground(array(
        array(0, 'hsl(110, 53%, 70%)'),
        array(1, 'hsl(110, 53%, 60%)')
    )); ?>
}

.LockedClass {
    background-color: hsl(10, 77%, 70%);
    <? GradientBackground(array(
        array(0, 'hsl(10, 80%, 75%)'),
        array(1, 'hsl(10, 80%, 65%)')
    )); ?>
}

.SelectedClass {
    background-color: hsl(37, 77%, 85%);
    <? GradientBackground(array(
        array(0, 'hsl(37, 80%, 80%)'),
        array(1, 'hsl(37, 80%, 65%)')
    )); ?>
}

.ClassHeader {
    font-size: 120%;
    border-bottom: 1px dotted;
}

.ClassData, .BonusData {
    border-bottom: 1px dotted;
}

.BonusHeader {
    font-size: 110%;
}

</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Army', array('controller' => 'army', 'action' => 'index')); ?> |
        <?= $html->link2($character['Character']['name'], array('controller' => 'characters', 'action' => 'view', $character['Character']['id'])); ?> |
        Promotion
    </div>

    <div class = 'PageContent'>
        <div style = 'margin-bottom: 5px'>
            Click on a class to change to that class. Choose wisely, because you can<?= "'"; ?>t go back!
        </div>

        <form id = 'Form_ChangeClass' method = 'POST' action = '<?= h($this->here); ?>'>
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
            <?= $form->hidden('character_id', array('name' => 'data[Character][id]', 'value' => $character['Character']['id'])); ?>
            <?= $form->hidden('class_id', array('name' => 'data[Character][class_id]', 'value' => '')); ?>
        </form>

        <!-- List of possible classes -->
        <div style = 'width: 42%; float: left;'>
            <? if (!empty($baseClasses)): ?>
                <div style = 'font-size: 130%; text-align: center; margin-bottom: 5px;'>
                    <?
                        $href = '/help/class_tree/' . strtolower($baseClasses['first']);
                        if (isset($baseClasses['second']))
                            $href .= '#' . strtolower($baseClasses['second']);
                    ?>
                    <?= $html->link($html->image('class_tree.png', array('style' => 'border: 1px solid; border-radius: 2px;')), $href, false, false); ?> <br />
                    <input
                        type = 'button'
                        value = 'View Class Tree for <?= $character['Character']['name']; ?>'
                        id = 'ClassTreeButton'
                        href = '<?= $href; ?>'
                    />
                    <script type = 'text/javascript'>
                        $('#ClassTreeButton').linkButton();
                    </script>
                </div>
            <? endif; ?>

            <? foreach ($promotionClasses as $class): ?>
                <? $canChangeClass = $character['Character']['level'] >= $class['CClass']['required_level']; ?>
                <div class = 'BorderDiv'>
                    <div
                        class = 'Div_Preview <?= $canChangeClass ? 'AvailableClass' : 'LockedClass'; ?>'
                        classId = '<?= $class['CClass']['id']; ?>'
                        cclassName = '<?= $class['CClass']['name']; ?>'
                        canChangeClass = '<?= $canChangeClass ? 1 : 0; ?>'>
                        <div style = 'width: 100%'>
                            <table>
                                <tr>
                                    <td style = 'text-align: center; font-style: italic; font-size: 120%;'>
                                        <? echo $class['CClass']['name']; ?>
                                    </td>
                                <tr>
                                    <td><?= $ui->displayFaceIcon($class['CClass']['face_icon']); ?></td>
                                    <td style = 'font-size: 90%; font-style: italic; vertical-align: top;'><?= $class['CClass']['description']; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            <? endforeach; ?>
        </div>

        <!-- Details that gets populated with the details when people hover over the list below -->
        <div id = 'Div_DetailsContainer'>
            <? foreach ($promotionClasses as $class): ?>
                <? $canChangeClass = $character['Character']['level'] >= $class['CClass']['required_level']; ?>
                <div class = 'Div_Details BorderDiv' classId = <?= $class['CClass']['id']; ?>>
                    <div class = 'DetailsDivContent <?= $canChangeClass ? 'AvailableClass' : 'LockedClass'; ?>'>
                        <div class = 'ClassHeader'>
                            <?= $class['CClass']['name']; ?>
                        </div>

                        <div style = 'position: absolute; top: 5px; right: 5px'>
                            <table style = 'text-align: center; margin-left: auto; margin-right: auto;'>
                                <tr>
                                    <? foreach ($class['WeaponType'] as $type): ?>
                                        <td style = 'width: 24px; height: 24px;'>
                                            <?= $html->image('sprites/' . $type['sprite'] . '.png', array('title' => 'Uses ' . Inflector::pluralize($type['name']))); ?>
                                        </td>
                                    <? endforeach; ?>
                                </tr>
                            </table>
                        </div>

                        <div class = 'ClassData'>
                            <? $color = $canChangeClass ? 'rgb(20, 100, 20)' : 'rgb(125, 25, 25)'; ?>
                            <span style = 'font-weight: bold; color: <?= $color; ?>'>
                                Required Level: <?= $class['CClass']['required_level']; ?>
                            </span>

                            <table style = 'width: 75%;'>
                                <tr>
                                    <td>STR</td>
                                    <td><?= $ui->displayGrowth($class['CClass']['growth_str']); ?></td>
                                    <td>VIT</td>
                                    <td><?= $ui->displayGrowth($class['CClass']['growth_vit']); ?></td>
                                    <td>INT</td>
                                    <td><?= $ui->displayGrowth($class['CClass']['growth_int']); ?></td>
                                    <td>LUK</td>
                                    <td><?= $ui->displayGrowth($class['CClass']['growth_luk']); ?></td>
                                </tr>
                            </table>
                        </div>

                        <? if (!empty($class['Bonus'])): ?>
                            <div class = 'BonusData'>
                                <div class = 'BonusHeader'>
                                    Ability: <?= $class['CClass']['bonus_name']; ?>
                                </div>

                                <table style = 'margin: 2px'>
                                    <tr>
                                        <td style = 'vertical-align: middle'>
                                            <? $ui->displayBonusGrid ($class); ?>
                                        </td>
                                        <td style = 'vertical-align: top'>
                                            <?= $ui->replaceBonusDesc($class['CClass']['bonus_description'], $character); ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        <? endif; ?>

                        <? if (!empty($class['CClass']['promotionClassNames'])): ?>
                            <div style = 'font-size: 110%'>
                                Can Become: <?= implode(', ', $class['CClass']['promotionClassNames']); ?>
                            </div>
                        <? endif; ?>
                    </div>
                </div>
            <? endforeach; ?>
        </div>

        <div style = 'clear: both;'></div>
    </div>
</div>

<script type = 'text/javascript'>
    $(document).ready(function() {

        $('.Div_Preview').hover(
            function() {
                var hoverDiv = $(this);

                var classId = hoverDiv.attr('classId');

                $('.Div_Details[classId!=' + classId + ']').hide();
                $('.Div_Details[classId=' + classId + ']').show();

                if (hoverDiv.attr('canChangeClass') == 1) {
                    $('body').css('cursor', 'pointer');
                    $(this).addClass('SelectedClass');
                }
            },
            function() {
                $('body').css('cursor', 'auto');
                $(this).removeClass('SelectedClass');
            }
        );

        $('.Div_Preview').click(function(event) {
            var canChangeClass = $(this).attr('canChangeClass');
            if (canChangeClass == 1) {
                var classId = $(this).attr('classId');
                var className = $(this).attr('cclassName');
                event.preventDefault();
                var confirmed = confirm('Are you sure you want to change class to be a ' + className + '?');
                if (confirmed) {
                    $('#Form_ChangeClass #class_id').attr('value', classId);
                    $('#Form_ChangeClass').submit();
                }
            }
        });
    });
</script>
