<script type = 'text/javascript'>
    ActivateItemTooltips();
</script>

<?= $html->css('items'); ?>

<style type = 'text/css'>
    .PageContent {
        padding: 2px;
    }

    .PageContent table {
        /*width: 700px;*/
    }

    .PageContent .SectionHeader {
        font-size: 140%;
        border-bottom: 1px dotted rgb(128, 128, 128);
        margin-bottom: 5px;
    }

    .ReferTable {
        width: 700px;
        margin: 5px;
    }

    .Complete {
        color: rgb(0, 125, 0);
    }

    .Incomplete {
        color: rgb(100, 0, 0);
    }
</style>


<div class = 'PageDiv'>
    <div class = 'PageTitle'>
        <?= $html->link2($a_user['User']['username'], array('controller' => 'users', 'action' => 'profile')); ?> | Referrals
    </div>

    <div class = 'PageContent'>
        <div style = 'width: 800px'>
            <div style = 'margin-bottom: 10px; margin-top: 3px;'>
                Refer your friends to play Almasy for sweet rewards!
            </div>

            <div class = 'SectionHeader'>
                Instructions
            </div>

            <div style = 'margin-bottom: 10px'>
                Step 1) A friend signs up with your referral link. <br />
                Step 2) Your friend wins a certain number of battles (shown below). <br />
                Step 3) Both you and your friend get a prize! <br />
            </div>

            <div class = 'SectionHeader'>
                Rewards
            </div>

            <div style = 'margin-bottom: 10px'>
                <table style = 'margin: 5px; width: 800px;'>
                    <tr>
                        <th style = 'width: 100px'>Level</th>
                        <th style = 'width: 100px'>Victories</th>
                        <th style = 'width: 300px'>Yuanbao Reward</th>
                        <th style = 'width: 300px'>Item Reward</th>
                    </tr>
                    <tr>
                        <td>Level 1</td>
                        <td><?= REFERRAL_SYSTEM_LEVEL_1_REQ_BATTLES; ?></td>
                        <td>5000 yuanbao or 20% of your current yuanbao, whichever is greater (100k max)</td>
                        <td>None</td>
                    </tr>
                    <tr>
                        <td>Level 2</td>
                        <td><?= REFERRAL_SYSTEM_LEVEL_2_REQ_BATTLES; ?></td>
                        <td>5000 yuanbao or 20% of your current yuanbao, whichever is greater (100k max)</td>
                        <td>
                            The unique armor
                            <?= $this->element('item_tooltip', array('display' => 'name', 'userItem' => $friendshipsBond['UserItem'], 'displayStyle' => 'font-weight: bold')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Level 3</td>
                        <td><?= REFERRAL_SYSTEM_LEVEL_3_REQ_BATTLES; ?></td>
                        <td>5000 yuanbao or 20% of your current yuanbao, whichever is greater (100k max)</td>
                        <td>
                            The unique weapons
                            <?= $this->element('item_tooltip', array('display' => 'name', 'userItem' => $bladeOfLoyalty['UserItem'], 'displayStyle' => 'font-weight: bold')); ?> and
                            <?= $this->element('item_tooltip', array('display' => 'name', 'userItem' => $devotionGem['UserItem'], 'displayStyle' => 'font-weight: bold')); ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class = 'SectionHeader'>
                Your Referral Link
            </div>

            <div style = 'margin-bottom: 10px'>
                This is your referral link:
                <span style = 'text-decoration: underline'>
                    <? printf('http://%s%s', $_SERVER['SERVER_NAME'], $html->url(array('controller' => 'users', 'action' => 'register', $a_user['User']['username']))); ?></span>
                <br />

                If you copy this URL and give it to a friend, they can use it to sign up for Almasy.
                It should say that they are being referred by you, so that your friend and you can get rewards.
            </div>

            <div class = 'SectionHeader'>
                People You've Referred
            </div>
            <div style = 'left: 50%; margin-left: -350px; position: relative;'>
                <table class = 'ReferTable'>
                    <tr>
                        <th>Username</th>
                        <th>Level 1</th>
                        <th>Level 2</th>
                        <th>Level 3</th>
                    </tr>
                    <? foreach ($referredPeople as $user): ?>
                        <tr>
                            <td style = 'width: 200px'>
                                <?= $user['User']['username']; ?>
                            </td>
                            <? $class = $user['User']['referring_bonuses_given'] >= 1 ? 'Complete' : 'Incomplete'; ?>
                            <td class = '<?= $class; ?>'>
                                <?
                                    if ($user['User']['referring_bonuses_given'] >= 1) {
                                        echo 'Complete!';
                                    } else {
                                        $numBattles = REFERRAL_SYSTEM_LEVEL_1_REQ_BATTLES - $user['User']['battles_won'];
                                        if ($numBattles > 0)
                                            echo $numBattles . ' more victories';
                                        else
                                            echo 'User must login for reward.';
                                    }
                                ?>
                            </td>
                            <? $class = $user['User']['referring_bonuses_given'] >= 2 ? 'Complete' : 'Incomplete'; ?>
                            <td class = '<?= $class; ?>'>
                                <?
                                    if ($user['User']['referring_bonuses_given'] >= 2) {
                                        echo 'Complete!';
                                    } else {
                                        $numBattles = REFERRAL_SYSTEM_LEVEL_2_REQ_BATTLES - $user['User']['battles_won'];
                                        if ($numBattles > 0)
                                            echo $numBattles . ' more victories';
                                        else
                                            echo 'User must login for reward.';
                                    }
                                ?>
                            </td>
                            <? $class = $user['User']['referring_bonuses_given'] >= 3 ? 'Complete' : 'Incomplete'; ?>
                            <td class = '<?= $class; ?>'>
                                <?
                                    if ($user['User']['referring_bonuses_given'] >= 3) {
                                        echo 'Complete!';
                                    } else {
                                        $numBattles = REFERRAL_SYSTEM_LEVEL_3_REQ_BATTLES - $user['User']['battles_won'];
                                        if ($numBattles > 0)
                                            echo $numBattles . ' more victories';
                                        else
                                            echo 'User must login for reward.';
                                    }
                                ?>
                            </td>
                        </tr>
                    <? endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>
