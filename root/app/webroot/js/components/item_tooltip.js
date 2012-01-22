var ItemTooltip = (function() {

    var itemTooltipTemplate = " \
    <div class = 'ItemTooltipData'> \
        <div style = 'font-weight: bold; color: <%= color %>'> \
            <%= title %> \
        </div> \
        <% if (item.CharacterEquipped != undefined && item.CharacterEquipped.id != null) { %> \
            <div> \
                Equipped to <b><%= item.CharacterEquipped.name %></b> \
            </div> \
        <% } %> \
        <% if (item.Item.MiscItemType.id == null) { %> \
            <div> \
                Required Lv. <b><%= item.Item.req_lvl %></b> \
            </div> \
        <% } %> \
        <% if (item.Item.WeaponType.id != null) { %> \
            <% \
                var attack = item.Item.attack; \
                if (item.refine > 0) \
                    attack += ' + ' + item.refine_bonus; \
                var spanClass = item.refine > 0 ? 'item-mod' : ''; \
            %> \
            <div> \
                <span style = 'font-weight: bold' class = '<%= spanClass %>'> \
                    <%= attack %> \
                </span> Attack \
            </div> \
            <div> \
                <% var plural = item.Item.strikes > 1 ? 's' : ''; %> \
                Strikes <b><%= item.Item.strikes %></b> Time<%= plural %> \
            </div> \
            <% if (item.Item.critical != 0) { %> \
                <div> \
                    <b><%= ToSignedStr(item.Item.critical) %></b> Critical \
                </div> \
            <% } %> \
        <% } %> \
        <% if (item.Item.ArmorType.id != null) { %> \
            <% var spanClass = item.refine > 0 ? 'item-mod' : ''; %> \
            <div> \
                Defense: \
                <span style = 'font-weight: bold' class = '<%= spanClass %>'> \
                    <%= parseInt(item.Item.phys_reduction) + \
                        (item.refine > 0 ? parseInt(item.refine_bonus) : 0) %>% \
                        + <%= item.Item.phys_defense %> \
                </span> \
            </div> \
            <div> \
                Magic Defense: \
                <span style = 'font-weight: bold' class = '<%= spanClass %>'> \
                    <%= parseInt(item.Item.mag_reduction) + \
                        (item.refine > 0 ? parseInt(item.refine_bonus) : 0) %>% \
                        + <%= item.Item.mag_defense %> \
                </span> \
            </div> \
            <% if (item.Item.ArmorType.speed_mod != 0) { %> \
                <% var name = item.Item.ArmorType.speed_mod > 0 ? 'Boost' : 'Penalty'; %> \
                <div> \
                    Speed <%= name %>: <b> \
                        <%= ToSignedStr(item.Item.ArmorType.speed_mod) %> \
                        Speed</b> \
                </div> \
            <% } %> \
        <% } %> \
        <% for (var i = 0; i < mods.length; i++) { %> \
            <div style = 'color: <%= mods[i].color %>'> \
                <%= mods[i].text %> \
            </div> \
        <% } %> \
        <% if (item.Item.description !== '') { %> \
            <div style = 'font-style: italic; font-size: 80%; white-space: pre'> \
                Description: <%= item.Item.description %> \
            </div> \
        <% } %> \
    </div> \
    ";

    return Component.extend({
    init: function(options, element) {
        this.options = $.extend({
            item: null,
        }, options);

        this.$element = $(element);
        var tooltipElement = this.renderItemTooltip();
        this.tooltip = new Tooltip({ tooltipElement: tooltipElement }, element);
    },
    renderItemTooltip: function() {
        var item = this.options.item;

        var color = '';
        if (item.rarity === 'imbued')
            color = 'rgb(255, 217, 0)';
        else if (item.rarity == 'unique')
            color = 'rgb(200, 0, 240)';

        this.$element.css('color', color);

        var title = '';
        if (item.refine > 0)
            title += item.refine_prefix + ' ';
        title += item.name;
        title += ' (';
        if (item.Item.WeaponType.id != null)
            title += item.Item.WeaponType.name;
        else if (item.Item.ArmorType.id != null)
            title += item.Item.ArmorType.name + ' Armor';
        else
            title += item.Item.MiscItemType.name;
        title += ') ';

        if (item.quantity > 1)
            title += 'x ' + item.quantity;

        var mods = [];
        for (var i = 0; i < item.ItemMod.length; i++) {
            var mod = item.ItemMod[i];

            var modColor = mod.amount >= 0 ? 'rgb(75, 255, 75)' : 'rgb(225, 75, 75)';
            var amount = '';
            if (mod.amount !== '0.00') {
                if (parseInt(mod.amount) == parseFloat(mod.amount)) {
                    amount = parseInt(mod.amount);
                } else {
                    amount = parseFloat(mod.amount);
                }
            }

            var duration = mod.duration != null ?
                (' for ' + mod.duration + ' round' + (mod.duration == 1 ? '' : 's')) :
                '';

            var modName = mod.BonusType.name;
            if (modName[0] != '%')
                modName = ' ' + modName;

            mods.push({
                color: modColor,
                text: ToSignedStr(amount) + modName + duration
            });
        }

        var div = renderTemplate(
            itemTooltipTemplate,
            {
                title: title,
                color: color,
                item: item,
                mods: mods
            }
        );

        return div;
    }
});
})();

$.plugin('itemTooltip', ItemTooltip);