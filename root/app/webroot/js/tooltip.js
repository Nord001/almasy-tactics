
//---------------------------------------------------------------------------------------------
function ShowTooltip (x, y, contents, className) {
    var tooltip = $('<div class = "HoveringTooltip">' + contents + '</div>').css(
        {
            top: y + 5,
            left: x + 5
        }
    );
    if (className != false)
        tooltip.addClass(className);
    
    var windowWidth = window.innerWidth;
    var windowHeight = window.innerHeight;

    var windowXOffset = window.pageXOffset;
    var windowYOffset = window.pageYOffset;
    
    tooltip.appendTo("body").fadeIn(100);
    
    var width = tooltip.attr('offsetWidth');
    var height = tooltip.attr('offsetHeight');

    // Move tooltip up if it's too low and is going past the end of the window
    var margin = 25;
    if (y + height + margin > windowHeight + windowYOffset) {
        var newHeight = windowHeight + windowYOffset - height - margin;
        tooltip.css({top: newHeight});
    }

    if (x + width + margin > windowWidth + windowXOffset) {
        var newWidth = windowWidth + windowXOffset - width - margin;
        tooltip.css({left: newWidth});
    }
}

//---------------------------------------------------------------------------------------------
function AddTooltip (hoverObj, tooltip, className) {
    $(hoverObj).hover(
        // Mouse over
        function(event) {
            ShowTooltip(event.pageX, event.pageY, $(tooltip).html(), className != false ? className : $(tooltip).attr('class'));
        },
        // Mouse out
        function(event) {
            $('.HoveringTooltip').remove();
        }
    );
}
// Activate all item tooltips
function ActivateItemTooltips () {
    $(document).ready(function() {
        var tooltips = $('div[class=ItemTooltip]');

        for (var i = 0; i < tooltips.length; i++) {
            var span = $(tooltips[i]).children('span.ItemName').get(0);

            // If span doesn't exist, get img instead
            if (span == null)
                span = $(tooltips[i]).children('img').get(0);

            var div = $(tooltips[i]).children('div').get(0);

            // Wire up tooltip so that hovering over the span shows the div
            AddTooltip(span, div, 'ItemTooltipData');
        }
    });
}
