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

    .Yes {
        color: rgb(0, 100, 0);
    }
    .No {
        color: rgb(100, 0, 0);
    }
</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Battling Basics
    </div>
    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Battling Basics</div>

        <div class = 'HelpIntro'>
            Clicking on the War Room link will bring you to a page with two different options for battle: Spar and battle. The options only affect how you attack other people. The actual battle always works the same way. Specifics on how an actual battle works are <a href = '/help/battle_mechanics'>here</a>.
        </div>

        <div class = 'StatHeader'>
            Spar
        </div>
        <div class = 'StatContent'>
            Sparring allows you to attack any team with no consequences. You can initiate battles by typing the <u>exact</u> name of the formation you want to attack. If you're trying to test a team or want to just challenge your friends for bragging rights, you'll want to use the Spar option.
        </div>

        <div class = 'StatHeader'>
            Battle
        </div>
        <div class = 'StatContent'>
            <div>
                Battle pits you against a formation of similar <a href = '/help/powerandrep'>reputation</a>. You can't choose your opponent, but since the matchmaking system pairs you up with someone of similar reputation, the fights you encounter here will typically be fair.
            </div>
            <div>
                After each battle, you'll get experience and yuanbao. If you win, you'll get a lot experience and yuanbao, while racking up <a href = '/help/bounty'>bounty</a>!
            </div>
        </div>

        <div style = 'position: absolute; top: 250px; right: 5px;'>
            <?= $html->image('help/neophyte_200x251.png'); ?>
        </div>

        <div class = 'StatHeader'>
            Battle Type Summary
        </div>
        <div class = 'StatContent'>
            <table class = 'DataTable'>
                <tr>
                    <th></th>
                    <th>Choose Target</th>
                    <th>EXP Gain</th>
                    <th>Battle Point Usage</th>
                </tr>
                <tr>
                    <th>Spar</th>  <td class = 'Yes'>Yes</td> <td class = 'No'>No</td>  <td class = 'No'>No</td>
                </tr>
                <tr>
                    <th>Battle</th>  <td class = 'No'>No</td> <td class = 'Yes'>Yes</td>  <td class = 'Yes'>Yes</td>
                </tr>
            </table>

        </div>

        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>