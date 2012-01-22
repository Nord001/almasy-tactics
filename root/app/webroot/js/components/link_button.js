/**
 * A button that takes the user to a link when clicked. The link is either derived
 * from the href attribute of this element or is passed in.
 *
 * Options:
 *     link: The link to send the user to when the button is clicked.
 */
var LinkButton = Component.extend({
    options: {
        link: false
    },
    init: function(options, element) {
        this.options = $.extend({
            link: false
        }, options);
        this.$element = $(element);
        this.bindEvents();
    },
    bindEvents: function() {
        var obj = this;
        this.$element.click(function() { obj._onClick() });
    },
    _onClick: function() {
        if (this.options.link) {
            window.location = this.options.link;
            return;
        }

        link = this.$element.attr('href');
        if (link) {
            window.location = link;
            return;
        }
    }
});

$.plugin('linkButton', LinkButton);