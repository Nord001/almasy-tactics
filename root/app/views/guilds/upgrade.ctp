<style type = 'text/css'>
    .Header {
        border-bottom: 1px dotted;
        font-size: 140%;
        margin-top: 10px;
    }
</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2($guild['Guild']['name'], array('controller' => 'guilds', 'action' => 'view', $guild['Guild']['id'])); ?> | Upgrade Guild
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        Here are the upgrades you can buy for your guild. The cost is deducted from guild funds.

        <div>
            The guild currently has <b><?= number_format($guild['Guild']['money']); ?></b> yb.
        </div>

        <div class = 'Header'>
            Emblem
        </div>
        <? if ($guild['Guild']['can_have_emblem']): ?>
            You have this upgrade.
        <? else: ?>
            You can buy an emblem for your guild, which allows you to choose a picture to use as the guild emblem.
            An emblem costs <?= number_format(GUILD_EMBLEM_COST); ?> yb.
            <?= $form->create('Guild', array('action' => 'upgrade')); ?>
                <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
                <input type = 'hidden' name = 'data[type]' value = 'emblem' />
                <input type = 'submit' value = 'Buy Emblem' style = 'width: 200px' />
            </form>
        <? endif; ?>

        <div class = 'Header'>
            Level Upgrade
        </div>
        You can increase the level of your guild. Your guild is level <?= $guild['Guild']['level']; ?>,
        and can get to the next level with <?= number_format($guild['Guild']['level_up_cost']); ?> yb.
        <?= $form->create('Guild', array('action' => 'upgrade')); ?>
            <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
            <input type = 'hidden' name = 'data[type]' value = 'level' />
            <input type = 'submit' value = 'Upgrade Level' style = 'width: 200px' />
        </form>

        <div class = 'Header'>
            Size Upgrade
        </div>
        <? if ($guild['Guild']['size_level'] >= GUILD_SIZE_MAX_LEVEL): ?>
            You have the maximum level of this upgrade.
        <? else: ?>
            You can increase the size of your guild, which allows more players to join. Your guild is at size level <?= $guild['Guild']['size_level']; ?>,
            and can have <?= $guild['Guild']['max_size']; ?> members. You can upgrade your size and add <?= GUILD_SIZE_INCREASE; ?> more members
            for <?= number_format($guild['Guild']['size_upgrade_cost']); ?> yb.
            <?= $form->create('Guild', array('action' => 'upgrade')); ?>
                <?= $form->hidden('csrf_token', array('name' => 'data[csrf_token]', 'value' => $a_user['User']['csrf_token'])); ?>
                <input type = 'hidden' name = 'data[type]' value = 'size' />
                <input type = 'submit' value = 'Upgrade Size' style = 'width: 200px' />
            </form>
        <? endif; ?>
    </div>
</div>