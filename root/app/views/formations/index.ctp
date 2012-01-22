<style type = 'text/css'>
    .FormationDiv {
        width: 465px;
        border: 1px solid;
        position: relative;
        font-size: 120%;
        margin-bottom: 10px;
    }

    .FormationInnerDiv {
        height: 136px;
        border: 1px solid;
        padding: 2px;
        background-color: hsl(209, 86%, 83%);
        <? GradientBackground(array(
            array(0, 'hsl(209, 86%, 83%)'),
            array(1, 'hsl(209, 86%, 65%)')
        )); ?>
    }

    .Active {
        background-color: hsl(118, 56%, 83%);
        <? GradientBackground(array(
            array(0, 'hsl(118, 56%, 73%)'),
            array(1, 'hsl(118, 56%, 55%)')
        )); ?>
    }

    .FormationHeader {
        font-size: 90%;
        border-bottom: 1px dotted;
        margin-left: 2px;
    }

    .FormationContent {
        position: relative;
        padding: 5px;
    }

    .ColumnHeader {
        font-size: 120%;
        font-style: italic;
        margin-bottom: 10px;
    }

</style>

<div class = 'PageDiv'>
    <div class = 'PageTitle'>
        Your Formations

        <div style = 'position: absolute; top: 7px; right: 200px; font-size: 80%;'>
            <input type = 'button' value = 'Rankings' id = 'RankingsButton' />
            <script type = 'text/javascript'>
                $('#RankingsButton').click(function(event) {
                    event.preventDefault();

                    window.location = '<?= $html->url(array('controller' => 'formations', 'action' => 'rankings')); ?>';
                });
            </script>
        </div>
        <div style = 'position: absolute; top: 7px; right: 10px; font-size: 80%;'>
            <input type = 'button' value = 'Create Formation' id = 'CreateButton' />
            <script type = 'text/javascript'>
                var createUrl = '<?= $html->url(array('controller' => 'formations', 'action' => 'create')); ?>';
                $('#CreateButton').click(function(event) {

                    var canCreate = <?= (count($activeFormations) + count($inactiveFormations)) < MAX_FORMATIONS_PER_USER ? 'true' : 'false'; ?>;
                    event.preventDefault();

                    if (!canCreate) {
                        alert('You cannot create any more formations.');
                        return;
                    }
                    window.location = createUrl;
                });
            </script>
        </div>
    </div>

    <div class = 'PageContent'>
        <div style = 'float: left; width: 460px;'>
            <div class = 'ColumnHeader'>
                Active Formations
            </div>
            <? if (!empty($activeFormations)): ?>
                <? foreach ($activeFormations as $formation): ?>
                    <? $formationView->displayFormation($formation, true); ?>
                <? endforeach; ?>
            <? else: ?>
                None.
            <? endif; ?>
        </div>

        <div style = 'float: right; width: 470px; margin-right: 10px; padding-left: 9px;'>
            <div class = 'ColumnHeader'>
                Standby Formations
            </div>
            <? if (!empty($inactiveFormations)): ?>
                <? foreach ($inactiveFormations as $formation): ?>
                    <? $formationView->displayFormation($formation, false); ?>
                <? endforeach; ?>
            <? else: ?>
                None.
            <? endif; ?>
        </div>

        <div style = 'clear: both'></div>
    </div>
</div>

<script type = 'text/javascript'>
    $('.OrganizeButton').click(function(event) {
        var id = $(this).closest('div').attr('formationId');
        event.preventDefault();
        window.location = '<?= $html->url(array('controller' => 'formations', 'action' => 'view')); ?>' + '/' + id;
    });

    $('.EditButton').click(function(event) {
        var id = $(this).closest('div').attr('formationId');
        event.preventDefault();
        window.location = '<?= $html->url(array('controller' => 'formations', 'action' => 'edit')); ?>' + '/' + id;
    });

    $('.StrategyButton').click(function(event) {
        var id = $(this).closest('div').attr('formationId');
        event.preventDefault();
        window.location = '<?= $html->url(array('controller' => 'formations', 'action' => 'strategy')); ?>' + '/' + id;
    });

    <? if(false): ?>
        $('.MissionsButton').click(function(event) {
            var id = $(this).closest('div').attr('formationId');
            event.preventDefault();
            window.location = '<?= $html->url(array('controller' => 'missions', 'action' => 'mission_list')); ?>' + '/' + id;
        });
    <? endif; ?>
</script>
