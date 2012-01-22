/**
 * This element causes another element (the tooltip) to appear next to this element.
 *
 * Options:
 *     tooltipElement: The element to show when this element is hovered.
 */
var HelpTooltip = Component.extend({
    init: function(options, element) {
        this.$element = $(element);
        this.$icon = this.$element.children('img');
        this.$tooltipContent = this.$element.children('div');
        this.$showingTooltip = null;
        this.bindEvents();
    },
    bindEvents: function() {
        var obj = this;
        this.$icon.click(
            function(event) { obj._onClick(event); }
        );
    },
    _onClick: function(event) {
        if (this.$showingTooltip) {
            this.$showingTooltip.remove();
            this.$showingTooltip = null;
            return;
        }

        var x = event.pageX;
        var y = event.pageY;

        var $tooltip = this.$tooltipContent.clone();
        $tooltip.css({
            top: y + 5,
            left: x + 5
        });

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

        this.$showingTooltip = $tooltip;
    }
});

$.plugin('helpTooltip', HelpTooltip);