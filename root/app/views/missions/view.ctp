<style type = 'text/css'>

.Header {
    font-size: 120%;
    border-bottom: 1px dotted;
    margin-bottom: 5px;
}
</style>

<div class = 'PageDiv'>
    <div class = 'PageTitle'>
        <?= $html->link2('Missions', array('controller' => 'missions', 'action' => 'mission_list', $formation['Formation']['id'])); ?>
        | <?= $mission['Mission']['name']; ?>
    </div>

    <div class = 'PageContent'>
        Completed <?= $mission['Mission']['completion_count']; ?> times.

        <div class = 'Header'>
            Area Description
        </div>
        <?= $mission['MissionGroup']['name']; ?>: <?= $mission['MissionGroup']['description']; ?>

        <div class = 'Header'>
            Description
        </div>
        <?= $mission['Mission']['description']; ?>

        <div class = 'Header'>
            Requirements
        </div>
        <? if ($mission['Mission']['restrictions'] == ''): ?>
            <span style = 'color: rgb(0, 100, 0)'>Nothing.</span>
        <? else: ?>
            <span class = '<?= !$canDoMission ? 'RestrictedDescription' : ''; ?>'>
                <?= $mission['Mission']['restrictions_desc']; ?>
            </span>
        <? endif; ?>

        <div class = 'Header'>
            Rewards
        </div>
        <?= $missionView->rewardsToString($mission['MissionReward']); ?>

        <div>
            <form method = 'POST' action = '<?= $html->url(array('controller' => 'missions', 'action' => 'do_mission')); ?>'>
                <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
                <input type = 'hidden' name = 'data[mission_id]' value = '<?= $mission['Mission']['id']; ?>' />
                <input type = 'hidden' name = 'data[formation_id]' value = '<?= $formation['Formation']['id']; ?>' />
                <input type = 'submit' value = 'Begin Mission!' id = 'MissionSubmit' />
            </form>
        </div>
    </div>
</div>