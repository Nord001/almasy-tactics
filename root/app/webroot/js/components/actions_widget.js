var ActionsWidget = Component.extend({
    //---------------------------------------------------------------------------------------------
    init: function(options, element) {
        this.options = $.extend({
            armoryWidget: null,
            weaponImbues: null,
            armorImbues: null,
            refineChances: null
        }, options);

        this.$element = $(element);
        this.armoryWidget = $(this.options.armoryWidget).data('armoryWidget');
        this.selectedItem = null;
        this._onItemSelected(null);

        if (this.armoryWidget != null) {
            var obj = this;
            this.armoryWidget.onItemSelected(function(item) {
                obj._onItemSelected(item);
            });
        }
    },
    //---------------------------------------------------------------------------------------------
    _onItemSelected: function(item) {
        this.selectedItem = item;

        var obj = this;

        this.$element.empty();

        if (item == null) {
            this.$element.text('Click on an item to see what you can do with it.');
            return;
        }

        var actions = [];

        actions.push(
            $("<a>").attr('href', '#')
                .append('Sell (+' + AddCommas(this.selectedItem.Item.sell_price) + ' yb)')
                .click(function(event) {
                    event.preventDefault();
                    obj._sellItem();
                })
        );

        if (item.Item.WeaponType.id != null || item.Item.ArmorType.id != null) {
            if (item.rarity != 'unique') {
                var imbueCost = <?= IMBUE_COST_MULTIPLIER; ?> * this.selectedItem.Item.value + <?= IMBUE_COST_START; ?>;

                var canImbue = imbueCost <= GetMoney();
                var link = $("<a>").attr('href', '#')
                    .append('Imbue (' + AddCommas(imbueCost) + ' yb)')
                    .click(function(event) {
                        event.preventDefault();
                        if (canImbue)
                            obj._imbueItem();
                    });
                if (!canImbue)
                    link.addClass('Disabled');

                actions.push(link);
            }

            var refine = parseInt(item.refine);
            if (refine != <?= MAX_REFINE; ?>) {
                var refineCost = Math.floor((refine + 1) / 4 * item.Item.value);
                actions.push(
                    $("<a>").attr('href', '#').append('Refine (' + AddCommas(refineCost) + ' yb)').click(function(event) {
                        event.preventDefault();
                        obj._refineItem();
                    })
                );
            }
        }

        for (var i = 0; i < actions.length; i++) {
            this.$element.append(actions[i]);
            if (i != actions.length - 1)
                this.$element.append(' | ');
        }
    },
    //---------------------------------------------------------------------------------------------
    _sellItem: function() {
        if (this.selectedItem == null)
            return;

        var itemName = this.selectedItem.name;
        var qty = 1;

        if (this.selectedItem.Item.weapon_type_id != null || this.selectedItem.Item.armor_type_id != null) {
            if (!confirm('Do you want to sell this ' + itemName + '?'))
                return;
        } else {
            qty = prompt('How many to sell?', this.selectedItem.quantity);
            if (qty == null)
                return;

            qty = parseInt(qty);
            if (isNaN(qty) || qty <= 0) {
                alert('Invalid quantity.');
                return;
            }
            if (qty > this.selectedItem.quantity) {
                alert('You don\'t have that amount.');
                return;
            }
        }

        var userItemId = this.selectedItem.id;

        var dialog = $('<div>').attr('title', 'Please wait...').text('Processing...').appendTo($('body'));
        dialog.dialog(
            {
                modal: true,
                draggable: false,
            }
        );

        var obj = this;

        $.post(
            '/items/sell_item',
            {
                userItemId: userItemId,
                quantity: qty
            },
            function(data) {
                if (data == AJAX_ERROR_CODE) {
                    alert('An error has occurred.');
                    return;
                }

                dialog.dialog('option', 'title', 'Success!');
                dialog.text('You\'ve sold the item(s).');
                dialog.append("<input type = 'button' value = 'Okay' style = 'width: 100%; height: 75px' class = 'ConfirmButton' />");

                $('.ConfirmButton').click(function() {
                    dialog.remove();
                });
                $('.ConfirmButton').focus();

                UpdateMoneyDisplay();

                if (obj.armoryWidget != null)
                    obj.armoryWidget.update();
            }
        );
    },
    //---------------------------------------------------------------------------------------------
    _imbueItem: function() {
        if (this.selectedItem == null)
            return;

        var dialog = $('<div>').attr('title', 'Imbue').appendTo($('body'));
        dialog.dialog(
            {
                modal: true,
                draggable: false,
                width: '350px',
            }
        );

        var content = $("<div style = 'position: relative'>").appendTo(dialog);

        var obj = this;

        $('<div>').applyCss('position: absolute; left: 0px; top: 25px; width: 100px').append(
            $('<div>').applyCss('text-align: center').append(
                $('<img>').attr('src', '/img/sprites/' + this.selectedItem.Item.sprite + '.png')
                  .css('vertical-align', 'middle')
            )
        ).append(
            $('<div>').applyCss('text-align: center').append(this.selectedItem.name)
        ).appendTo(content);

        var select = $('<select>').applyCss('margin-bottom: 5px');
        if (this.selectedItem.Item.WeaponType.id != null) {
            if (this.options.weaponImbues != null) {
                for (var i = 0; i < this.options.weaponImbues.length; i++) {
                    var imbue = this.options.weaponImbues[i];
                    select.append($('<option>').attr('value', imbue.id).text(imbue.name));
                }
            }
        } else if (this.selectedItem.Item.ArmorType.id != null) {
            if (this.options.armorImbues != null) {
                for (var i = 0; i < this.options.armorImbues.length; i++) {
                    var imbue = this.options.armorImbues[i];
                    select.append($('<option>').attr('value', imbue.id).text(imbue.name));
                }
            }
        }

        $('<div>').applyCss('position: absolute; left: 110px; top: 10px').append(select).append(
            $('<input>').attr('value', 'Imbue!').applyCss('width: 80px; height: 30px;').attr('type', 'button')
            .click(function(event) {
                event.preventDefault();
                obj._onImbueClick(select.val());
                dialog.dialog('close');
            })
        ).append(
            $('<input>').attr('value', 'Cancel').applyCss('width: 80px; height: 30px; margin-left: 5px;').attr('type', 'button')
            .click(function(event) {
                event.preventDefault();
                dialog.dialog('close');
            })
        ).appendTo(content);
    },
    //---------------------------------------------------------------------------------------------
    _onImbueClick: function(imbueId) {
        var dialog = $('<div>').attr('title', 'Please wait...').text('Processing...').appendTo($('body'));
        dialog.dialog(
            {
                modal: true,
                draggable: false,
            }
        );

        var obj = this;

        $.post(
            '/items/perform_imbue',
            {
                userItemId: this.selectedItem.id,
                imbueId: imbueId
            },
            function(data) {
                if (data == AJAX_ERROR_CODE) {
                    dialog.dialog('option', 'title', 'Error...');
                    dialog.text('An error has occurred. Sorry!');
                    dialog.append("<input type = 'button' value = 'Okay..' style = 'width: 100%; height: 75px' class = 'ConfirmButton' />");

                    $('.ConfirmButton').click(function() {
                        dialog.dialog('close');
                    });
                    $('.ConfirmButton').focus();
                    return;
                }

                if (data == AJAX_INSUFFICIENT_FUNDS) {
                    dialog.dialog('option', 'title', 'Oops...');
                    dialog.text('You don\'t have enough money.');
                    dialog.append("<input type = 'button' value = 'Okay..' style = 'width: 100%; height: 75px' class = 'ConfirmButton' />");

                    $('.ConfirmButton').click(function() {
                        dialog.dialog('close');
                    });
                    $('.ConfirmButton').focus();
                    return;
                }

                dialog.dialog('option', 'title', 'Success!');
                dialog.text('The item has been imbued!');
                dialog.append("<input type = 'button' value = 'Awesome!' style = 'width: 100%; height: 75px' class = 'ConfirmButton' />");


                $('.ConfirmButton').click(function() {
                    dialog.dialog('close');
                });
                $('.ConfirmButton').focus();

                UpdateMoneyDisplay();
                if (obj.armoryWidget != null)
                    obj.armoryWidget.update();
            }
        );
    },
    //---------------------------------------------------------------------------------------------
    _refineItem: function() {
        if (this.selectedItem == null)
            return;

        var dialog = $('<div>').attr('title', 'Refine').appendTo($('body'));
        dialog.dialog(
            {
                modal: true,
                draggable: false,
                width: '350px',
            }
        );

        var content = $("<div style = 'position: relative'>").appendTo(dialog);

        var obj = this;

        $('<div>').applyCss('position: absolute; left: 0px; top: 25px; width: 100px').append(
            $('<div>').applyCss('text-align: center').append(
                $('<img>').attr('src', '/img/sprites/' + this.selectedItem.Item.sprite + '.png')
                  .css('vertical-align', 'middle')
            )
        ).append(
            $('<div>').applyCss('text-align: center').append(this.selectedItem.name)
        ).appendTo(content);

        var refineChance = this.options.refineChances != null ?
            this.options.refineChances[parseInt(this.selectedItem.refine) + 1] : null;
        if (refineChance == null)
            refineChance = 0;

        $('<div>').applyCss('position: absolute; left: 110px; top: 10px').append(
            $('<div>').applyCss('text-align: center').append('Refine Chance: ' + refineChance + '%')
        ).append(
            $('<input>').attr('value', 'Refine!').applyCss('width: 80px; height: 30px;').attr('type', 'button')
            .click(function(event) {
                event.preventDefault();
                obj._onRefineClick();
                dialog.dialog('close');
            })
        ).append(
            $('<input>').attr('value', 'Cancel').applyCss('width: 80px; height: 30px; margin-left: 5px;').attr('type', 'button')
            .click(function(event) {
                event.preventDefault();
                dialog.dialog('close');
            })
        ).appendTo(content);
    },
    //---------------------------------------------------------------------------------------------
    _onRefineClick: function() {
        var dialog = $('<div>').attr('title', 'Please wait...').text('Processing...').appendTo($('body'));
        dialog.dialog(
            {
                modal: true,
                draggable: false,
            }
        );

        var obj = this;

        $.post(
            '/items/perform_refine',
            {
                userItemId: this.selectedItem.id,
            },
            function(data) {
                if (data == AJAX_ERROR_CODE) {
                    dialog.dialog('option', 'title', 'Error...');
                    dialog.text('An error has occurred. Sorry!');
                    dialog.append("<input type = 'button' value = 'Okay..' style = 'width: 100%; height: 75px' class = 'ConfirmButton' />");

                    $('.ConfirmButton').click(function() {
                        dialog.dialog('close');
                    });
                    $('.ConfirmButton').focus();
                    return;
                }

                if (data == AJAX_INSUFFICIENT_FUNDS) {
                    dialog.dialog('option', 'title', 'Oops...');
                    dialog.text('You don\'t have enough money.');
                    dialog.append("<input type = 'button' value = 'Okay..' style = 'width: 100%; height: 75px' class = 'ConfirmButton' />");

                    $('.ConfirmButton').click(function() {
                        dialog.dialog('close');
                    });
                    $('.ConfirmButton').focus();
                    return;
                }

                var success = data[0];
                data = data.substr(1);
                if (success == 1) {
                    dialog.dialog('option', 'title', 'Success!');
                    dialog.text('The item has been refined!');
                    dialog.append("<input type = 'button' value = 'Awesome!' style = 'width: 100%; height: 75px' class = 'ConfirmButton' />");
                } else if (success == 0) {
                    dialog.dialog('option', 'title', 'Failure.');
                    dialog.text('The item failed to refine and was broken..');
                    dialog.append("<input type = 'button' value = 'Awww.' style = 'width: 100%; height: 75px' class = 'ConfirmButton' />");
                }


                $('.ConfirmButton').click(function() {
                    dialog.dialog('close');
                });
                $('.ConfirmButton').focus();

                UpdateMoneyDisplay();
                if (obj.armoryWidget != null)
                    obj.armoryWidget.update();
            }
        );
    },
});

$.plugin('actionsWidget', ActionsWidget);