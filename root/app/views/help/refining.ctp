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
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Refining
    </div>
    <div class = 'PageContent' style = 'position: relative'>
        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Refining Items</div>

        <div class = 'HelpIntro'>
            Refining increases the damage or defense of an item, but there is a chance for the item to break every time you attempt to refine it. The higher the refine level, the higher the chance of breaking, but the stronger the item gets.
        </div>

        <div class = 'StatHeader'>
            Effect
        </div>
        <div class = 'StatContent'>
            <table class = 'DataTable'>
                <tr>
                    <th>Refine Level</th>
                    <th>Success Rate</th>
                    <th>Weapon Damage Increase</th>
                    <th>Armor Reduction/Defense Increase</th>
                    <th>Cost</th>
                </tr>
                <tr>
                    <td>Sturdy/Durable</td>  <td>50%</td> <td>+8%</td>  <td>+5</td> <td>25% of Item </td>
                </tr>
                <tr>
                    <td>Supreme/Stalwart</td>  <td>30%</td> <td>+15%</td>  <td>+8</td> <td>50% of Item </td>
                </tr>
                <tr>
                    <td>Awesome/Almighty</td>  <td>10%</td> <td>+25%</td>  <td>+15</td> <td>75% of Item </td>
                </tr>
            </table>

            These effects are not cumulative.

        </div>

        <div style = 'position: absolute; top: 220px; right: 10px;'>
            <?= $html->image('help/armorsmith_190x357.png'); ?>
        </div>

        <? require '../views/help/back_to_top.ctp'; ?>
    </div>
</div>