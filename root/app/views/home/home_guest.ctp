<style type = 'text/css'>

#PlayNowButton {
    border-radius: 15px;
    -moz-border-radius: 15px;
    background-color: rgb(120, 0, 0);
    <? GradientBackground(array(
        array(0, 'hsl(0, 80%, 35%)'),
        array(1, 'hsl(0, 100%, 20%)')
    )); ?>
    border-bottom: 2px solid rgb(0, 0, 0);
    border-right: 2px solid rgb(0, 0, 0);
    border-top: 1px solid rgb(50, 50, 50);
    border-left: 1px solid rgb(50, 50, 50);
    font-size: 250%;
    color: rgb(255, 255, 255);
    font-family: "garamond";
    font-weight: bold;
    width: 250px;
    height: 105px;
}

#PlayNowButton:hover {
    border-bottom: 2px solid rgb(169, 71, 0);
    border-right: 2px solid rgb(169, 71, 0);
    border-top: 1px solid rgb(229, 151, 0);
    border-left: 1px solid rgb(229, 151, 0);
}

#IntroDiv {
    margin-top: 290px;
    width: 680px;
}

#IntroDivContent {
    <? GradientBackground(array(
        array(0, 'rgb(240, 215, 200)'),
        array(1, 'rgb(220, 193, 140)')
    )); ?>
    background-color: rgb(255, 230, 190);
    border: 1px solid;
    padding: 10px;
}

</style>

<div class = 'PageDiv'>
    <div class = 'PageTitle'>
        Almasy Tactics: Step onto the Battlefield
    </div>

    <div class = 'PageContent'>
        <div style = 'position: absolute; top: 15px; left: 15px;'>
            <?= $html->image('knight_small.png'); ?> <br />
        </div>
        <div style = 'position: absolute; top: 220px; left: 20px; width: 200px; text-align: center;'>
            <b>Step One</b> <br />Recruit the deadliest warriors across the land!
        </div>

        <div style = 'position: absolute; top: 15px; left: 280px;'>
            <?= $html->image('virtuoso_small.png'); ?>
        </div>
        <div style = 'position: absolute; top: 220px; left: 260px; width: 200px; text-align: center;'>
            <b>Step Two</b> <br />Arm them with powerful weapons!
        </div>

        <div style = 'position: absolute; top: 15px; left: 510px;'>
            <?= $html->image('minstrel_small.png'); ?>
        </div>
        <div style = 'position: absolute; top: 220px; left: 495px; width: 200px; text-align: center;'>
            <b>Step Three</b> <br /><i>???</i>
        </div>

        <div style = 'position: absolute; top: 15px; left: 740px;'>
            <?= $html->image('mastersmith_small.png'); ?>
        </div>
        <div style = 'position: absolute; top: 220px; left: 740px; width: 200px; text-align: center;'>
            <b>Step Four</b> <br />Profit - and achieve eternal glory! ..or just kick ass!
        </div>

        <div id = 'IntroDiv' class = 'BorderDiv'>
            <div id = 'IntroDivContent'>
                <div style = 'border-bottom: 1px dotted; margin-bottom: 5px; font-size: 140%'>
                    What is Almasy Tactics?
                </div>

                Almasy Tactics is a browser game with strategy and RPG elements. We wanted to make a browser game that wouldn't have the usual tedious routine of logging in once a day, clicking a few links, and then waiting for the next day. Therefore, we made this game to cater both to people who just want a fun game to play and people who want to really explore all aspects of the game (aka, hardcore gamers :P). We hope that the less involved gamers will still find the game interesting and enjoyable, while the more avid gamers will find the strategic depth that makes a game truly captivating for them.
            </div>
        </div>

        <div style = 'position: absolute; top: 300px; left: 700px'>
            <input type = "button" id = 'PlayNowButton' value = "Play Now!" />
        </div>

        <div style = 'position: absolute; top: 405px; left: 700px; width: 250px; text-align: center;'>
            <? $html->link2('More About Almasy', '/help/about'); ?> <br />
            <?= $html->link2('About Us', '/help/who_we_are'); ?> <br />
            <?= $html->link2('Terms of Service', '/help/tos'); ?>
        </div>
    </div>
</div>

<script type = 'text/javascript'>
    $(document).ready(function() {
        $('#PlayNowButton').click(function(event) {
            event.preventDefault();

            window.location = '<?= $html->url(array('controller' => 'users', 'action' => 'register')); ?>';
        });
    });
</script>
