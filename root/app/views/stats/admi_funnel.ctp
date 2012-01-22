<style type = 'text/css'>
    #StatsTable th {
        text-align: center;
        font-size: 12pt;
    }

    #StatsTable td {
        text-align: center;
    }
</style>

<div class = 'DividerHeader'>
    New Account Funnel
</div>

<table style = 'width: 80%' id = 'StatsTable'>
    <tr>
        <th>Week</th>
        <th>New Accounts</th>
        <th>Referred Accounts</th>
        <th>Stuck for Ten Mins</th>
        <th>%</th>
        <th>Stuck for One Day</th>
        <th>%</th>
        <th>Stuck for One Week</th>
        <th>%</th>
        <th>Stuck for One Week Overall</th>
    </tr>
    <? foreach ($data as $row): ?>
        <tr>
            <td><?= $row['week']; ?></td>
            <td style = 'background-color: rgb(235, 235, 235)'><?= $row['numNewAccounts']; ?></td>
            <td style = 'background-color: rgb(235, 235, 235)'><?= $row['referredAccounts']; ?></td>
            <td style = 'background-color: rgb(255, 225, 225)'><?= $row['numStuckForTenMinutes']; ?></td>
            <td style = 'background-color: rgb(255, 225, 225)'><?= $row['numStuckForTenMinutesCumul']; ?></td>
            <td style = 'background-color: rgb(225, 225, 255)'><?= $row['numStuckForOneDay']; ?></td>
            <td style = 'background-color: rgb(225, 225, 255)'><?= $row['numStuckForOneDayCumul']; ?></td>
            <td style = 'background-color: rgb(225, 255, 225)'><?= $row['numStuckForOneWeek']; ?></td>
            <td style = 'background-color: rgb(225, 255, 225)'><?= $row['numStuckForOneWeekCumul']; ?></td>
            <td style = 'font-weight: bold'><?= $row['numStuckForOneWeekTotal']; ?></td>
        </tr>
    <? endforeach; ?>
</table>