<?= $html->css('items'); ?>

<style type = 'text/css'>
    #Armory {
        width: 370px;
    }

    #Formations {
        float: right;
        width: 560px;
    }

    #Actions {
        width: 370px;
    }

    #ArmoryContent, #FormationsContent, #ActionsContent {
        padding: 4px;
        border: 1px solid;
    }

    #ArmoryContent {
        height: 425px;
        overflow: auto;
        background-color: hsl(24, 5%, 59%);
        <? GradientBackground(array(
            array(0, 'hsl(24, 5%, 65%)'),
            array(0.8, 'hsl(24, 5%, 55%)')
        )); ?>
    }

    #FormationsContent {
        height: 500px;
        background-color: hsl(24, 5%, 74%);
        <? GradientBackground(array(
            array(0, 'hsl(24, 5%, 80%)'),
            array(0.8, 'hsl(24, 5%, 70%)')
        )); ?>
    }

    #ActionsContent {
        height: 50px;
    }

    #ActionsWidget {
        text-align: center;
    }

    .Header {
        border-bottom: 1px dotted;
        font-weight: bold;
        margin-top: -2px;
        padding-bottom: 2px;
    }

    #ArmoryHeader, #FormationHeader {
        text-align: center;
        border: 1px solid;
        <? RoundedCorners('5px'); ?>
        margin-top: 5px;
        margin-bottom: 5px;
        background-color: hsl(44, 73%, 49%);
        <? GradientBackground(array(
            array(0, 'hsl(44, 73%, 50%)'),
            array(0.8, 'hsl(44, 73%, 40%)')
        )); ?>
    }

    .CharacterDiv {
        <? RoundedCorners('3px'); ?>
        border: 1px solid;
        background-color: hsl(230, 70%, 70%);
        <? GradientBackground(array(
            array(0, 'hsl(220, 67%, 70%)'),
            array(0.8, 'hsl(240, 67%, 62%)')
        )); ?>
        padding: 3px;
        margin: 1px;
        position: relative;
    }

    .CanEquip {
        background-color: hsl(110, 77%, 30%);
        <? GradientBackground(array(
            array(0, 'hsl(110, 77%, 35%)'),
            array(0.8, 'hsl(110, 77%, 25%)')
        )); ?>
    }

    .CantEquip {
        background-color: hsl(10, 77%, 30%);
        <? GradientBackground(array(
            array(0, 'hsl(10, 80%, 35%)'),
            array(0.8, 'hsl(10, 80%, 25%)')
        )); ?>
    }

    .CharacterHeader {
        font-weight: bold;
        font-size: 10pt;
        border-bottom: 1px dotted;
        padding: 1px;
    }

    .FormationIcon {
        margin-right: 2px;
        vertical-align: middle;
    }
</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        Armory

        <?= $this->element('items_buttons'); ?>
    </div>

    <div class = 'PageContent'>
        <div style = 'float: left'>
            <div class = 'BorderDiv' id = 'Armory'>
                <div id = 'ArmoryContent'>
                    <div class = 'Header'>
                        Armory
                    </div>
                    <div id = 'ArmoryWidget' style = 'position: relative'>
                    </div>
                </div>
            </div>
            <div class = 'BorderDiv' id = 'Actions'>
                <div id = 'ActionsContent'>
                    <div id = 'ActionsWidget' style = 'position: relative'>
                    </div>
                </div>
            </div>
        </div>
        <div class = 'BorderDiv' id = 'Formations'>
            <div id = 'FormationsContent'>
                <div id = 'FormationWidget' style = 'position: relative'>
                </div>
            </div>
        </div>
        <div style = 'clear: both;'></div>
    </div>
</div>

<script type = 'text/javascript'>
    $(document).ready(function() {
        var weaponImbues = <?= json_encode($weaponImbues); ?>;
        var armorImbues = <?= json_encode($armorImbues); ?>;
        var refineChances = <?= json_encode($refineChances); ?>;
        var classWeaponEquips = <?= json_encode($classWeaponEquips); ?>;
        var weaponSprites = <?= json_encode($weaponSprites); ?>;
        var armorSprites = <?= json_encode($armorSprites); ?>;
        var miscItemSprites = <?= json_encode($miscItemSprites); ?>;

        $('#ArmoryWidget').armoryWidget({
            weaponSprites: weaponSprites,
            armorSprites: armorSprites,
            miscItemSprites: miscItemSprites
        });
        $('#FormationWidget').formationWidget({
            armoryWidget: '#ArmoryWidget',
            classWeaponEquips: classWeaponEquips
        });
        $('#ActionsWidget').actionsWidget({
            armoryWidget: '#ArmoryWidget',
            weaponImbues: weaponImbues,
            armorImbues: armorImbues,
            refineChances: refineChances
        });
    });
</script>
