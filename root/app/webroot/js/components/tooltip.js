/**
 * This element causes another element (the tooltip) to appear next to this element.
 *
 * Options:
 *     tooltipElement: The element to show when this element is hovered.
 */
var Tooltip = Component.extend({
    init: function(options, element) {
        this.options = $.extend({
            tooltipElement: ''
        }, options);

        this.$element = $(element);
        this.bindEvents();
    },
    bindEvents: function() {
        var obj = this;
        this.$element.hover(
            function(event) { obj._onHoverStart(event); },
            function() { obj._onHoverEnd(); }
        );
    },
    _onHoverStart: function(event) {

        var x = event.pageX;
        var y = event.pageY;

        var $tooltip = $(this.options.tooltipElement);
        $tooltip = $tooltip.clone();

        $tooltip.css({
            top: y + 5,
            left: x + 5,
            position: 'absolute'
        });
        $tooltip.addClass('Tooltip');

        var windowWidth = window.innerWidth;
        var windowHeight = window.innerHeight;

        var windowXOffset = window.pageXOffset;
        var windowYOffset = window.pageYOffset;

        $tooltip.appendTo("body").fadeIn(100);

        var width = $tooltip.attr('offsetWidth');
        var height = $tooltip.attr('offsetHeight');

        // Move tooltip up if it's too low and is going past the end of the window
        var margin = 25;
        if (y + height + margin > windowHeight + windowYOffset) {
            var newHeight = windowHeight + windowYOffset - height - margin;
            $tooltip.css({top: newHeight});
        }

        if (x + width + margin > windowWidth + windowXOffset) {
            var newWidth = windowWidth + windowXOffset - width - margin;
            $tooltip.css({left: newWidth});
        }
    },
    _onHoverEnd: function() {
        $('.Tooltip').remove();
    }
});

$.plugin('tooltip', Tooltip);