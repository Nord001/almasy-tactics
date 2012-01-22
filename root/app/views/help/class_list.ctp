<style type = 'text/css'>
    #ClassTree ul {
        margin-top: 15px;
    }

    .TreeElem {
        margin-left: 10px;
    }

    .Expandable {
        text-decoration: underline;
    }

    .Expandable:hover {
        cursor: pointer;
        color: rgb(200, 0, 0);
    }

    .BranchDiv {
        font-weight: bold;
        margin-top: 10px;
        border: 1px solid;
        border-radius: 4px;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;
        width: 435px;
        padding: 5px;
    }

    .ClassTooltip {
        position: absolute;
        border: 1px solid #333;
        border-radius: 3px;
        -moz-border-radius: 3px;
        padding: 4px;
        color: #fff;
        background-color: #000;
        width: 100px;
    }

</style>

<? define('COMMENTS_PER_PAGE', 3); ?>

<div class = 'PageDiv'>

    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> | Class List
    </div>

    <div class = 'PageContent' style = 'position: relative'>

        <? require '../views/help/navbar.ctp'; ?>

        <div class = 'HelpPageHeader'>Class List</div>

        <div class = 'StatHeader'>
            Quick Find
        </div>
        <div class = 'StatContent'>
            Type in the name of the class exactly. Uppercase is not necessary.
            <form>
                <input type = 'text' id = 'ClassSearchInput' />
                <input type = 'submit' style = 'width: auto; height: auto; font-size: 10pt;' value = 'Quick Find' id = 'ClassSearchButton' />
            </form>

            <script type = 'text/javascript'>
                var url = '<?= $html->url(array('controller' => 'help', 'action' => 'view_class')); ?>';
                $(document).ready(function() {
                    $('#ClassSearchButton').click(function(event) {
                        event.preventDefault();
                        window.location = url + '/' + $('#ClassSearchInput').val();
                    });
                });
            </script>
        </div>

        <div class = 'StatHeader'>
            Full Class Branch Charts
        </div>
        <div class = 'StatContent'>
            <div class = 'BranchDiv' style = 'background-color: rgb(225, 150, 150)'>
                <?= $html->image('sprites/Swordsman.png', array('class' => 'face-icon')); ?>
                <?= $html->image('sprites/Master Knight.png', array('class' => 'face-icon')); ?>
                <?= $html->image('sprites/Champion.png', array('class' => 'face-icon')); ?>
                <?= $html->image('sprites/Monarch.png', array('class' => 'face-icon')); ?>
                <?= $html->link2('Swordsman Branch', '/help/class_tree/swordsman'); ?>
            </div>

            <div class = 'BranchDiv' style = 'background-color: rgb(150, 150, 225)'>
                <?= $html->image('sprites/Spellcaster.png', array('class' => 'face-icon')); ?>
                <?= $html->image('sprites/Rune Master.png', array('class' => 'face-icon')); ?>
                <?= $html->image('sprites/Soothsayer.png', array('class' => 'face-icon')); ?>
                <?= $html->image('sprites/Warlock.png', array('class' => 'face-icon')); ?>
                <?= $html->link2('Spellcaster Branch', '/help/class_tree/spellcaster'); ?>
            </div>

            <div class = 'BranchDiv' style = 'background-color: rgb(150, 200, 150)'>
                <?= $html->image('sprites/Trainee.png', array('class' => 'face-icon')); ?>
                <?= $html->image('sprites/Mastersmith.png', array('class' => 'face-icon')); ?>
                <?= $html->image('sprites/Melee Hyper Battlesuit.png', array('class' => 'face-icon')); ?>
                <?= $html->image('sprites/Apothecary.png', array('class' => 'face-icon')); ?>
                <?= $html->link2('Trainee Branch', '/help/class_tree/trainee'); ?>
            </div>

        </div>

        <div class = 'StatHeader'>
            Class List (Alphabetical)
        </div>
        <div class = 'StatContent'>
            <table style = 'width: 100%'>
                <?
                    $ids = array_keys($classListing);
                    $classes = array_values($classListing);
                    $NUM_COLS = 6;
                    $rows = ceil(count($classListing) / $NUM_COLS);
                ?>
                <? for ($i = 0; $i < $rows; $i++): ?>
                    <tr>
                        <? for ($j = 0; $j < $NUM_COLS; $j++): ?>
                            <? $index = $j * $rows + $i; ?>
                            <td>
                                <? if (isset($ids[$index])): ?>
                                    <?= $html->link2($classes[$index]['name'], array('controller' => 'help', 'action' => 'view_class', $ids[$index])); ?>

                                    <? if ($classes[$index]['face_icon'] != ''): ?>
                                        <div class = 'ClassTooltip' style = 'display: none'>
                                            <?
                                                $icon = $ui->getFaceIcon($classes[$index]['face_icon']);
                                                echo $html->image($icon);
                                            ?>
                                        </div>
                                    <? endif; ?>
                                <? endif; ?>
                            </td>
                        <? endfor; ?>
                    </tr>
                <? endfor; ?>
            </table>
        </div>

        <? /* ?>

        <div class = 'StatHeader'>
            Community Comments
        </div>
        <div style = 'padding: 3px'>
            <div style = 'padding: 5px;'>
                <table style = 'width: 100%'>
                    <tr>
                        <td>
                            <a href = '#comment'>Post a Comment</a> | Sort by: <a id = 'SortDateLink' href = '#'>Date</a>, <a id = 'SortRatingLink' href = '#'>Rating</a>
                        </td>
                        <td style = 'text-align: right'>
                            <a id = 'Link_First' href = '#'>First</a>
                            <a id = 'Link_Prev' href = '#'>Prev</a>
                            <span id = 'CurrentPageMin'>1</span> -
                            <span id = 'CurrentPageMax'><?= COMMENTS_PER_PAGE; ?></span> of
                            <?= count($comments); ?>
                            <a id = 'Link_Next' href = '#'>Next</a>
                            <a id = 'Link_Last' href = '#'>Last</a>
                        </td>
                    </tr>
                </table>
            </div>

            <div id = 'Comments'>
                <? foreach ($comments as $comment): ?>
                    <div class = 'HelpComment' style = 'display: none'>
                        <div class = 'HelpCommentHeader'>
                            <?= $html->link2($comment['HelpComment']['User']['username'], array('controller' => 'users', 'action' => 'profile', $comment['HelpComment']['User']['username'])); ?>,
                            <?= $time->GetTimeAgoString(strtotime($comment['HelpComment']['time'])); ?>
                            <span class = 'HelpCommentTime' style = 'display: none'><?= strtotime($comment['HelpComment']['time']); ?></span>
                            <a class = 'ReplyLink' href = '#'>Reply</a>

                            <div style = 'position: absolute; top: 0px; right: 0px;'>
                                <?
                                    $color = '';
                                    if ($comment['HelpComment']['rating'] > 0)
                                        $color = 'rgb(0, 150, 0)';
                                    else if ($comment['HelpComment']['rating'] < 0)
                                        $color = 'rgb(150, 0, 0)';
                                ?>
                                <span style = 'font-weight: bold;'>
                                    <span style = 'color: <?= $color; ?>;'>
                                        Rating: <span class = 'HelpCommentRating'><? printf('%+d', $comment['HelpComment']['rating']); ?></span>
                                    </span>
                                    <a class = 'VoteLink' href = '#' direction = '1'>[+]</a>
                                    <a class = 'VoteLink' href = '#' direction = '-1'>[-]</a>
                                </span>
                            </div>
                        </div>

                        <?
                            $color = '';
                            if ($comment['HelpComment']['rating'] > 10)
                                $color = rgb(0, 100, 0);
                        ?>
                        <div class = 'HelpCommentContent' style = 'color: <?= $color; ?>'>
                            <?= $comment['HelpComment']['comment']; ?>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>

            <div class = 'CommentForm'>
                <div class = 'CommentFormHeader' style = 'font-size: 120%'>
                    <a name = 'comment' class = 'NamedAnchor'>Post A Comment</a>
                </div>

                <form>
                    <div>
                        <input type = 'hidden' name = 'page' value = '<?= h($_SERVER['REQUEST_URI']); ?>' />
                        <input type = 'hidden' name = 'parent_id' value = '' />
                        <textarea name = 'Textarea_Comment' style = 'width: 560px; height: 200px'></textarea>
                    </div>

                    <div style = 'padding: 3px'>
                        <input class = 'CommentSubmit' type = 'button' value = 'Comment!' style = 'font-size: 100%; height: 30px;' />
                        <?= $html->image('cycle.gif', array('style' => 'margin-right: 2px; vertical-align: middle; display: none;', 'id' => 'Img_CommentLoading')); ?>
                    </div>
                </form>
            </div>
        </div>

        <? */ ?>
    </div>
