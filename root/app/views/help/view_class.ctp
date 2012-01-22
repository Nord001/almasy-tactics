<style type = 'text/css'>
    .DivHeader {
        font-size: 140%;
        border-bottom: 1px dotted;
        padding-left: 1px;
    }

    .DivContent {
        padding: 3px;
    }
</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> |
        <?= $html->link2('Class List', array('controller' => 'help', 'action' => 'class_list')); ?> |
        <?= $class['CClass']['name']; ?>
    </div>

    <div class = 'PageContent' style = 'position: relative'>

        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader' style = 'margin-bottom: 5px'><?= $class['CClass']['name']; ?></div>

        <div style = 'position: absolute; left: 430px;'>
            <div>
                <?= $ui->displayFaceIcon($class['CClass']['face_icon']); ?>
            </div>

            <div style = 'margin-top: 5px'>
                <? foreach ($class['CClass']['WeaponType'] as $type): ?>
                    <div style = 'width: 24px; height: 24px; display: inline;'>
                        <?= $html->image('sprites/' . $type['sprite'] . '.png', array('title' => 'Uses ' . Inflector::pluralize($type['name']))); ?>
                    </div>
                <? endforeach; ?>
            </div>
        </div>

        <div style = 'float: left; width: 400px;'>
            <div>
                <div class = 'DivHeader'>
                    Stats
                </div>

                <div class = 'DivContent'>
                    <table style = 'width: 100%'>
                        <tr>
                            <td>STR</td>
                            <td style = 'text-align: right'><?= $ui->displayGrowth($class['CClass']['growth_str']); ?></td>
                        </tr>
                        <tr>
                            <td>VIT</td>
                            <td style = 'text-align: right'><?= $ui->displayGrowth($class['CClass']['growth_vit']); ?></td>
                        </tr>
                        <tr>
                            <td>INT</td>
                            <td style = 'text-align: right'><?= $ui->displayGrowth($class['CClass']['growth_int']); ?></td>
                        </tr>
                        <tr>
                            <td>LUK</td>
                            <td style = 'text-align: right'><?= $ui->displayGrowth($class['CClass']['growth_luk']); ?></td>
                        </tr>
                        <tr>
                            <td>Range</td>
                            <td style = 'text-align: right'><?= $class['CClass']['min_range']; ?> - <?= $class['CClass']['max_range']; ?></td>
                        </tr>
                        <tr>
                            <td>Speed</td>
                            <td style = 'text-align: right'><?= $class['CClass']['speed']; ?></td>
                        </tr>
                        <tr>
                            <td>Melee Attack Stat</td>
                            <td style = 'text-align: right'>
                                <?
                                    if ($class['CClass']['melee_atk_stat'] != 'both')
                                        echo up($class['CClass']['melee_atk_stat']);
                                    else
                                        echo 'STR + INT';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Ranged Attack Stat</td>
                            <td style = 'text-align: right'>
                                <?
                                    if ($class['CClass']['ranged_atk_stat'] != 'both')
                                        echo up($class['CClass']['ranged_atk_stat']);
                                    else
                                        echo 'STR + INT';
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div>
                <div class = 'DivHeader'>
                    Promotions
                </div>

                <div class = 'DivContent'>
                    <? if (count($promotionClasses) > 0): ?>
                        <? foreach ($promotionClasses as $promotionClass): ?>
                            <div style = 'height: 40px; position: relative'>
                                <div style = 'position: absolute; top: 0px'>
                                    <?
                                        $icon = $ui->getFaceIcon($promotionClass['CClass']['face_icon']);
                                        echo $html->image($icon, array('style' => 'width: 35px; border: 1px solid rgb(0, 0, 0);'));
                                    ?>
                                </div>
                                <div style = 'position: absolute; top: 7px; left: 45px'>
                                    <?
                                        $str = sprintf('%s (Lv. %s)', $promotionClass['CClass']['name'], $promotionClass['CClass']['required_level']);
                                        echo $html->link2($str, array('controller' => 'help', 'action' => 'view_class', $promotionClass['CClass']['id']));
                                    ?>
                                </div>
                            </div>
                        <? endforeach; ?>
                    <? else: ?>
                        No promotions.
                    <? endif; ?>
                </div>
            </div>

            <div class = 'DivHeader'>
                Quick Find
            </div>
            <div class = 'DivContent'>
                <form>
                    <input type = 'text' id = 'ClassSearchInput' />
                    <input type = 'submit' style = 'width: auto; height: auto; font-size: 10pt;' value = 'Quick Find' id = 'ClassSearchButton' />
                </form>

                <script type = 'text/javascript'>
                    var url = '<?= $html->url(array('controller' => 'help', 'action' => 'view_class')); ?>';
                    $(document).ready(function() {
                        $('#ClassSearchButton').click(function(event) {
                            event.preventDefault();
                            window.location = url + '/' + $('#ClassSearchInput').val();
                        });
                    });
                </script>
            </div>
        </div>

        <div style = 'float: right; width: 380px; margin-right: 20px;'>
            <? if (!empty($class['CClass']['Bonus'])): ?>
                <div>
                    <div class = 'DivHeader'>
                        Ability: <?= $class['CClass']['bonus_name']; ?>
                    </div>

                    <div class = 'DivContent'>
                        <table>
                            <tr>
                                <td>
                                    <div style = 'width: 100px'>
                                        <? $ui->displayBonusGrid ($class); ?>
                                    </div>
                                </td>
                                <td>
                                    <?
                                        $str = str_replace('#name#', 'this class', $class['CClass']['bonus_description']);
                                        $str = str_replace('<li>this', '<li>This', $str);
                                        if (strpos($str, 'this') === 0)
                                            $str[0] = 'T';
                                        echo $str;
                                    ?>                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            <? endif; ?>
        </div>

        <div style = 'clear: both'></div>

    </div>
</div>
