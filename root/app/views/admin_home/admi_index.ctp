<meta http-equiv = 'refresh' content = '60' />

<style type = 'text/css'>
    .StatTable {
        width: 700px;
    }

    .StatTable td {
        font-size: 140%;
    }
</style>

<div style = 'margin-bottom: 20px'>
    Welcome to the fuqqin awsum admin page!
</div>

<div class = 'DividerHeader'>
    Stats
</div>

<table class = 'StatTable'>
    <tr>
        <td>
            Players Online
        </td>
        <td style = 'text-align: right'>
            <?= $numOnline; ?>
        </td>
    </tr>
    <tr>
        <td>
            Players Today
        </td>
        <td style = 'text-align: right'>
            <?= $numUsersPlayedToday; ?>
        </td>
    </tr>
    <tr>
        <td>
            New Accounts Created Today
        </td>
        <td style = 'text-align: right'>
            <?= $numNewAccounts; ?>
        </td>
    </tr>
    <tr>
        <td>
            New Players Referred Today
        </td>
        <td style = 'text-align: right'>
            <? printf('%d', $numNewReferredAccounts); ?>
        </td>
    </tr>
    <tr>
        <td>
            Battles Today
        </td>
        <td style = 'text-align: right'>
            <?= $numBattles; ?>
        </td>
    </tr>
</table>

<div class = 'DividerHeader'>
    Player Data
</div>

<div id = 'Graph' style = 'width: 700px; height: 350px'></div>
<div id = 'Graph2' style = 'width: 700px; height: 350px'></div>

<?= $javascript->link('third_party/jquery.flot.pack.js'); ?>

<script type = 'text/javascript'>
    $.plot(
        $('#Graph'),
        <?=
            json_encode(array(
                array(
                    'label' => 'Time Spent On Site (hours)',
                    'data' => $timeSpentInLastTwoWeeks,
                    'yaxis' => 1,
                ),
                array(
                    'label' => 'Players',
                    'data' => $players,
                    'color' => 2,
                    'yaxis' => 2
                ),
            ));
        ?>,
        {
            legend: {
                show: true,
                noColumns: 1,
                position: 'sw',
            },
            xaxis: {
                mode: "time",
                minTickSize: [1, "day"]
            },
            yaxis: {
                min: 0,
            },
            y2axis: {
                min: 0,
                minTickSize: 1,
                tickDecimals: 0,
            },
        }
    );
    $.plot(
        $('#Graph2'),
        <?=
            json_encode(array(
                array(
                    'label' => 'Time Spent On Site (hours)',
                    'data' => $timeSpentInLastTwoMonths,
                    'yaxis' => 1,
                ),
                array(
                    'label' => 'Players',
                    'data' => $playersInLastTwoMonths,
                    'color' => 2,
                    'yaxis' => 2
                ),
            ));
        ?>,
        {
            legend: {
                show: true,
                noColumns: 1,
                position: 'sw',
            },
            xaxis: {
                mode: "time",
                minTickSize: [1, "day"]
            },
            yaxis: {
                min: 0,
            },
            y2axis: {
                min: 0,
                minTickSize: 1,
                tickDecimals: 0,
            },
        }
    );
</script>