</div>

<script type = 'text/javascript'>
    var currentPage = 2;
    var commentsPerPage = <?= COMMENTS_PER_PAGE; ?>;

    function SortComments (mode) {
        var commentDiv = $('#Comments');
        var comments = commentDiv.children();
        if (comments.length > 1) {
            var sortedComments = [];
            for (var i = 0; i < comments.length; i++) {
                var date = parseInt(comments.eq(i).find('.HelpCommentTime').text());
                sortedComments[sortedComments.length] = {
                    'index': i,
                    'date': date,
                    'rating': parseInt(comments.eq(i).find('.HelpCommentRating').text())
                };
            }

            if (mode == 'rating')
                sortedComments.sort(function(a, b) { return b.rating - a.rating; });
            else if (mode == 'date')
                sortedComments.sort(function(a, b) { return a.date - b.date; });

            comments.remove();
            $.each(
                sortedComments,
                function (i, v) {
                    commentDiv.append(comments.eq(v.index));
                }
            );
            Page();
        }
    }

    function Page () {
        var commentDiv = $('#Comments');
        var comments = commentDiv.children();

        var minIndex = (currentPage - 1) * commentsPerPage;
        var maxIndex = minIndex + commentsPerPage - 1;

        if (minIndex >= comments.length)
            minIndex = comments.length - 1;
        if (maxIndex >= comments.length)
            maxIndex = comments.length - 1;


        $('#CurrentPageMin').text(minIndex + 1);
        $('#CurrentPageMax').text(maxIndex + 1);

        comments.show();

        $.each(
            comments,
            function (i, v) {
                if (!(i >= minIndex && i <= maxIndex)) {
                    $(v).hide();
                }
            }
        );
    }

    $(document).ready(function() {
        Page();

        var commentForm = $('div.CommentForm');
        $('a.ReplyLink').click(function(event) {
            event.preventDefault();

            var replyForm = commentForm.clone();
            var commentDiv = $(this).parent().parent();
            replyForm.appendTo(commentDiv);
        });

        $('#SortDateLink').click(function(event) {
            event.preventDefault();

            SortComments('date');
        });

        $('#SortRatingLink').click(function(event) {
            event.preventDefault();

            SortComments('rating');
        });

        $('#Link_First').click(function(event) {
            event.preventDefault();

            currentPage = 1;
            Page();
        });

        $('#Link_Prev').click(function(event) {
            event.preventDefault();

            currentPage--;
            if (currentPage < 1)
                currentPage = 1;
            Page();
        });

        $('#Link_Next').click(function(event) {
            event.preventDefault();

            var maxPages = Math.ceil($('#Comments').children().length / commentsPerPage);
            currentPage++;
            if (currentPage > maxPages)
                currentPage = maxPages;
            Page();
        });

        $('#Link_Last').click(function(event) {
            event.preventDefault();

            var maxPages = Math.ceil($('#Comments').children().length / commentsPerPage);
            currentPage = maxPages;
            Page();
        });

        var tooltips = $('.ClassTooltip');
        for (var i = 0; i < tooltips.length; i++) {
            var tooltip = $(tooltips[i]);
            var link = tooltip.parent().find('a');

            link.tooltip({tooltipElement: tooltip});
        }

       $('.BranchDiv').click(function(event) {
            var link = $(this).children('a');
            window.location = link.attr('href');
        });

        $('.BranchDiv').hover(
            function() {
                $('body').css('cursor', 'pointer');
            },
            function() {
                $('body').css('cursor', 'auto');
            }
        );
    });
</script>
