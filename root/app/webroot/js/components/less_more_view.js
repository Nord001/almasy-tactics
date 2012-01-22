/**
 * Allows the user to show less or more, triggered by two link clicks.
 *
 * Options:
 *   name: Name of this element.
 *   lessLink: The link to click on to show the less view.
 *   moreLink: The ilnk to click on to show the more view.
 *   lessView: The view to show for the less link.
 *   moreView: The view to show for the more link.
 */

var LessMoreView = Component.extend({
    options: {
        lessLink: false,
        moreLink: false,
        lessView: false,
        moreVieW: false,
        name: false,
        cookieOptions: { path: '/', expires: 9999 }
    },
    init: function(options) {
        this.options = $.extend({
            lessLink: false,
            moreLink: false,
            lessView: false,
            moreView: false,
            name: false
        }, options);

        this.activeView = 'less';

        this.$lessLink = $(this.options.lessLink);
        this.$moreLink = $(this.options.moreLink);
        this.$lessView = $(this.options.lessView);
        this.$moreView = $(this.options.moreView);

        this.bindEvents();

        this._restoreFromCookie();
    },
    bindEvents: function() {
        var obj = this;
        this.$lessLink.click(function(event) {
            event.preventDefault();
            obj._activateLessView();
        });
        this.$moreLink.click(function(event) {
            event.preventDefault();
            obj._activateMoreView();
        });
    },
    activateView: function(name) {
        if (name == 'more')
            this._activateMoreView();
        else if (name == 'less')
            this._activateLessView();
    },
    activeView: function() {
        return this.activeView;
    },
    _activateLessView: function() {
        this.$moreView.hide();
        this.$lessView.show();
        this.$lessLink.css('fontWeight', 'bold');
        this.$moreLink.css('fontWeight', 'normal');

        this.activeView = 'less';
        this._setCookie();
    },
    _activateMoreView: function() {
        this.$lessView.hide();
        this.$moreView.show();
        this.$moreLink.css('fontWeight', 'bold');
        this.$lessLink.css('fontWeight', 'normal');

        this.activeView = 'more';
        this._setCookie();
    },
    _setCookie: function() {
        $.cookie(this.options.name, this.activeView, this.options.cookieOptions);
    },
    _restoreFromCookie: function() {
        var lessMoreCookie = $.cookie(this.options.name);
        var mode = 'less';
        if (lessMoreCookie != null) {
            mode = lessMoreCookie;
        }
        this.activateView(mode);
    }
});
