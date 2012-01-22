/**
 * A div that represents a potential target that the user can attack.
 *
 * Options:
 *     onClick: A function that is called when the target is clicked.
 */
var MatchmakingTarget = Component.extend({
    options: {
        onClick: false
    },
    init: function(options, element) {
        this.options = $.extend({
            onClick: false
        }, options);
        this.$element = $(element);
        this.bindEvents();
    },
    bindEvents: function() {
        var obj = this;
        this.$element.click(function() { obj._onClick() });
        this.$element.hover(
            function() { obj._onHoverOn(); },
            function() { obj._onHoverOff(); }
        );
    },
    _onClick: function() {
        //if (!confirm('Are you sure you want to attack this formation?'))
        //    return;

        if (this.options.onClick) {
            this.options.onClick(this.$element);
        }
    },
    _onHoverOn: function() {
        this.$element.attr('oldBg', this.$element.css('background-color'));
        this.$element.css('background-color', 'rgb(250, 200, 200)');
        this.$element.css('cursor', 'pointer');
    },
    _onHoverOff: function() {
        this.$element.css('cursor', 'auto');
        this.$element.css('background-color', this.$element.attr('oldBg'));
    }
});

$.plugin('matchmakingTarget', MatchmakingTarget);