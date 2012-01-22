<style type = 'text/css'>

#ChangePortraitContent img {
    border: 2px solid rgb(75, 75, 75);
}

</style>

<div class = 'PageDiv'>
    <div class = 'PageTitle'>
        <?= $html->link2($a_user['User']['username'], array('controller' => 'users', 'action' => 'profile')); ?> |
        Change Portrait
    </div>

    <div class = 'PageContent' id = 'ChangePortraitContent'>
        <table>
            <?
                $COLS = 9;
                $list = PORTRAIT_LIST();
            ?>

            <? for ($i = 0; $i < count($list); $i += $COLS): ?>
                <tr>
                    <? for ($index = $i; $index < ($i + $COLS) && $index < count($list); $index++): ?>
                        <td>
                            <?
                                $portrait = $list[$index];
                            ?>
                            <?= $html->link2(
                                    $html->image('sprites/' . $portrait . '.png'),
                                    array('controller' => 'users', 'action' => 'change_portrait', $portrait),
                                    null,
                                    null,
                                    false
                                );
                            ?>
                        </td>
                    <? endfor; ?>
                </tr>
            <? endfor; ?>
        </table>
    </div>
</div>