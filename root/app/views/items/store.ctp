<?= $html->css('items'); ?>

<style type = 'text/css'>
    .item-list tr td { height: 40px; border-bottom: 1px dotted; }

    .ItemListDivContent {
        background-color: rgb(224, 208, 208);
        <? GradientBackground(array(
            array(0, 'rgb(224, 208, 208)'),
            array(1, 'rgb(210, 180, 180)')
        )); ?>
        border: 1px solid;
        padding: 5px;
    }

    .ItemListDiv {
        width: 500px;
    }

    .WeaponTypeHeader {
        font-size: 120%;
        border-bottom: 1px dotted;
    }

    .WeaponTypeListDiv {
        float: left;
        width: 190px;
    }

    .WeaponTypeListDivContent {
        background-color: rgb(208, 208, 244);
        <? GradientBackground(array(
            array(0, 'rgb(208, 208, 244)'),
            array(1, 'rgb(210, 180, 220)')
        )); ?>
        border: 1px solid;
        padding: 5px;
    }
</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Armory', array('controller' => 'items', 'action' => 'index')); ?> | Store

        <?= $this->element('items_buttons'); ?>
    </div>

    <div class = 'PageContent' id = 'StorePage'>
        <div class = 'WeaponTypeListDiv BorderDiv'>
            <div class = 'WeaponTypeListDivContent'>
                <div class = 'WeaponTypeHeader'>
                    Weapons
                </div>
                <table>
                    <? foreach ($weaponTypes as $type): ?>
                        <tr>
                            <td style = 'padding-right: 5px; text-align: center;'><?= $html->image('sprites/' . $type['WeaponType']['sprite'] . '.png'); ?></td>
                            <td><a href = '#' type = 'weapon' typeId = '<?= $type['WeaponType']['id']; ?>'><?= Inflector::pluralize($type['WeaponType']['name']); ?></a></td>
                        </tr>
                    <? endforeach; ?>
                </table>
                <div class = 'WeaponTypeHeader'>
                    Armor
                </div>
                <table>
                    <? foreach ($armorTypes as $type): ?>
                        <tr>
                            <td style = 'padding-right: 5px; text-align: center;'><?= $html->image('sprites/' . $type['ArmorType']['sprite'] . '.png'); ?></td>
                            <td><a href = '#' type = 'armor' typeId = '<?= $type['ArmorType']['id']; ?>'><?= $type['ArmorType']['name']; ?></a></td>
                        </tr>
                    <? endforeach; ?>
                </table>
            </div>
        </div>

        <script type = 'text/javascript'>
            // Posts the item view change. Called when you buy an item to refresh.
            function ShowItemView (type, typeId) {
                $('#StoreLoadingGif').fadeIn(200);
                $.post(
                    '<?= $html->url(array('controller' => 'items', 'action' => 'view_items')); ?>',
                    {
                        type: type,
                        typeId: typeId
                    },
                    function(data) {
                        $('#ItemsView').html(data);
                        $('#StoreLoadingGif').fadeOut(200);
                    }
                );
            }

            $(document).ready(function() {
                $('a[type]').click(function(event) {
                    event.preventDefault();
                    ShowItemView($(this).attr('type'), $(this).attr('typeId'));
                });
            });
        </script>

        <div style = 'float: right; width: 750px' id = 'ItemsView'>
        </div>

        <div style = 'clear: both'></div>
    </div>
</div>

