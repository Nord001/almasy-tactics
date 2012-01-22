var cellHeight = 140;
var c_cellWidthSpacing = 135;
var c_cellHeightSpacing = 150;

var ctx;

//=================================================================================================
// Fireball
//=================================================================================================

//--------------------------------------------------------------------------------------------------
function Fireball () {
    this.x = 0;
    this.y = 0;
    this.vx = 0;
    this.vy = 0;
    this.trail = [];

    Fireball.prototype.SetPosition = FireballSetPosition;
    Fireball.prototype.SetVelocity = FireballSetVelocity;
    Fireball.prototype.Display = FireballDisplay;
    Fireball.prototype.Update = FireballUpdate;
}

//--------------------------------------------------------------------------------------------------
function FireballSetPosition (x, y) {
    this.x = x;
    this.y = y;
}

//--------------------------------------------------------------------------------------------------
function FireballSetVelocity (x, y) {
    this.vx = x;
    this.vy = y;
}

//--------------------------------------------------------------------------------------------------
function FireballUpdate (dt) {
    this.x += this.vx * dt;
    this.y += this.vy * dt;

    this.trail.push({x: this.x, y: this.y});

    if (this.trail.length > 8)
        this.trail.shift();

}

var c_fireballRadius = 30;
var c_fireballTrailWidth = 30.0;

//--------------------------------------------------------------------------------------------------
function FireballDisplay () {
    // Trail
    var gradient = ctx.createLinearGradient(this.x, this.y, this.x - this.vx, this.y - this.vy);
    gradient.addColorStop(0, 'rgb(200, 40, 40)');
    gradient.addColorStop(1, 'rgb(200, 150, 40)');

    ctx.save();
    ctx.strokeStyle = gradient;
    ctx.lineWidth = c_fireballTrailWidth;
    ctx.lineJoin = 'round';
    ctx.lineCap = 'round';
    ctx.shadowBlur = 5.0;
    ctx.shadowColor = 'hsl(0, 60%, 40%)';
    ctx.beginPath();
    ctx.moveTo(this.x, this.y);
    for (var i = this.trail.length - 2; i >= 1; i -= 2) {
        ctx.quadraticCurveTo(this.trail[i + 1].x, this.trail[i + 1].y, this.trail[i].x, this.trail[i].y);
    }
    ctx.stroke();
    ctx.restore();

    // Fireball
    ctx.save();
    ctx.translate(this.x, this.y);
    var gradient = ctx.createRadialGradient(0, 0, .1, 0, 0, c_fireballRadius);
    gradient.addColorStop(0, '#FFFB00');
    gradient.addColorStop(0.35, '#DCCB00');
    gradient.addColorStop(0.6, '#C72401');
    gradient.addColorStop(0.8, '#C12015');
    gradient.addColorStop(1.0, 'rgba(150, 50, 50, 0)');
    ctx.fillStyle = gradient;
    DrawCircle(c_fireballRadius, true, false);
    ctx.restore();
}

//=================================================================================================
// Formation
//=================================================================================================

//--------------------------------------------------------------------------------------------------
function Formation () {
    this.characters = [];
    this.x = 0;
    this.y = 0;
    this.flipped = false;

    Formation.prototype.SetPosition = FormationSetPosition;
    Formation.prototype.LoadCharacters = FormationLoadCharacters;
    Formation.prototype.Display = FormationDisplay;
    Formation.prototype.GetCharacterById = FormationGetCharacterById;
}

//--------------------------------------------------------------------------------------------------
function FormationSetPosition (x, y) {
    this.x = x;
    this.y = y;
}

//--------------------------------------------------------------------------------------------------
function FormationLoadCharacters (info) {
    for (var x = 0; x < c_formationWidth; x++) {
        for (var y = 0; y < c_formationHeight; y++) {
            var pos = x + y * c_formationWidth;

            var xCoord;
            if (this.flipped)
                xCoord = (c_formationHeight - y - 1) * c_cellWidthSpacing;
            else
                xCoord = y * c_cellWidthSpacing;

            var yCoord = x * c_cellHeightSpacing;

            var char = info[pos];

            if (char == null)
                continue;

            var c = new Character();

            c.SetPosition(xCoord, yCoord);
            c.id = char.id;
            c.name = char.name;
            c.icon = char.icon;
            c.maxHp = 15;
            c.formation = this;

            this.characters.push(c);
        }
    }
}

//--------------------------------------------------------------------------------------------------
function FormationDisplay () {
    ctx.save();
    ctx.translate(this.x, this.y);
    for (var index in this.characters) {
        this.characters[index].Display();
    }
    ctx.restore();
}

