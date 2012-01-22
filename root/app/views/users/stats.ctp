<style type = 'text/css'>
    .PageContent table {
        /*width: 700px;*/
    }

    .PageContent .TacStatHeader {
        font-size: 140%;
        border-bottom: 1px dotted rgb(128, 128, 128);
    }

    .PageContent .StatData {
        text-align: right;
        width: 50px;
    }

    .StatHeaderTd {
        width: 610px;
    }

    .IncreaseButtonTd {
        padding-left: 5px;
        width: 20px;
    }

    .PageContent td {
        padding-bottom: 5px;
    }

    .PageContent {
        position: relative;
    }

    #LevelHeader, #ExpHeader {
        font-size: 140%;
    }

    #ExpHeader {
        position: absolute;
        right: 0px;
        top: 0px;
    }

    #LevelBar {
        margin-bottom: 15px;
        position: relative;
        width: 700px;
    }

    .IncreaseButtonTd {
        text-align: right;
    }
</style>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2($a_user['User']['username'], array('controller' => 'users', 'action' => 'profile')); ?> |
        Tactician Traits
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px;'>
        <div style = 'position: relative; margin-left: -350px; left: 50%'>
            <div id = 'LevelBar'>
                <div id = 'LevelHeader'>
                    Level <?= $user['User']['level']; ?>
                </div>
                <div id = 'ExpHeader'>
                    EXP. <?= $user['User']['exp']; ?> / <?= $user['User']['total_exp_to_next_level']; ?>
                </div>
                <div style = 'width: 700px; border: 1px solid; height: 5px; background-color: rgb(225, 225, 240)'>
                    <? $percent = intval($user['User']['exp'] / $user['User']['total_exp_to_next_level'] * 100); ?>
                    <? if ($percent > 0): ?>
                        <div style = 'width: <?= $percent ?>%; height: 5px; background-color: rgb(255, 255, 255); border-right: 1px solid;'>
                        </div>
                    <? endif; ?>
                </div>
            </div>

            <div id = 'StatPage'>
                <?= $this->element('ajax_stat_page'); ?>
            </div>
        </div>
    </div>
</div>

<script type = 'text/javascript'>
    function WireButtons () {
        $('.IncreaseButton').click(function(event) {
            event.preventDefault();

            var stat = $(this).attr('name');
            $.post(
                '/users/increase_stat',
                { stat: stat },
                function(data) {
                    $('#StatPage').html(data);
                    WireButtons();
                }
            );
        });
    }

    $(document).ready(function() {
        WireButtons();
    });
</script>