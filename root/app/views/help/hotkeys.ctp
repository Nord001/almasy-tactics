<style type = 'text/css'>
    .DataTable {
        padding: 5px;
    }

    .DataTable th {
        text-align: center;
        padding: 3px;
    }

    .DataTable td {
        text-align: center;
        padding: 3px;
        width: 200px;
    }

</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Hotkeys
    </div>
    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Navigation Hotkeys</div>

        <div class = 'HelpIntro'>
            You can speed up the rate that you navigate through the site using built in hotkeys.
        </div>

        <div class = 'StatHeader'>
            Hotkeys
        </div>
        <div class = 'StatContent'>
            <table class = 'DataTable'>
                <tr>
                    <th>Hotkey</th>
                    <th>Page</th>
                </tr>
                <tr>
                    <td>b</td>  <td>Battle Matchmaking</td>
                </tr>
                <tr>
                    <td>m</td>  <td>Messages</td>
                </tr>
                <tr>
                    <td>a</td>  <td>Army</td>
                </tr>
                <tr>
                    <td>w</td>  <td>War Room</td>
                </tr>
                <tr>
                    <td>f</td>  <td>Formations</td>
                </tr>
                <tr>
                    <td>y</td>  <td>Armory</td>
                </tr>
                <tr>
                    <td>r</td>  <td>Forums</td>
                </tr>
                <tr>
                    <td>h</td>  <td>Help</td>
                </tr>
            </table>

        </div>


        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>