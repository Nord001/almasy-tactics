<?

class UiHelper extends AppHelper {

    var $helpers = array('Html');

    //---------------------------------------------------------------------------------------------
    function HelpIcon ($id = false) {
        if ($id === false)
            echo $this->Html->image('help.png', array('class' => 'HelpIcon', 'style' => 'vertical-align: middle'));
        else
            echo $this->Html->image('help.png', array('id' => $id, 'style' => 'vertical-align: middle'));
    }

    //---------------------------------------------------------------------------------------------
    function replaceBonusDesc ($desc, $character) {
        $desc = str_replace('#name#', $character['Character']['name'], $desc);
        return $desc;
    }

    //---------------------------------------------------------------------------------------------
    function locationGrid ($locations) { ?>

        <table style = 'width: 100%; border: 1px solid;'>
            <? for ($i = 0; $i < 3; $i++): ?>
                <tr>
                <? for ($j = 0; $j < 3; $j++): ?>
                    <? $cell = $i * 3 + $j + 1; ?>
                    <? $color = in_array($cell, $locations) ? 'background-color: rgb(225, 225, 255); font-weight: bold;' : 'background-color: rgb(255, 255, 255)'; ?>
                    <td style = 'border: 1px solid; text-align: center; <?= $color; ?>'><?= $cell; ?></td>
                <? endfor; ?>
                </tr>
            <? endfor; ?>
        </table>

    <? }

    //---------------------------------------------------------------------------------------------
    function locationInputGrid () { ?>

        <input type = 'button' id = 'CheckAllButton' value = 'Check All' />
        <input type = 'button' id = 'UncheckAllButton' value = 'Uncheck All' />
        <table style = 'width: 100px; border: 1px solid; margin-top: 10px;'>
            <? for ($i = 0; $i < 3; $i++): ?>
                <tr>
                <? for ($j = 0; $j < 3; $j++): ?>
                    <? $cell = $i * 3 + $j + 1; ?>
                    <?
                        $checked = false;
                        if ($this->data['Bonus']['locations'])
                            $checked = in_array($cell, $this->data['Bonus']['locations']) ? 'checked = "checked"' : '';
                    ?>
                    <td style = 'border: 1px solid; '><input type = 'checkbox' name = 'data[Bonus][locations][]' value = '<?= $cell; ?>' class = 'inline' <?= $checked; ?> /><?= $cell; ?></td>
                <? endfor; ?>
                </tr>
            <? endfor; ?>
        </table>

        <script type = 'text/javascript'>
            $(document).ready(function() {
                var checkboxes = $("input[name=<?= EscapeJQuerySelector('data[Bonus][locations][]'); ?>]")
                $('#CheckAllButton').click(function() {
                    checkboxes.attr('checked', true);
                });
                $('#UncheckAllButton').click(function() {
                    checkboxes.attr('checked', false);
                });
            });
        </script>
    <? }

    //---------------------------------------------------------------------------------------------
    function displayBattleIcon ($picture) {

        // Show default
        if ($picture == '')
            $picture = 'battle';
        $picture = $picture . '.png';

        return $this->Html->image('sprites/' . $picture, array('class' => 'battle-icon'));
    }

    //---------------------------------------------------------------------------------------------
    function displayFaceIcon ($picture, $style = false) {

        return $this->Html->image($this->getFaceIcon($picture), array('class' => 'face-icon', 'style' => $style));
    }

    //---------------------------------------------------------------------------------------------
    function getFaceIcon ($picture) {
        // Show default
        if ($picture == '')
            $picture = 'face';
        $picture = $picture . '.png';

        return 'sprites/'. $picture;
    }

    //---------------------------------------------------------------------------------------------
    function displayItemIcon ($picture) {
        // Show default
        if ($picture == '')
            $picture = 'knife';
        $picture = $picture . '.png';

        return $this->Html->image('sprites/' . $picture, array('class' => 'item-icon'));
    }

    //---------------------------------------------------------------------------------------------
    function displayStat ($stat) {
        return '<span style = "font-weight: bold">' . intval($stat) . '</span>';
    }

    //---------------------------------------------------------------------------------------------
    function displayGrowth ($growth, $showColor = true) {
        $color = '';
        if ($showColor) {
            if ($growth > 0)
                $color = 'color: rgb(25, 125, 25)';
            else
                $color = 'color: rgb(125, 25, 25)';
        }

        if ($growth == intval($growth))
            return sprintf("<span style = '$color; font-weight: bold'>%+d</span>", $growth);

        return sprintf("<span style = '$color; font-weight: bold'>%+.1f</span>", $growth);
    }

    //---------------------------------------------------------------------------------------------
    function displayAffinitySprite ($num) {
        switch ($num) {
            case AFFINITY_FIRE:
                return $this->Html->image('sprites/affinity_fire.png', array('alt' => 'Fire', 'title' => 'Fire'));
            case AFFINITY_STEEL:
                return $this->Html->image('sprites/affinity_steel.png', array('alt' => 'Steel', 'title' => 'Steel'));
            case AFFINITY_WOOD:
                return $this->Html->image('sprites/affinity_wood.png', array('alt' => 'Wood', 'title' => 'Wood'));
            case AFFINITY_EARTH:
                return $this->Html->image('sprites/affinity_earth.png', array('alt' => 'Earth', 'title' => 'Earth'));
            case AFFINITY_WATER:
                return $this->Html->image('sprites/affinity_water.png', array('alt' => 'Water', 'title' => 'Water'));
            default:
                return "Error";
        }
        return "Error";
    }

    //---------------------------------------------------------------------------------------------
    function displayHelpTooltip ($name, $content) { ?>
        <? $name = 'HelpTooltip-' . $name; ?>
        <div class = 'HelpTooltip' id = '<?= $name; ?>'>
            <?= $this->Html->image('large_help.png', array('style' => 'vertical-align: middle;')); ?>
            <div class = 'HelpTooltipContent'>
                <?= $content; ?>
            </div>
        </div>
        <script type = 'text/javascript'>
            $('#<?= $name; ?>').helpTooltip();
        </script>
    <? }

    //---------------------------------------------------------------------------------------------
    function displayBonusGrid ($character) { ?>
        <div style = 'width: 100px'>
            <table style = 'border: 1px solid;'>
                <? for ($i = 0; $i < 3; $i++): ?>
                    <tr>
                    <? for ($j = 0; $j < 3; $j++): ?>
                        <? $cell = $i * 3 + $j + 1; ?>
                        <? $class = in_array($cell, $character['CClass']['bonus_locations']) ? 'ActiveBonus' : 'InactiveBonus'; ?>
                        <? $color = in_array($cell, $character['CClass']['bonus_locations']) ? 'background-color: rgb(205, 205, 255); font-weight: bold;' : 'background-color: rgb(255, 255, 255)'; ?>
                        <td style = 'width: 30px; height: 30px; border: 1px solid rgb(0, 0, 0);' class = '<?= $class; ?>'>
                            <?
                                if ($cell == 5) {
                                    $icon = $this->getFaceIcon($character['CClass']['face_icon']);
                                    echo $this->Html->image($icon, array('style' => 'width: 30px; border: 1px solid rgb(0, 0, 0)'));
                                }
                            ?>
                        </td>
                    <? endfor; ?>
                    </tr>
                <? endfor; ?>
            </table>
        </div>
    <? }
}

?>
