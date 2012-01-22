<table>
    <tr>
        <td>
            <?= $ui->displayItemIcon($item['Item']['sprite']); ?>
        </td>
        <td>
            <?= $item['name']; ?>

            <!-- Unequip link -->
            <?
                if ($item['CharacterEquipped']['id'] != '') {
                    echo "({$item['CharacterEquipped']['name']}) ";
                    //echo $html->link2('Unequip', '#', array('id' => $unequipLinkId));
                }

            ?>
        </td>
    </tr>
</table>