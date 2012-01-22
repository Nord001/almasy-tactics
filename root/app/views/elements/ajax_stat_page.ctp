<table>
    <tr>
        <td colspan = 3 style = 'text-align: right'>
            <span style = 'font-size: 120%'>Trait Points: <?= $user['User']['stat_points']; ?></span>
        </td>
    </tr>
    <tr>
        <td class = 'StatHeaderTd'>
            <div class = 'TacStatHeader'>
                Zeal
            </div>
            Increases the speed at which you get battles by 0.75% for each point of zeal.
        </td>
        <td class = 'StatData'>
            <?= $user['User']['zeal']; ?>
        </td>
        <td class = 'IncreaseButtonTd'>
            <? if ($user['User']['stat_points'] > 0): ?>
                <input type = 'button' class = 'IncreaseButton' value = '+' name = 'zeal' />
            <? endif; ?>
        </td>
    </tr>
    <tr>
        <td class = 'StatHeaderTd'>
            <div class = 'TacStatHeader'>
                Greed
            </div>
            Increases your daily income for each point of greed.
        </td>
        <td class = 'StatData'>
            <?= $user['User']['greed']; ?>
        </td>
        <td class = 'IncreaseButtonTd'>
            <? if ($user['User']['stat_points'] > 0): ?>
                <input type = 'button' class = 'IncreaseButton' value = '+' name = 'greed' />
            <? endif; ?>
        </td>
    </tr>
    <tr>
        <td class = 'StatHeaderTd'>
            <div class = 'TacStatHeader'>
                Ambition
            </div>
            Increases the amount of bounty and yuanbao you win from battlegrounds by 0.75% for each point of ambition. <br />
            Increases the amount of interest you receive from battlegrounds by 0.5% for each point of ambition.
        </td>
        <td class = 'StatData'>
            <?= $user['User']['ambition']; ?>
        </td>
        <td class = 'IncreaseButtonTd'>
            <? if ($user['User']['stat_points'] > 0): ?>
                <input type = 'button' class = 'IncreaseButton' value = '+' name = 'ambition' />
            <? endif; ?>
        </td>
    </tr>
</table>