<?

require_once 'server_constants.php';

//==============================================================================================
// Game Constants
//==============================================================================================

//---------------------------------------------------------------------------------------------
// Forum
//---------------------------------------------------------------------------------------------
define('FORUM_HANDLER', '/forums/secret_hax.php');
define('FORUM_HANDLER_KEY', 'QRFvd83Mk');

//---------------------------------------------------------------------------------------------
// User
//---------------------------------------------------------------------------------------------
define('USER_COOKIE_NAME', 'UserCookie');
define('USER_COOKIE_KEY', 'TR7f8vI*3');
define('USER_COOKIE_EXPIRATION', 60 * 60 * 24 * 7); // 7 days
define('USER_SESSION_COOKIE_EXPIRATION', 60 * 30); // 30 minutes, no 'remember me'

define('MAX_MESSAGES_PER_USER', 500);

define('USER_STATE_NORMAL', 0);
define('USER_STATE_BANNED', 1);

define('USER_RESET_KEY_SALT', 'f8bj389f');

define('USER_PING_INTERVAL', '-5 minutes');

define('ALMASY_USER_ID', 1);

define('PASSWORD_MIN_CHARS', 6);
define('USERNAME_MAX_CHARS', 15);

define('STARTING_BATTLES', 10);
define('STARTING_MONEY', 100);
define('STARTING_INCOME', 50);
define('USER_STAT_POINTS_PER_LEVEL', 2);

define('CSRF_SALT', 'hij8vgnhb');

//---------------------------------------------------------------------------------------------
// Referrals
//---------------------------------------------------------------------------------------------

define('REFERRAL_SYSTEM_LEVEL_2_USER_ITEM_ID', DEBUG_CONSTANTS ? 2 : 8727);


function REFERRAL_SYSTEM_LEVEL_3_1_USER_ITEM_ID_LIST () {
    if (DEBUG_CONSTANTS)
        return array(
            3
        );

    return array(
        14890,
        14923,
        14924,
        14925,
        14926
    );
}

function REFERRAL_SYSTEM_LEVEL_3_2_USER_ITEM_ID_LIST () {
    if (DEBUG_CONSTANTS)
        return array(
            4
        );

    return array(
        14892,
        14927,
        14928,
        14929,
        14930
    );
}

define('REFERRAL_SYSTEM_LEVEL_1_REQ_BATTLES', 70);
define('REFERRAL_SYSTEM_LEVEL_2_REQ_BATTLES', 150);
define('REFERRAL_SYSTEM_LEVEL_3_REQ_BATTLES', 250);

//---------------------------------------------------------------------------------------------
// Formations
//---------------------------------------------------------------------------------------------
define('MAX_FORMATIONS_PER_USER', 8);
define('MAX_CHARACTERS_PER_FORMATION', 7);
define('FORMATION_NAME_MAX_CHARS', 20);
define('STARTING_REPUTATION', 1000);

//---------------------------------------------------------------------------------------------
// Messages
//---------------------------------------------------------------------------------------------
define('MESSAGES_PER_PAGE', 8);
define('MESSAGE_MAX_LENGTH', 3000);
define('MESSAGE_SUBJECT_MAX_LENGTH', 50);

//---------------------------------------------------------------------------------------------
// Admin
//---------------------------------------------------------------------------------------------
define('HELP_EDITING', 1); // Whether or not editing files is enabled in the help page

//---------------------------------------------------------------------------------------------
// Characters
//---------------------------------------------------------------------------------------------
define('CHARACTER_NAME_MAX_CHARS', 15);

define('CHARACTER_MAX_LEVEL', 99);
define('CHARACTER_STAT_ROLL_MEAN', 4);
define('CHARACTER_STAT_ROLL_VARIANCE', 1);
define('CHARACTER_MAX_ROLL_GROWTH', 7);
define('MAX_LEVEL_GAIN', 3);


define('LEVEL_UP_CHEAT', 0);

define('CHARACTER_ROLL_COST', 20);
define('CHARACTER_KEEP_COST', 200);

define('SHORT_SWORD_USER_ITEM_ID', 33);
define('KNIFE_USER_ITEM_ID', 45);
define('BOOK_USER_ITEM_ID', 79);
define('BOW_USER_ITEM_ID', 56);

