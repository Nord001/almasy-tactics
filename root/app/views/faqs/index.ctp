<style type = 'text/css'>
    .QuestionDiv {
        margin-bottom: 20px;
    }

    .CategoryHeader {
        font-size: 140%;
        border-bottom: 1px dotted rgb(128, 128, 128);
        width: 750px;
        margin-bottom: 5px;
    }

    .CategoryHeader a {
        color: rgb(0, 0, 0);
    }

    .Question:hover, .CategoryHeader a:hover {
        text-decoration: none;
    }

    .Question {
        color: rgb(130, 0, 0);
    }

    .Answer {
        text-align: justify;
    }

    #TableContents ul li {
        margin-bottom: 2px;
    }

    ul {
        margin-top: 0px;
        list-style: none;
        margin-left: 0px;
        padding-left: 0px;
    }

    #TableContents {
        float: left;
        width: 150px;
        font-size: 120%;
    }

    #QuestionContents {
        float: right;
        width: 760px;
        margin-right: 40px;
    }

</style>

<div class = 'PageDiv'>
    <div class = 'PageTitle'>
        <?= $html->link2('Help', array('controller' => 'help', 'action' => 'index')); ?> |
        Frequently Asked Questions
    </div>

    <div class = 'PageContent' style = 'padding-left: 10px; padding-bottom: 700px;'>
        <div id = 'TableContents'>
            <div style = 'font-size: 110%; border-bottom: 1px dotted rgb(128, 128, 128)'>
                Contents
            </div>
            <ul>
                <? $i = 0; ?>
                <? foreach ($faqs as $category => $categoryFaqs): ?>
                    <li>
                        <a href = '#Category<?= $i++; ?>'>
                            <?= $category; ?>
                        </a>
                    </li>
                <? endforeach; ?>
            </ul>
        </div>

        <div id = 'QuestionContents'>
            <? $i = 0; ?>
            <? foreach ($faqs as $category => $categoryFaqs): ?>
                <div class = 'CategoryHeader'>
                    <a name = 'Category<?= $i++; ?>'>
                        <?= $category; ?>
                    </a>
                </div>

                <? foreach ($categoryFaqs as $faq): ?>
                    <div class = 'QuestionDiv'>
                        <div class = 'Question'>
                            <a name = '<?= $faq['Faq']['id']; ?>' class = 'Question'>
                                <?= $faq['Faq']['question']; ?>
                            </a>
                        </div>

                        <div class = 'Answer'>
                            <?= $faq['Faq']['answer']; ?>
                        </div>
                    </div>
                <? endforeach; ?>

                <div style = 'margin-bottom: 20px; font-size: 110%'>
                    <a href = '#' onclick = 'window.scrollTo(0,0); return false;'>Back to Top</a>
                </div>
            <? endforeach; ?>
        </div>

        <div style = 'clear: both;'></div>
    </div>
</div>