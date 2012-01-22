var ShrinkText = Component.extend({
    init: function(options, element) {
        this.options = $.extend({
            maxSize: 50,
            width: null,
            shrinkOffset: null,
        }, options);
        var $element = $(element);

        if ($element[0].currentStyle !== undefined) {
            // This is IE, atm unsupported.
            return;
        }

        var measure = $.create('span').hide().appendTo(document.body);
        measure.html($element.html());

        // Get computed width. For div, this will be equivalent to the css width.
        // For span, it will be the existing width.
        var desiredWidth = this.options.width;
        if (desiredWidth == null) {
            desiredWidth = parseInt($element.attr('desiredWidth'));
            if (isNaN(desiredWidth))
                desiredWidth = $element.width();
            var cssWidth = parseInt($element.css('width'));
            if (cssWidth > desiredWidth)
                desiredWidth = cssWidth;
        }

        // We're only shrinking the text if necessary, not making it larger.
        var oldSize = oldSize = parseInt($element.css('font-size'));

        var maxSize = this.options.maxSize;

        var offset = this.options.shrinkOffset;
        if (offset == null)
            offset = $element.attr('shrinkOffset');
        if (offset)
            desiredWidth -= offset;

        if (measure.width() >= desiredWidth) {
            var size = 2;
            measure.css('font-size', size);

            while (size <= maxSize && measure.width() < desiredWidth) {
                size++;
                measure.css('font-size', size + 'px');
            }
            size--;

            if (isNaN(oldSize) || size < oldSize) {
                $element.css('font-size', size + 'px');
                //$element.css('line-height', $element.height() + 'px');
            }
        }

        measure.remove();
    }
});

$.plugin('shrinkText', ShrinkText);