define('SHIRT_USER_ITEM_ID', 128);
define('ROBE_USER_ITEM_ID', 119);
define('PLATE_USER_ITEM_ID', 110);

define('SWORDSMAN_CLASS_ID', 5);
define('SPELLCASTER_CLASS_ID', 53);
define('TRAINEE_CLASS_ID', 91);

define('MELEE_BATTLESUIT_CLASS_ID', 109);
define('HVY_BATTLESUIT_CLASS_ID', 111);

define('NEW_CHARACTER_STAT_VARIATION', 0.2);
function NEW_CHARACTER_PARTY() {
    return array(
        // Name,          STR, VIT, INT, DEX, Starting Weapon,          Starting Armor,     Starting Class
        array('name',     5.7, 5.7, 5.7, 5.7, SHORT_SWORD_USER_ITEM_ID, ROBE_USER_ITEM_ID,  0                    ),
        array('Crysello', 5.9, 5.9, 2.0, 2.0, SHORT_SWORD_USER_ITEM_ID, PLATE_USER_ITEM_ID, SWORDSMAN_CLASS_ID   ),
        array('Erik',     3.0, 2.0, 5.8, 5.8, BOOK_USER_ITEM_ID,        ROBE_USER_ITEM_ID,  SPELLCASTER_CLASS_ID ),
        array('Leo',      6.0, 2.0, 3.0, 3.0, BOW_USER_ITEM_ID,         SHIRT_USER_ITEM_ID, TRAINEE_CLASS_ID     ),
        array('Joshua',   1.0, 5.8, 5.8, 3.0, BOOK_USER_ITEM_ID,        ROBE_USER_ITEM_ID,  SPELLCASTER_CLASS_ID ),
        array('Allos',    5.0, 5.0, 5.0, 2.0, KNIFE_USER_ITEM_ID,       ROBE_USER_ITEM_ID,  TRAINEE_CLASS_ID     ),
        array('Matthew',  5.8, 4.0, 2.0, 5.8, KNIFE_USER_ITEM_ID,       SHIRT_USER_ITEM_ID, SWORDSMAN_CLASS_ID   ),
    );
}

//---------------------------------------------------------------------------------------------
// Battles
//---------------------------------------------------------------------------------------------
define('MATCHMAKE_NUM_VISIBLE_CHARACTERS', 3);

define('MIN_REPUTATION', 5);
define('MAX_REPUTATION', 50000);

define('MIN_EXP_GAIN', 100);

define('BOUNTY_ADD_RATE', 4);

define('BATTLE_AWARD_RATE', 1);         // Speed at which you get battles
define('BATTLE_EXP_RATE', 1.3);           // Global rate for exp
define('BATTLE_YB_RATE', 1.3);            // Global rate for yb
function BATTLE_PRORATION_MATRIX () {
    return array(
        array( 1.0,  0.66 ),                 // First row is winning, first column is attacking
        array( 0.3,  0.05 ),
    );
}
//define('TACTICIAN_LEVEL_RATE', 0.5); // Tacticians level up half as fast as regular characters

//---------------------------------------------------------------------------------------------
// Captcha
//---------------------------------------------------------------------------------------------
define('CAPTCHA_INTERVAL', '-25 minutes'); // How long between captchas minimum
define('CAPTCHA_THRESHOLD_NUM_ACTIONS_PER_CAPTCHA', 100); // How many actions minimum before captcha
define('CAPTCHA_RANDOM_CHANCE', 0.001); // Chance of random captcha
define('CAPTCHA_THRESHOLD_APM', 50);

function CAPTCHA_ACTIONS () {
    return array(
        'characters/roll_new_character',
        'battles/matchmake',
        'items/perform_imbue',
        'items/perform_refine',
    );
}

//---------------------------------------------------------------------------------------------
// Imbues
//---------------------------------------------------------------------------------------------
define('IMBUE_ENHANCED_DAMAGE_BONUSTYPE_ID', 37);
define('IMBUE_NUM_RANDOM_MODS', 3);

// Cost of an imbue is MULTIPLIER * value of item + START
define('IMBUE_COST_START', 1000);
define('IMBUE_COST_MULTIPLIER', 3);

