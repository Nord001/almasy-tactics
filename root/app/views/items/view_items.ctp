<script type = 'text/javascript'>
    var itemType = '<?= $itemType ?>';
    var itemTypeId = <?= $itemTypeId ?>;
</script>
<?= $javascript->link('pages/store'); ?>

<div class = 'ItemListDiv  BorderDiv'>
    <div class = 'ItemListDivContent'>
        <div class = 'WeaponTypeHeader'><?= Inflector::pluralize($typeName); ?></div>

        <table class = 'item-list' style = 'width: 100%;'>
            <? foreach ($items as $item): ?>
                <tr userItemId = '<?= $item['UserItem']['id']; ?>' itemName = '<?= $item['UserItem']['name']; ?>'>
                    <td style = 'width: 10px; padding-right: 5px;'><?= $ui->displayItemIcon($item['UserItem']['Item']['sprite']); ?></td>
                    <td><?= $this->element('item_tooltip', array('display' => 'name', 'userItem' => $item['UserItem'])); ?></td>
                    <td>Lv. <?= $item['UserItem']['Item']['req_lvl']; ?></td>

                    <? $cost = $item['ItemCatalogEntry']['use_item_value'] ? $item['UserItem']['Item']['value'] : $item['ItemCatalogEntry']['cost']; ?>
                    <td><?= number_format($cost); ?> yb</td>

                    <? $disabled = $a_user['User']['money'] < $cost ? 'disabled' : ''; ?>
                    <td><input type = 'button' style = 'width: 60px; height: 30px;' value = 'Buy' <?= $disabled; ?>  /></td>
                </tr>
            <? endforeach; ?>
        </table>
    </div>
</div>

<div id = 'BuyDialog' style = 'display: none' title = 'Processing...'>Please Wait...</div>