var HoverPointer = Component.extend({
    //---------------------------------------------------------------------------------------------
    init: function(options, element) {
        $(element).hover(
            function(event) {
                $('body').css('cursor', 'pointer');
            },
            function() {
                $('body').css('cursor', 'auto');
            }
        );
    },
});

$.plugin('hoverPointer', HoverPointer);