define('ENHANCED_DAMAGE_BONUS_TYPE_ID', 37);

//---------------------------------------------------------------------------------------------
// Refines
//---------------------------------------------------------------------------------------------
define('MAX_REFINE', 3);

//---------------------------------------------------------------------------------------------
// Basic Game
//---------------------------------------------------------------------------------------------
define('FORMATION_WIDTH', 4);
define('FORMATION_HEIGHT', 3);

define('AFFINITY_NEUTRAL', 5);

define('AFFINITY_FIRE',   0);
define('AFFINITY_STEEL',  1);
define('AFFINITY_WOOD',   2);
define('AFFINITY_EARTH',  3);
define('AFFINITY_WATER',  4);
define('NUM_AFFINITIES',  5);

function PORTRAIT_LIST () {
    return array(
        'Abyss Knight',
        'Adjudicator',
        'Alchemist',
        'Apothecary',
        'Arbiter',
        'Archer',
        'Armorsmith',
        'Artificer',
        'Artisan',
        'Assassin',
        'Avenger',
        'Barbarian',
        'Bard',
        'Battlesmith',
        'Blacksmith',
        'Blade Dancer',
        'Bounty Hunter',
        'Buccaneer',
        'Champion',
        'Clairvoyant',
        'Conjurer',
        'Conqueror',
        'Crusader',
        'Cultist',
        'Cutthroat',
        'Dark Knight',
        'Deadeye',
        'Death Knight',
        'Diabolist',
        'Dominator',
        'Dragon Knight',
        'Druid',
        'Elementalist',
        'Empath',
        'Enchanter',
        'Entertainer',
        'face',
        'Fighter',
        'Fomenter',
        'Geomancer',
        'Gladiator',
        'Heretic',
        'Hero',
        'Knight',
        'Lancer',
        'Lord',
        'Machinist',
        'Mage',
        'Mancer',
        'Marauder',
        'Master Knight',
        'Master Lancer',
        'Master Weaponsmith',
        'Mastersmith',
        'Melee Battlesuit',
        'Melee Hvy Battlesuit',
        'Melee Hyper Battlesuit',
        'Mercenary',
        'Minstrel',
        'Monarch',
        'Myrmidon',
        'Mystic',
        'Neophyte',
        'Novice',
        'Overlord',
        'Overseer',
        'Paladin',
        'Phoenix Knight',
        'Pirate',
        'Posologist',
        'Professor',
        'Prowler',
        'Pupil',
        'Ranged Battlesuit',
        'Ranged Hvy Battlesuit',
        'Ranged Hyper Battlesuit',
        'Ranger',
        'Rogue',
        'Ruffian',
        'Rune Mage',
        'Rune Master',
        'Rune Priest',
        'Sage',
        'Savage',
        'Savant',
        'Scholar',
        'Sentinel',
        'Shadowmage',
        'Shaman',
        'Slavedriver',
        'Slayer',
        'Sniper',
        'Soothsayer',
        'Sorcerer',
        'Soulcatcher',
        'Spearman',
        'Spectral Knight',
        'Spellcaster',
        'Swashbuckler',
        'Swordsman',
        'Thief',
        'Tinkerer',
        'Trainee',
        'Transcendental',
        'Tyrant',
        'Veteran Armorsmith',
        'Virtuoso',
        'Warlock',
        'Warlord',
        'Warrior',
        'Warsmith',
        'Weaponsmith',
        'Witchdoctor'
    );
}

//---------------------------------------------------------------------------------------------
// Guilds
//---------------------------------------------------------------------------------------------
define('GUILD_STARTING_SIZE', 10);
define('GUILD_UPKEEP_INTERVAL', '1 week');
define('GUILD_NAME_MAX_CHARS', 25);
define('GUILD_CREATION_COST', 1000000);
define('GUILD_EMBLEM_COST', 2000000);
define('GUILD_SIZE_INCREASE', 10);
define('GUILD_SIZE_MAX_LEVEL', 4);


//---------------------------------------------------------------------------------------------
// AJAX
//---------------------------------------------------------------------------------------------
define('AJAX_SUCCESS', 'succ');
define('AJAX_ERROR_CODE', 'err');
define('AJAX_INSUFFICIENT_FUNDS', 'if');

?>