//--------------------------------------------------------------------------------------------------
function FormationGetCharacterById (id) {
    for (var index in this.characters) {
        var character = this.characters[index];
        if (character.id == id)
            return character;
    }
    return null;
}

//=================================================================================================
// Character
//=================================================================================================

//--------------------------------------------------------------------------------------------------
function Character () {
    this.x = 0;
    this.y = 0;
    this.id = 0;
    this.hp = 0;
    this.maxHp = 0;
    this.icon = '';
    this.formation = null;
    this.name = '';

    Character.prototype.SetPosition = CharacterSetPosition;
    Character.prototype.Display = CharacterDisplay;
    Character.prototype.Attack = CharacterAttack;
}

//--------------------------------------------------------------------------------------------------
function CharacterSetPosition (x, y) {
    this.x = x;
    this.y = y;
}

var c_characterPanelWidth = 125;
var c_characterPanelHeight = 140;
var c_iconSize = 100;

//--------------------------------------------------------------------------------------------------
function CharacterDisplay () {
    var length;

    ctx.save();
    ctx.translate(this.x, this.y);

    // Hp
    var percent = this.hp / this.maxHp;
    if (isNaN(percent))
        percent = 0;

    var height = c_characterPanelHeight * percent;
    ctx.save();
    if (percent > .6) {
        ctx.fillStyle = 'rgb(140, 240, 140)';
    } else if (percent > .3) {
        ctx.fillStyle = 'rgb(240, 240, 130)';
    } else if (percent > 0) {
        ctx.fillStyle = 'rgb(240, 140, 140)';
    }
    ctx.translate(0, c_characterPanelHeight - height);
    ctx.fillRect(0, 0, c_characterPanelWidth, height);
    ctx.strokeRect(0, 0, c_characterPanelWidth, height);
    ctx.restore();

    // Border
    ctx.strokeRect(0, 0, c_characterPanelWidth, c_characterPanelHeight);

    // Icon
    ctx.save();
    var iconStr = this.icon;
    if (iconStr == '')
        iconStr = 'face';

    var icon = document.getElementById(iconStr);
    ctx.translate(parseInt(c_characterPanelWidth / 2 - c_iconSize / 2), 5);
    ctx.strokeRect(0, 0, c_iconSize, c_iconSize);
    ctx.drawImage(icon, 0, 0);
    ctx.restore();

    ctx.font = 'bold 14px tahoma';
    length = ctx.measureText(this.name).width;
    ctx.fillText(this.name, c_characterPanelWidth / 2 - length / 2, 120);

    var hpString = parseInt(this.hp) + ' / ' + parseInt(this.maxHp);
    length = ctx.measureText(hpString).width;
    ctx.fillText(hpString, c_characterPanelWidth / 2 - length / 2, 135);

    ctx.restore();
}

//--------------------------------------------------------------------------------------------------
function CharacterAttack (targetChar) {
    var posX = this.x + this.formation.x;
    var posY = this.y + this.formation.y;

    var dirX = targetChar.x - posX;
    var dirY = targetChar.y - posY;

    var norm = Math.sqrt(dirX * dirX + dirY * dirY);

    dirX /= norm;
    dirY /= norm;

    dirX *= 200;
    dirY *= 200;

    for (var i = 0; i < 5; i++) {
        var fireball = new Fireball();
        fireball.SetPosition(posX, posY);
        fireball.SetVelocity(-dirX + (Math.random() - 0.5) * 50, -dirY + (Math.random() - 0.5) * 50);
        fireballs.push(fireball);
    }
}

//=================================================================================================
// Primitives
//=================================================================================================

//--------------------------------------------------------------------------------------------------
function DrawCircle (radius, filled, stroked) {
    if (filled == null)
        filled = false;
    if (stroked == null)
        stroked = true;

    ctx.beginPath();
    ctx.moveTo(0, -radius);
    ctx.arc(0, 0, radius, 0, Math.PI * 2, true);
    if (filled)
        ctx.fill();
    if (stroked)
        ctx.stroke();
}

var radius = 10;

//--------------------------------------------------------------------------------------------------
function DrawRoundedRect (width, height, filled, stroked) {
    if (filled == null)
        filled = false;
    if (stroked == null)
        stroked = true;

    ctx.beginPath();
    ctx.moveTo(radius, 0);
    ctx.lineTo(width - radius, 0);
    ctx.quadraticCurveTo(width, 0, width, radius);
    ctx.lineTo(width, height - radius);
    ctx.quadraticCurveTo(width, height, width - radius, height);
    ctx.lineTo(radius, height);
    ctx.quadraticCurveTo(0, height, 0, height - radius);
    ctx.lineTo(0, radius);
    ctx.quadraticCurveTo(0, 0, radius, 0);
    if (filled)
        ctx.fill();
    if (stroked)
        ctx.stroke();
}