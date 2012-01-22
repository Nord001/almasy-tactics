var ArmoryWidget = Component.extend({
    //---------------------------------------------------------------------------------------------
    init: function(options, element) {
        this.options = $.extend({
            weaponSprites: null,
            armorSprites: null,
            miscItemSprites: null,
        }, options);

        this.$element = $(element);
        this.itemTable = null;
        this.currentIndex = 0;
        this.selectedItem = null;
        this.selectedItemDiv = null;
        this.onItemSelectedCallback = [];

        this.update();
    },
    //---------------------------------------------------------------------------------------------
    update: function() {
        var obj = this;

        $.post(
            '/items/get_items',
            function (result) {
                if (result == AJAX_ERROR_CODE) {
                    alert('Error fetching items.');
                    console.log(result);
                    return;
                }

                try {
                    result = JSON.parse(result);
                } catch (e) {
                    alert('Error fetching items.');
                    console.log(result);
                    return;
                }

                obj.updateItems(result);
            }
        );
    },
    //---------------------------------------------------------------------------------------------
    _itemSortFunc: function (a, b) {
        if (a.sortOrder != b.sortOrder)
            return a.sortOrder - b.sortOrder;
        else
            return a.category.localeCompare(b.category);
    },
    //---------------------------------------------------------------------------------------------
    updateItems: function (items) {

        var table = {};
        var itemTable = [];

        var translation = {
            'Knifes': 'Knives'
        }

        for (var i = 0; i < items.length; i++) {
            var item = items[i].UserItem;

            // Determine type and sort order.
            var type = "Items";
            var sortOrder = 0;
            var sprite = "";
            if (item.Item.WeaponType.name != null) {
                type = item.Item.WeaponType.name + "s";
                sortOrder = 0;
                sprite = this.options.weaponSprites != null ? this.options.weaponSprites[item.Item.WeaponType.id] : "";
            } else if (item.Item.ArmorType.name != null) {
                var armorType = item.Item.ArmorType.name;
                type = armorType + " Armor";
                switch (armorType) {
                    case "Light": sortOrder = 1; break;
                    case "Medium": sortOrder = 2; break;
                    case "Heavy": sortOrder = 3; break;
                }
                sprite = this.options.armorSprites != null ? this.options.armorSprites[item.Item.ArmorType.id] : "";
            } else if (item.Item.MiscItemType.name != null) {
                type = item.Item.MiscItemType.name + "s";
                sortOrder = 5;
                sprite = this.options.miscItemSprites != null ? this.options.miscItemSprites[item.Item.MiscItemType.id] : "";
            }

            if (translation[type] != null)
                type = translation[type];

            if (table[type] != null) {
                table[type].items.push(item);
            } else {
                var typeCategory = { category: type, sortOrder: sortOrder, sprite: sprite, items: [item] };
                table[type] = typeCategory;
            }
        }

        for (var type in table)
            itemTable.push(table[type]);

        var obj = this;
        itemTable.sort(function(a, b) { return obj._itemSortFunc(a, b); });

        this.itemTable = itemTable;
        this.renderCache = {};
        this.render();
    },
    //---------------------------------------------------------------------------------------------
    render: function() {
        var obj = this;

        this.selectedItem = null;
        this.selectedItemDiv = null;
        this._activateOnItemSelected();

        this.$element.empty();

        if (this.itemTable.length == 0) {
            this.$element.append('No items!');
            return;
        }

        if (this.currentIndex >= this.itemTable.length)
            this.currentIndex = 0;

        var div = $('<div>');

        var select = $('<select>').applyCss('width: auto; position: absolute; top: -30px; right: 0px;');
        for (var i = 0; i < this.itemTable.length; i++) {
            var category = this.itemTable[i];
            var selected = (this.currentIndex == i) ? 'selected' : '';
            select.append(
                $('<option>').append(category.category).attr('selected', selected)
            );
        }
        select.appendTo($('<div>').applyCss('text-align: right').appendTo(div));
        select.change(function() {
            obj._onSelect(select.val());
        });

        var category = this.itemTable[this.currentIndex];

        var headerDiv = $("<div style = 'font-size: 130%; position: relative;'>").appendTo(div);

        var header = $("<div id = 'ArmoryHeader'>")
            .append(
                $('<img>').attr('src', '/img/sprites/' + category.sprite + '.png')
                .applyCss('vertical-align: middle; margin-right: 2px; margin-top: -5px;')
            ).append(category.category + ' (' + category.items.length + ')')
            .appendTo(headerDiv);

        var style = 'position: absolute; top: 0px;';
        var leftLink = $("<a>").applyCss(style)
            .css('left', '5px').attr('href', '#').text('<<').appendTo(headerDiv);
        leftLink.click(function(event) { event.preventDefault(); obj.switchLeft(); });

        var rightLink = $("<a>").applyCss(style)
            .css('right', '5px').attr('href', '#').text('>>').appendTo(headerDiv);
        rightLink.click(function(event) { event.preventDefault(); obj.switchRight(); });

        var table = $('<table style = "width: 100%">');
        for (var i = 0; i < category.items.length; i++) {
            var tr = $('<tr>');
            for (var j = 0; j < 2; j++) {
                var item = category.items[i * 2 + j];
                if (item == undefined)
                    break;

                var td = $('<td style = "width: 50%">');

                var itemDiv = $('<div style = "position: relative; height: 27px;">');

                $('<img>').attr('src', '/img/sprites/' + item.Item.sprite + '.png')
                  .css('vertical-align', 'middle')
                  .appendTo(itemDiv);

                var name = item.name;
                if (item.quantity > 1)
                    name += ' x ' + item.quantity;

                $('<span>').css({
                    fontWeight: 'bold',
                    position: 'absolute',
                    left: '30px'
                }).text(name).appendTo(itemDiv);

                var tooltip = new ItemTooltip({ item: item }, itemDiv);

                itemDiv.click(function(item) {
                    return function(event) {
                        event.preventDefault();
                        if (obj.selectedItemDiv)
                            obj.selectedItemDiv.removeClass('Selected');
                        $(this).addClass('Selected');
                        obj.selectedItemDiv = $(this);
                        obj.selectedItem = item;
                        obj._activateOnItemSelected();
                    };
                }(item));

                itemDiv.hoverPointer();

                itemDiv.appendTo(td);
                td.appendTo(tr);
            }
            tr.appendTo(table);
        }

        table.appendTo(div);
        div.appendTo(this.$element);

        //console.profileEnd();
        //console.timeEnd('render');
    },
    //---------------------------------------------------------------------------------------------
    switchLeft: function() {
        this.currentIndex--;
        if (this.currentIndex == -1)
            this.currentIndex += this.itemTable.length;
        this.render();
    },
    //---------------------------------------------------------------------------------------------
    switchRight: function() {
        this.currentIndex = (this.currentIndex + 1) % this.itemTable.length;
        this.render();
    },
    //---------------------------------------------------------------------------------------------
    onItemSelected: function(callback) {
        this.onItemSelectedCallback.push(callback);
    },
    //---------------------------------------------------------------------------------------------
    _onSelect: function(name) {
        for (var i = 0; i < this.itemTable.length; i++) {
            if (this.itemTable[i].category == name) {
                this.currentIndex = i;
                break;
            }
        }
        this.render();
    },
    //---------------------------------------------------------------------------------------------
    _activateOnItemSelected: function() {
        var obj = this;
        $.each(this.onItemSelectedCallback, function(i, callback) {
            callback(obj.selectedItem);
        });
    }
});

$.plugin('armoryWidget', ArmoryWidget);