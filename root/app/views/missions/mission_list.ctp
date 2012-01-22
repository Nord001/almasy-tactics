<style type = 'text/css'>

.MissionBox {
    <? GradientBackground(array(
        array(0, 'hsl(90, 80%, 80%)'),
        array(1, 'hsl(90, 70%, 60%)'),
    )); ?>
    background-color: hsl(90, 80%, 80%);

    border: 1px solid;
    height: 120px;
    position: relative;
    padding: 4px;
}

.RestrictedMission {
    <? GradientBackground(array(
        array(0, 'hsl(10, 80%, 80%)'),
        array(1, 'hsl(10, 70%, 60%)'),
    )); ?>
    background-color: hsl(10, 80%, 80%);
}

.HoverBox {
    <? GradientBackground(array(
        array(0, 'rgb(180, 180, 230)'),
        array(1, 'rgb(148, 148, 210)')
    )); ?>
}

.Header {
    font-size: 120%;
    border-bottom: 1px dotted;
    margin-bottom: 10px;
}

.MissionHeader {
    border-bottom: 1px dotted;
    font-weight: bold;
}

.MissionDescription {
    font-style: italic;
    font-size: 90%;
}

.MissionRewards {
    position: absolute;
    bottom: 25px;
    left: 5px;
}

.MissionRestrictions {
    position: absolute;
    bottom: 4px;
    left: 5px;
}

.RestrictedDescription {
    color: rgb(150, 0, 0);
}
</style>

<div class = 'PageDiv'>
    <div class = 'PageTitle'>
        Missions
        <? if (isset($selectedFormation)): ?>
            | <?= $selectedFormation['Formation']['name']; ?>
        <? endif; ?>
    </div>

    <div class = 'PageContent'>
        Select a formation to view available missions.
        <form>
            <select name = "data[formation_id]" id = "FormationSelect" style = 'font-size: 90%;'>
                <? foreach ($userFormations as $formation): ?>
                    <option
                        value = '<?= $formation['Formation']['id']; ?>'
                        <?= $formation['Formation']['id'] == @$selectedFormation['Formation']['id'] ? 'selected' : ''; ?>
                    >
                        <?= $formation['Formation']['name']; ?>
                    </option>
                <? endforeach; ?>
            </select>
            <input type = 'submit' value = 'Go!' id = 'FormationSubmit' />
        </form>

        <? if (!isset($noFormationSelected)): ?>
            <div class = 'Header'>
                Open Missions
            </div>
            <?= $missionView->displayMissionList($openMissions, false); ?>
            <div class = 'Header'>
                Locked Missions
            </div>
            <?= $missionView->displayMissionList($restrictedMissions, true); ?>
        <? endif; ?>
    </div>
</div>

<script type = 'text/javascript'>
    $(document).ready(function() {
        $('#FormationSubmit').click(function(event) {
            event.preventDefault();
            var id = $('#FormationSelect').val();
            window.location = '<?= $html->url(array('controller' => 'missions', 'action' => 'mission_list')); ?>' + '/' + id;
        });

        // Setup hover
        $('.MissionBox').hover(
            function() {
                $(this).addClass('HoverBox');
                $('body').css('cursor', 'pointer');
            },
            function() {
                $(this).removeClass('HoverBox');
                $('body').css('cursor', 'auto');
            }
        );

        $('.MissionBox').click(function() {
            var url = '<?= $html->url(array('controller' => 'missions', 'action' => 'view')); ?>/';
            window.location = url + <?= @$formation['Formation']['id']; ?> + '/' + $(this).attr('missionId');
        });
    });
</script>
