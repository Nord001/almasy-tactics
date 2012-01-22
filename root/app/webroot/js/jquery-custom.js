jQuery.fn.exists = function(){ return jQuery(this).length > 0; }

jQuery.fn.applyCss = function(cssStr) {
    var properties = cssStr.split(';');

    for (var i = 0; i < properties.length; i++) {
        var chunks = properties[i].split(':');
        if (chunks.length != 2)
            continue;

        chunks[0] = $.trim(chunks[0]);
        chunks[1] = $.trim(chunks[1]);

        this.css(chunks[0], chunks[1]);
    }
    return this;
}

jQuery.extend({
    create: function(str) {
        return $(document.createElement(str));
    }
});
