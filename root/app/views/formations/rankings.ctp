<style type = 'text/css'>
    .StatTable {
        width: 600px;
    }

    .StatTable td {
        font-size: 140%;
    }

    .DividerHeader {
        margin-top: 30px;
        font-size: 150%;
        width: 700px;
        border-bottom: 1px dotted;
    }

    .OwnedFormation {
        font-weight: bold;
    }
</style>

<div class = 'PageDiv'>
    <div class = 'PageTitle'>
        <?= $html->link2('Formations', array('controller' => 'formations', 'action' => 'index')); ?> |
        Rankings
    </div>

    <div class = 'PageContent'>
        <div style = 'position: relative; left: 50%; margin-left: -300px'>
            <table class = 'StatTable'>
                <? $lastRank = 0; ?>
                <? foreach ($personalRankings as $ranking): ?>
                    <? if ($ranking['Rank'] - $lastRank > 1): ?>
                        <tr>
                            <td colspan = 4 style = 'text-align: center'>
                            ...
                            </td>
                        </tr>
                    <? endif; ?>
                    <? $lastRank = $ranking['Rank']; ?>
                    <? $class = ($ranking['Formation']['user_id'] == $a_user['User']['id']) ? 'OwnedFormation' : ''; ?>
                    <tr class = '<?= $class; ?>'>
                        <td>
                            #<?= $ranking['Rank']; ?>
                        </td>
                        <td style = 'text-align: center'>
                            <?= $ranking['Formation']['name']; ?> (<?= $ranking['Formation']['User']['username']; ?>)
                        </td>
                        <td style = 'text-align: center'>
                            <?= $ranking['Formation']['reputation']; ?>
                        </td>
                        <td style = 'text-align: center'>
                            <?= $ranking['Formation']['battles_won']; ?> - <?= $ranking['Formation']['battles_lost']; ?>
                        </td>
                    </tr>
                <? endforeach; ?>
            </table>
        </div>
        <div style = 'text-align: center; font-size: 140%'>
            ...out of <?= $numRankings; ?> formations
        </div>
    </div>
</div>