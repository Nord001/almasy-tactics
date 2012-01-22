var FormationWidget = (function() {
    var characterDivTemplate = " \
    <div class = 'CharacterDiv' \
        characterId = '<%= character.Character.id %>' \
        classId = '<%= character.CClass.id %>' \
        charLevel = '<%= character.Character.level %>'> \
        <div class = 'CharacterHeader'> \
            <img src = '<%= 'img/sprites/' + character.CClass.face_icon + '.png' %>' \
                style = 'border: 1px solid; vertical-align: middle; width: 15px; \
                         height: 15px; margin-right: 4px; vertical-align: middle;' /> \
            <%= character.Character.name %> \
        </div> \
        <span style = 'position: absolute; right: 2px; top: 2px'> \
            Lv. <%= character.Character.level %> \
        </span> \
        <table style = 'width: 100%'> \
            <tr> \
                <td style = 'width: 60%'> \
                    <img src = '/img/hp.png' class = 'FormationIcon' /> \
                    <%= parseInt(character.Character.Stats.maxHp) %> \
                </td> \
                <td> \
                    <img src = '/img/phys defense.png' class = 'FormationIcon' /> \
                    <%= pd %> \
                </td> \
            </tr> \
            <tr> \
                <td style = 'width: 60%'> \
                    <img src = '/img/atk.png' class = 'FormationIcon' /> \
                    <%= damageString %> \
                </td> \
                <td> \
                    <img src = '/img/dodge.png' class = 'FormationIcon' /> \
                    <%= md %> \
                </td> \
            </tr> \
        </table> \
        <span class = 'Equipment'> \
            <% if (character.Character.Weapon.id == null && character.Character.Armor.id == null) { %> \
                Nothing Equipped \
            <% } else { %> \
                <% var hasWeapon = (character.Character.Weapon.id != null); %> \
                <% if (hasWeapon) { %> \
                    <span style = 'font-weight: bold' class = 'Weapon'> \
                        <img src = '<%= 'img/sprites/' + character.Character.Weapon.Item.sprite + '.png' %>' \
                            style = 'margin-right: 2px; vertical-align: middle' /> \
                        <%= character.Character.Weapon.name %> \
                    </span> \
                <% } %> \
                <% if (character.Character.Armor.id != null) { %> \
                    <% if (hasWeapon) { %> | <% } %> \
                    <span style = 'font-weight: bold' class = 'Armor'> \
                        <img src = '<%= 'img/sprites/' + character.Character.Armor.Item.sprite + '.png' %>' \
                            style = 'margin-right: 2px; vertical-align: middle' /> \
                        <%= character.Character.Armor.name %> \
                    </span> \
                <% } %> \
            <% } %> \
        </span> \
    </div>";

    return Component.extend({
    //---------------------------------------------------------------------------------------------
    init: function(options, element) {
        this.options = $.extend({
            armoryWidget: null,
            classWeaponEquips: null
        }, options);

        this.$element = $(element);
        this.formationList = null;
        this.currentIndex = 0;
        this.armoryWidget = this.options.armoryWidget != null ?
            $(this.options.armoryWidget).data('armoryWidget') :
            null;

        var obj = this;
        this.armoryWidget.onItemSelected(function(item) {
            obj._highlightItemUsage(item);
        });

        this.update();
    },
    //---------------------------------------------------------------------------------------------
    update: function() {
        var obj = this;

        $.post(
            '/formations/get_formations',
            function (result) {
                if (result == AJAX_ERROR_CODE) {
                    alert('Error fetching formations.');
                    console.log(result);
                    return;
                }

                try {
                    result = JSON.parse(result);
                } catch (e) {
                    alert('Error fetching formations.');
                    console.log(result);
                    return;
                }

                obj.updateFormations(result);
            }
        );
    },
    //---------------------------------------------------------------------------------------------
    updateFormations: function (formations) {
        this.formationList = formations;
        this.render();
    },
    //---------------------------------------------------------------------------------------------
    render: function() {
        var obj = this;

        this.$element.empty();

        if (this.formationList.length == 0) {
            this.$element.append('No formations!');
            return;
        }

        if (this.currentIndex >= this.formationList.length)
            this.currentIndex = 0;

        var formation = this.formationList[this.currentIndex];

        var div = $('<div>');

        var headerDiv = $("<div style = 'font-size: 130%;'>").appendTo(div);

        var header = $("<div id = 'FormationHeader'>").append(
            $('<a>').text(formation.Formation.name)
                .attr('href', '/formations/view/' + formation.Formation.id)
        ).appendTo(headerDiv);

        var style = 'position: absolute; top: 0px;';
        var leftLink = $("<a>").applyCss(style)
            .css('left', '5px').attr('href', '#').text('<<').appendTo(headerDiv);
        leftLink.click(function(event) { event.preventDefault(); obj.switchLeft(); });

        var rightLink = $("<a>").applyCss(style)
            .css('right', '5px').attr('href', '#').text('>>').appendTo(headerDiv);
        rightLink.click(function(event) { event.preventDefault(); obj.switchRight(); });

        var table = $('<table style = "width: 100%">');
        for (var i = 0; i < formation.Characters.length; i++) {
            var tr = $('<tr>');
            for (var j = 0; j < 2; j++) {
                var character = formation.Characters[i * 2 + j];
                if (character == undefined)
                    break;

                var td = $('<td style = "width: 50%">');

                var damageString = '';
                if (character.Character.Stats.meleeAtk != character.Character.Stats.rangedAtk)
                    damageString = parseInt(character.Character.Stats.meleeAtk) + '/' + parseInt(character.Character.Stats.rangedAtk);
                else
                    damageString = parseInt(character.Character.Stats.meleeAtk);

                damageString += 'x' + character.Character.Stats.numStrikes;

                var pd = parseInt(character.Character.Stats.physReduction) + '% ' + ToSignedStr(parseInt(character.Character.Stats.physDefense));
                var md = parseInt(character.Character.Stats.magReduction) + '% ' + ToSignedStr(parseInt(character.Character.Stats.magDefense));

                var charDiv = renderTemplate(
                    characterDivTemplate,
                    {
                        character: character,
                        damageString: damageString,
                        pd: pd,
                        md: md
                    }
                );
                td.append(charDiv);
                td.click(function(event) {
                    event.preventDefault();
                    obj._onClick(this);
                });

                var weaponSpan = td.find('.Weapon');
                if (weaponSpan.exists()) {
                    new ItemTooltip({ item: character.Character.Weapon }, weaponSpan);
                    weaponSpan.click(function(item) {
                        return function() {
                            obj._unequip(item.id);
                        }
                    }(character.Character.Weapon));
                    weaponSpan.hoverPointer();
                }

                var armorSpan = td.find('.Armor');
                if (armorSpan.exists()) {
                    new ItemTooltip({ item: character.Character.Armor }, armorSpan);
                    armorSpan.click(function(item) {
                        return function() {
                            obj._unequip(item.id);
                        }
                    }(character.Character.Armor));
                    armorSpan.hoverPointer();
                }

                var itemSpan = td.find('.Equipment');
                itemSpan.shrinkText({ maxSize: 15, width: 240 });

                /* Removed because the highlighting takes care of this.
                var weaponUsage = $('<span>').applyCss('position: absolute; right: 50px; top: 5px');
                for (var k = 0; k < character.CClass.WeaponType.length; k++) {
                    weaponUsage.append(
                        $('<img>').attr('src', '/img/sprites/' + character.CClass.WeaponType[k].sprite + '.png')
                        .applyCss('margin-right: 2px; vertical-align: middle; width: 18px; height: 18px;')
                    );
                }
                */

                td.appendTo(tr);
            }
            tr.appendTo(table);
        }

        table.appendTo(div);

        div.appendTo(this.$element);
    },
    //---------------------------------------------------------------------------------------------
    switchLeft: function() {
        this.currentIndex--;
        if (this.currentIndex == -1)
            this.currentIndex += this.formationList.length;
        this.render();
    },
    //---------------------------------------------------------------------------------------------
    switchRight: function() {
        this.currentIndex = (this.currentIndex + 1) % this.formationList.length;
        this.render();
    },
    //---------------------------------------------------------------------------------------------
    _unequip: function(userItemId) {
        var obj = this;
        ShowLoadAnim();
        $.post(
            '/items/unequip_item',
            {
                userItemId: userItemId
            },
            function (data) {
                HideLoadAnim();
                if (data == AJAX_ERROR_CODE) {
                    alert('Sorry, an error has occurred.');
                    return;
                }

                obj.update();
                if (obj.armoryWidget)
                    obj.armoryWidget.update();
            }
        );
    },
    //---------------------------------------------------------------------------------------------
    _canEquip: function(characterDiv, item) {
        if (item.Item.MiscItemType.id != null)
            return false;

        if (item.Item.WeaponType.id == null || this.options.classWeaponEquips == null)
            return true;

        var reqLvl = parseInt(item.Item.req_lvl);
        var classId = $(characterDiv).attr('classId');
        var charLevel = parseInt($(characterDiv).attr('charLevel'));

        if (charLevel < reqLvl)
            return false;

        var classData = this.options.classWeaponEquips[classId];
        if (classData != null) {
            for (var i in classData) {
                if (classData[i] == item.Item.WeaponType.id)
                    return true;
            }
        }
        return false;
    },
    //---------------------------------------------------------------------------------------------
    _highlightItemUsage: function(item) {
        var obj = this;

        var nodes = this.$element.find('.CharacterDiv');
        nodes.removeClass('CanEquip').removeClass('CantEquip');

        if (item == null)
            return;

        if (item.Item.ArmorType.id != null) {
            nodes.addClass('CanEquip');
            return;
        }

        var canEquip = $.map(nodes, function(div) {
            if (obj._canEquip(div, item))
                return div;
            return null;
        });

        canEquip = $(canEquip);

        nodes.addClass('CantEquip');
        canEquip.removeClass('CantEquip').addClass('CanEquip');
    },
    //---------------------------------------------------------------------------------------------
    _onClick: function(div) {
        div = $(div).children('div');
        if (this.armoryWidget == null)
            return;

        var selectedItem = this.armoryWidget.selectedItem;
        if (selectedItem == null)
            return;

        if (!this._canEquip(div, selectedItem)) {
            alert('That character cannot equip that item.');
            return;
        }

        ShowLoadAnim();

        var obj = this;
        $.post(
            '/items/equip_item',
            {
                userItemId: selectedItem.id,
                characterId: $(div).attr('characterId')
            },
            function (data) {
                HideLoadAnim();
                if (data == AJAX_ERROR_CODE) {
                    alert('Sorry, an error has occurred.');
                    return;
                }
                obj.armoryWidget.update();
                obj.update();
            }
        );
    }
});

})();

$.plugin('formationWidget', FormationWidget);