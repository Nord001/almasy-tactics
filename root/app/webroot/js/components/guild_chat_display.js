var GuildChatDisplay = Component.extend({
    init: function(options, element) {
        this.options = $.extend({
            loadingIcon: false
        }, options);

        this.$element = $(element);
        this.lastId = '';   // The last message id received.
        this.isUpdating = false;  // A lock on when things are being updated.
        this.messageTable = {};  // A set of received message ids.

        if (this.options.loadingIcon)
            this.$loadingIcon = $(this.options.loadingIcon);

        this._updateTick(true);
    },
    _updateTick: function(isRecurring) {
        // Do update.
        var obj = this;

        // If not recurring and an update is happening, just stop.
        if (!isRecurring && this.isUpdating)
            return;

        this.isUpdating = true;

        if (this.options.loadingIcon)
            this.$loadingIcon.show();

        $.post(
            '/guilds/get_new_messages',
            { lastId: obj.lastId },
            function (result) {
                if (obj.options.loadingIcon)
                    obj.$loadingIcon.hide();

                obj.isUpdating = false;

                if (result == AJAX_ERROR_CODE) {
                    alert('Error fetching chat.');
                    console.log(result);
                    return;
                }

                try {
                    result = JSON.parse(result);
                } catch (e) {
                    alert('Error fetching chat.');
                    console.log(result);
                    return;
                }
                obj._addMessages(result.messages);
                obj.$element.attr({ scrollTop: obj.$element.attr('scrollHeight') });

                if (isRecurring)
                    setTimeout(function() { obj._updateTick(true); }, 3000);
            }
        );
    },
    postMessage: function(message, guildId) {
        // Post message to server.
        var obj = this;
        $.post(
            '/guilds/post_message',
            { message: message, guildId: guildId },
            function (result) {
                console.log(result);
                if (result == AJAX_ERROR_CODE) {
                    alert('Error posting message.');
                    return;
                }
                obj._updateTick(false);
            }
        );
    },
    _addMessages: function(messages) {
        for (var i = 0; i < messages.length; i++)
            this._addMessage(messages[i]);
    },
    _addMessage: function(message) {
        if (this.messageTable[message.id])
            return;

        this.messageTable[message.id] = 1;

        $('<div>').text(message.username + ": " + message.content)
        .css({width: '100%', wordWrap: 'break-word'}).appendTo(this.$element);

        this.lastId = message.id;
    }
});

$.plugin('guildChatDisplay', GuildChatDisplay);