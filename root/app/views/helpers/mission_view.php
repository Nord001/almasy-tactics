<?

class MissionViewHelper extends AppHelper {
    var $helpers = array('Html', 'Ui');

    function rewardsToString ($rewardList) {
        $rewards = array();
        foreach ($rewardList as $reward) {
            if ($reward['chance'] < 1) {
                $rewards[] = '???';
            } else {
                $str = '';
                if ($reward['type'] == 'exp')
                    $str = $reward['value'] . ' exp';
                else if ($reward['type'] == 'money')
                    $str = $reward['value'] . ' yb';
                else if ($reward['type'] == 'item')
                    $str = 'an item'; //$str = 'item: ' . $html->link2($reward['value'], array('controller' => 'user_items', 'action' => 'edit', $reward['value']));
                else if ($reward['type'] == 'character')
                    $str = 'a character'; //$str = 'character: ' . $html->link2($reward['value'], array('controller' => 'character', 'action' => 'view', $reward['value']));
                $rewards[] = $str;
            }
        }
        return count($rewards) > 0 ? implode(', ', $rewards) . '.' : 'None.';
    }

    function displayMissionList ($missions, $restricted) { ?>
        <table style = 'width: 100%'>
            <? for ($i = 0; $i < count($missions); $i += 2): ?>
                <tr>
                    <? for ($index = $i; ($index <= $i + 1) && ($index < count($missions)); $index++): ?>
                        <? $mission = $missions[$index]; ?>
                        <td style = 'width: 500px'>
                            <div class = 'BorderDiv'>
                                <div
                                    class = 'MissionBox <?= $restricted ? 'RestrictedMission' : ''; ?>'
                                    missionId = '<?= $mission['Mission']['id']; ?>'
                                >
                                    <div class = 'MissionHeader'>
                                        <?= $mission['MissionGroup']['name']; ?> - <?= $mission['Mission']['name']; ?>
                                    </div>
                                    <div class = 'MissionDescription'>
                                        <?= $mission['Mission']['description']; ?>
                                    </div>
                                    <div class = 'MissionRestrictions'>
                                        <? if ($mission['Mission']['restrictions'] == ''): ?>
                                            <span style = 'color: rgb(0, 100, 0)'>Requires: Nothing.</span>
                                        <? else: ?>
                                            <span class = '<?= $restricted ? 'RestrictedDescription' : ''; ?>'>
                                                Requires: <?= $mission['Mission']['restrictions_desc']; ?>
                                            </span>
                                        <? endif; ?>
                                    </div>
                                    <div class = 'MissionRewards'>
                                        <?

                                        ?>
                                        Rewards: <?= $this->rewardsToString($mission['MissionReward']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                    <? endfor; ?>
                </tr>
            <? endfor; ?>
        </table>
    <? }
}