<form method = 'POST' action = '/admi/battles/simulator'>
    <fieldset>
        <legend>Battle Simulator</legend>

        <table style = 'width: 700px'>
            <tr>
                <td>
                    <h2>Attacking Formation</h2>

                    <label>Reputation</label>
                    <input name = 'data[AttackingFormation][reputation]' type = 'text' value = '<?= isset($this->data['AttackingFormation']['reputation']) ? $this->data['AttackingFormation']['reputation']: ''; ?>' class = 'Reputation' />

                    <label>Level (for Reputation)</label>
                    <input name = 'data[AttackingFormation][level]' type = 'text' value = '<?= isset($this->data['AttackingFormation']['level']) ? $this->data['AttackingFormation']['level']: ''; ?>' class = 'Level' />

                    <label>Bounty</label>
                    <input name = 'data[AttackingFormation][bounty]' type = 'text' value = '<?= isset($this->data['AttackingFormation']['bounty']) ? $this->data['AttackingFormation']['bounty']: ''; ?>' />

                    <label>Attacker HP % Remaining</label>
                    <input name = 'data[attacker_hp_percent]' type = 'text' value = '<?= isset($this->data['attacker_hp_percent']) ? $this->data['attacker_hp_percent']: ''; ?>' />
                </td>
                <td>
                    <h2>Defending Formation</h2>

                    <label>Reputation</label>
                    <input name = 'data[DefendingFormation][reputation]' type = 'text' value = '<?= isset($this->data['DefendingFormation']['reputation']) ? $this->data['DefendingFormation']['reputation']: ''; ?>' class = 'Reputation' />

                    <label>Level (for Reputation)</label>
                    <input name = 'data[DefendingFormation][level]' type = 'text' value = '<?= isset($this->data['DefendingFormation']['level']) ? $this->data['DefendingFormation']['level']: ''; ?>' class = 'Level' />

                    <label>Bounty</label>
                    <input name = 'data[DefendingFormation][bounty]' type = 'text' value = '<?= isset($this->data['DefendingFormation']['bounty']) ? $this->data['DefendingFormation']['bounty']: ''; ?>' />

                    <label>Defender HP % Remaining</label>
                    <input name = 'data[defender_hp_percent]' type = 'text' value = '<?= isset($this->data['defender_hp_percent']) ? $this->data['defender_hp_percent']: ''; ?>' />
                </td>
            </tr>
        </table>

        <label>Who wins?</label>
        <input type = 'radio' name = 'data[victor]' value = 'attacker' style = 'width: auto' id = 'AttackerWins' <?= isset($this->data['victor']) && $this->data['victor'] == 'attacker' ? 'checked' : ''; ?> />
        <label style = 'display: inline' for = 'AttackerWins'>Attacker</label>
        <input type = 'radio' name = 'data[victor]' value = 'defender' style = 'width: auto' id = 'DefenderWins' <?= isset($this->data['victor']) && $this->data['victor'] == 'defender' ? 'checked' : ''; ?> />
        <label style = 'display: inline' for = 'DefenderWins'>Defender</label>
        <input type = 'radio' name = 'data[victor]' value = '' style = 'width: auto' id = 'Tie' <?= isset($this->data['victor']) && $this->data['victor'] == '' ? 'checked' : ''; ?> />
        <label style = 'display: inline' for = 'Tie'>Tie</label>
    </fieldset>
    <input type = 'submit' value = 'Submit' />
</form>

<? if(isset($result)): ?>
    <?= $this->data['AttackingFormation']['reputation']; ?> Rep <?= $this->data['AttackingFormation']['bounty']; ?> bounty formation
    attacks
    <?= $this->data['DefendingFormation']['reputation']; ?> Rep <?= $this->data['DefendingFormation']['bounty']; ?> bounty formation.
    <?
        $victor = isset($this->data['victor']) ? $this->data['victor'] : '';
        if ($victor == '')
            $victor = 'tie';
    ?>
    Victor: <?= ucfirst($victor); ?> <br />

    <?
        $rA = $this->data['AttackingFormation']['reputation'];
        $rB = $this->data['DefendingFormation']['reputation'];

        $qA = $rA * $rA * $rA;
        $qB = $rB * $rB * $rB;

        $eA = $qA / ($qA + $qB);
        $eB = 1 - $eA;
    ?>

    eA: <?= $eA; ?>
    eB: <?= $eB; ?>

    <table style = 'width: 500px'>
        <tr>
            <th></th>
            <th>Before</th>
            <th>Change</th>
            <th>After</th>
        </tr>
        <tr>
            <td>Reputation (A)</td>
            <td><?= $this->data['AttackingFormation']['reputation']; ?></td>
            <td style = 'color: <?= $result['attackerRepD'] > 0 ? 'rgb(0, 150, 0)' : 'rgb(150, 0, 0)'; ?>'><? printf('%+d', $result['attackerRepD']); ?></td>
            <td><?= $result['attackerRep']; ?></td>
        </tr>
        <tr>
            <td>Reputation (D)</td>
            <td><?= $this->data['DefendingFormation']['reputation']; ?></td>
            <td style = 'color: <?= $result['defenderRepD'] > 0 ? 'rgb(0, 150, 0)' : 'rgb(150, 0, 0)'; ?>'><? printf('%+d', $result['defenderRepD']); ?></td>
            <td><?= $result['defenderRep']; ?></td>
        </tr>
        <tr>
            <td>Bounty (A)</td>
            <td><?= $this->data['AttackingFormation']['bounty']; ?></td>
            <td style = 'color: <?= $result['attackerBountyD'] > 0 ? 'rgb(0, 150, 0)' : 'rgb(150, 0, 0)'; ?>'><? printf('%+d', $result['attackerBountyD']); ?></td>
            <td><?= $result['attackerBounty']; ?></td>
        </tr>
        <tr>
            <td>Bounty (D)</td>
            <td><?= $this->data['DefendingFormation']['bounty']; ?></td>
            <td style = 'color: <?= $result['defenderBountyD'] > 0 ? 'rgb(0, 150, 0)' : 'rgb(150, 0, 0)'; ?>'><? printf('%+d', $result['defenderBountyD']); ?></td>
            <td><?= $result['defenderBounty']; ?></td>
        </tr>
        <tr>
            <td>Yuanbao (A)</td>
            <td></td>
            <td><?= $result['attackerYbReward']; ?></td>
            <td></td>
        </tr>
        <tr>
            <td>Yuanbao (D)</td>
            <td></td>
            <td><?= $result['defenderYbReward']; ?></td>
            <td></td>
        </tr>
        <tr>
            <td>EXP (A)</td>
            <td></td>
            <td><?= $result['attackerExpReward']; ?></td>
            <td></td>
        </tr>
        <tr>
            <td>EXP (D)</td>
            <td></td>
            <td><?= $result['defenderExpReward']; ?></td>
            <td></td>
        </tr>
    </table>
<? endif; ?>

<script type = 'text/javascript'>
    $(document).ready(function() {
        $('.Level').keyup(function() {
            var level = parseInt($(this).val());

            if (isNaN(level))
                return;

            var reputation = Math.floor(7 * (0.4 * Math.pow(level, 2) + level));

            $(this).parent().find('.Reputation').val(reputation);
        });
    });
</script>