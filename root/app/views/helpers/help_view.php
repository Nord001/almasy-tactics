<?php

define('CLASS_TREE_HTML_CACHE', 'class_tree_html');
define('CLASS_TREE_HTML_CACHE_DURATION', 'long');

define('CELL_HEIGHT', 117);
define('CELL_WIDTH', 100);
define('ARROW_WIDTH', 20);

class HelpViewHelper extends AppHelper {

    var $helpers = array('Html', 'Ui');

    //---------------------------------------------------------------------------------------------
    function RenderSubTree ($classTree, $height) { ?>
        <div style = 'position: relative; height: <?= $height; ?>px;'>
            <? foreach ($classTree as $class): ?>
                <div style = 'position: absolute; top: <?= $class['y']; ?>px; left: <?= $class['x']; ?>px;'>
                    <div style = 'position: relative; border: 1px solid; width: <?= CELL_WIDTH; ?>px; height: <?= CELL_HEIGHT; ?>px;' classId = '<?= $class['CClass']['id']; ?>'>
                        <?
                            $icon = $this->Ui->getFaceIcon($class['CClass']['face_icon']);
                            echo $this->Html->image($icon);
                        ?>
                        <div class = 'ClassName'>
                            <?= $class['CClass']['name']; ?>
                        </div>
                        <? if (isset($class['required_level'])): ?>
                            <div class = 'RequiredLevel'>
                                <?= $class['required_level']; ?>
                            </div>
                        <? endif; ?>

                        <div class = 'ClassTooltip' style = 'display: none'>
                            <div style = 'border-bottom: 1px dotted; font-weight: bold; position: relative; font-size: 120%; padding-bottom: 4px; margin-bottom: 3px;'>
                                <?= $class['CClass']['name']; ?>

                                <div style = 'position: absolute; top: 0px; right: 0px'>
                                    <? foreach ($class['CClass']['WeaponType'] as $type): ?>
                                        <div style = 'width: 24px; height: 24px; display: inline;'>
                                            <?= $this->Html->image('sprites/' . $type['sprite'] . '.png', array('title' => 'Uses ' . Inflector::pluralize($type['name']))); ?>
                                        </div>
                                    <? endforeach; ?>
                                </div>
                            </div>

                            <div style = 'font-weight: bold'>
                                Stats
                            </div>

                            <div style = 'border-bottom: 1px dotted; padding-bottom: 3px; margin-bottom: 3px;'>
                                <table style = 'width: 100%'>
                                    <tr>
                                        <td>STR</td>
                                        <td>VIT</td>
                                        <td>INT</td>
                                        <td>LUK</td>
                                        <td>Range</td>
                                        <td>Speed</td>
                                        <td>Melee Attack</td>
                                        <td>Ranged Attack</td>
                                    </tr>
                                    <tr>
                                        <td><?= $this->Ui->displayGrowth($class['CClass']['growth_str']); ?></td>
                                        <td><?= $this->Ui->displayGrowth($class['CClass']['growth_vit']); ?></td>
                                        <td><?= $this->Ui->displayGrowth($class['CClass']['growth_int']); ?></td>
                                        <td><?= $this->Ui->displayGrowth($class['CClass']['growth_luk']); ?></td>
                                        <td><?= $class['CClass']['min_range']; ?> - <?= $class['CClass']['max_range']; ?></td>
                                        <td><?= $class['CClass']['speed']; ?></td>
                                        <td>
                                            <?
                                                if ($class['CClass']['melee_atk_stat'] != 'both')
                                                    echo up($class['CClass']['melee_atk_stat']);
                                                else
                                                    echo 'STR + INT';
                                            ?>
                                        </td>
                                        <td>
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

                            <? if ($class['CClass']['bonus_name']): ?>
                                <div style = 'font-weight: bold'>
                                    Ability: <?= $class['CClass']['bonus_name']; ?>
                                </div>
                                <table style = 'margin: 2px'>
                                    <tr>
                                        <td style = 'vertical-align: middle'>
                                            <? $this->Ui->displayBonusGrid ($class); ?>
                                        </td>
                                        <td style = 'vertical-align: top'>
                                            <?
                                                $str = str_replace('#name#', 'this class', $class['CClass']['bonus_description']);
                                                $str = str_replace('<li>this', '<li>This', $str);
                                                if (strpos($str, 'this') === 0)
                                                    $str[0] = 'T';
                                                echo $str;
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            <? endif; ?>
                        </div>
                    </div>
                </div>

                <? if ($class['id'] == MELEE_BATTLESUIT_CLASS_ID || $class['id'] == HVY_BATTLESUIT_CLASS_ID): ?>
                    <? // Special crosses for these classes. ?>
                    <div style = 'position: absolute; left: <?= $class['x'] + CELL_WIDTH; ?>px; top: <?= $class['y'] + CELL_HEIGHT / 2; ?>px;'>
                        <?= $this->Html->image('help/cross_arrows.png'); ?>
                    </div>
                <? endif; ?>

                <? foreach ($class['promotions'] as $promotionClassData): ?>
                    <? $promotionClass = $classTree[$promotionClassData['index']]; ?>
                    <? if ($promotionClass['x'] == $class['x']): ?>

                        <? // Simple vertical line ?>
                        <div class = 'VerticalArrow' style = 'top: <?= $class['y'] + CELL_HEIGHT + 2; ?>px; left: <?= $class['x'] + (CELL_WIDTH - ARROW_WIDTH) / 2; ?>px; height: <?= $promotionClass['y'] - $class['y'] - CELL_HEIGHT; ?>px;'>
                        </div>

                    <? else: ?>
                        <? if (isset($class['useBendyArrow']) && $class['useBendyArrow']): ?>
                            <? // Do nothing here, taken care of by crosses above. ?>
                        <? else: ?>
                            <? // Vertical align straight up from promotion class ?>
                            <div class = 'VerticalArrow' style = 'top: <?= $class['y'] + CELL_HEIGHT / 2; ?>px; left: <?= $promotionClass['x'] + (CELL_WIDTH - ARROW_WIDTH) / 2; ?>px; height: <?= $promotionClass['y'] - $class['y'] - CELL_HEIGHT / 2; ?>px;'>
                            </div>

                            <? // Horizontal bend to the left or right ?>
                            <? $width = abs($promotionClass['x'] - $class['x']) - CELL_WIDTH / 2 + 5; ?>

                            <? $top = $class['y'] + (CELL_HEIGHT - ARROW_WIDTH) / 2; ?>

                            <? if ($promotionClass['x'] > $class['x']): ?>
                                <div class = 'RightArrow' style = 'top: <?= $top; ?>px; left: <?= $class['x'] + CELL_WIDTH + 2; ?>px; width: <?= $width; ?>px;'>
                                </div>
                            <? else: ?>
                                <div class = 'LeftArrow' style = 'top: <?= $top; ?>px; left: <?= $promotionClass['x'] + (CELL_WIDTH - ARROW_WIDTH) / 2 + 3; ?>px; width: <?= $width + 2; ?>px;'>
                                </div>
                            <? endif; ?>
                        <? endif; ?>
                    <? endif; ?>
                <? endforeach; ?>
            <? endforeach; ?>
        </div>
    <? }
}
?>