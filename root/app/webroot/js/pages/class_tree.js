$(document).ready(function() {
    var selectedCell = null;
    var changeTriggered = false;

    var tooltips = $('div[class=ClassTooltip]');

    for (var i = 0; i < tooltips.length; i++) {
        var hoverElem = $(tooltips[i]).parent();

        var div = tooltips[i];

        AddTooltip(hoverElem, div, false);
    }

    $('div[classId]').click(function() {
        window.location = '<?= $html->url(array('controller' => 'help', 'action' => 'view_class')); ?>/' + $(this).attr('classId');
    });

    $('div[classId]').hover(
        function() {
            var className = $(this).find('.ClassName');
            className.attr('oldBg', className.css('background-color'));
            className.css('background-color', 'rgb(240, 120, 120)');
            $('body').css('cursor', 'pointer');
        },
        function() {
            var className = $(this).find('.ClassName');
            className.css('background-color', className.attr('oldBg'));
            $('body').css('cursor', 'auto');
        }
    );
});
