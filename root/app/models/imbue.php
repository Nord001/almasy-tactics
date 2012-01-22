<?

// Caches all weapon imbues
define('WEAPON_IMBUE_CACHE', 'imbues_weapon');
define('WEAPON_IMBUE_CACHE_DURATION', 'long');

// Caches all armor imbues
define('ARMOR_IMBUE_CACHE', 'imbues_armor');
define('ARMOR_IMBUE_CACHE_DURATION', 'long');

class Imbue extends AppModel {
    var $hasMany = array(
        'ImbueMod'
    );

    var $knows = array(
        'UserItem',
    );

    //---------------------------------------------------------------------------------------------
    // Determines if the imbue is a weapon or armor.
    function GetImbueType ($imbueId) {
        CheckNumeric($imbueId);

        $weaponImbues = $this->GetWeaponImbues();
        if (in_array($imbueId, array_keys($weaponImbues)))
            return 'weapon';

        $armorImbues = $this->GetArmorImbues();
        if (in_array($imbueId, array_keys($armorImbues)))
            return 'armor';

        return false;
    }

    //---------------------------------------------------------------------------------------------
    function GetWeaponImbues () {
        $imbues = Cache::read(WEAPON_IMBUE_CACHE, WEAPON_IMBUE_CACHE_DURATION);
        if ($imbues)
            return $imbues;

        $imbues = $this->find('list', array(
            'conditions' => array(
                'Imbue.item_type' => 'weapon',
            ),
        ));

        Cache::write(WEAPON_IMBUE_CACHE, $imbues, WEAPON_IMBUE_CACHE_DURATION);

        return $imbues;
    }

    //---------------------------------------------------------------------------------------------
    function GetArmorImbues () {
        $imbues = Cache::read(ARMOR_IMBUE_CACHE, ARMOR_IMBUE_CACHE_DURATION);
        if ($imbues)
            return $imbues;

        $imbues = $this->find('list', array(
            'conditions' => array(
                'Imbue.item_type' => 'armor',
            ),
        ));

        Cache::write(ARMOR_IMBUE_CACHE, $imbues, ARMOR_IMBUE_CACHE_DURATION);

        return $imbues;
    }

    //---------------------------------------------------------------------------------------------
    function GetImbueCost ($userItemId) {
        CheckNumeric($userItemId);

        $userItem = $this->UserItem->GetUserItem($userItemId);
        $value = $userItem['UserItem']['Item']['value'];
        return $value * IMBUE_COST_MULTIPLIER + IMBUE_COST_START;
    }

    //---------------------------------------------------------------------------------------------
    // Rolls an imbue and returns item mods.
    function RollImbue ($imbueId) {
        CheckNumeric($imbueId);

        $imbueMods = $this->ImbueMod->GetImbueModsByImbueId($imbueId);

        if (!$imbueMods)
            return false;

        $itemType = $this->GetImbueType($imbueId);
        $randomMods = array();
        if ($itemType == 'weapon')
            $randomMods = $this->ImbueMod->GetWeaponImbueModsRandomList();
        elseif ($itemType == 'armor')
            $randomMods = $this->ImbueMod->GetArmorImbueModsRandomList();

        // Select random mods
        shuffle($randomMods);
        $randomMods = array_slice($randomMods, 0, IMBUE_NUM_RANDOM_MODS);

        $imbueMods = array_merge($imbueMods, $randomMods);

        foreach ($imbueMods as $mod) {

            $min = $mod['ImbueMod']['min_amount'];
            $max = $mod['ImbueMod']['max_amount'];

            // Check if it doesn't have an amount, in which case it always just gives the mod
            // ie. fire type
            $amountless = ($min == '' && $max == '');

            if (!$amountless) {

                // If int, use int random, otherwise use float
                if (intval($min) == $min && intval($max) == $max) {
                    $chosenAmount = mt_rand(
                        $min,
                        $max
                    );
                } else {
                    $chosenAmount = mt_rand() / mt_getrandmax() * ($max - $min) + $min;
                    $chosenAmount = round($chosenAmount, 1);
                }

                // Don't bother if we rolled 0
                if ($chosenAmount == 0)
                    continue;
            }

            // Only try to roll duration if it has a duration
            $lastsForever = $mod['ImbueMod']['min_duration'] == 0 && $mod['ImbueMod']['max_duration'] == 0;

            $chosenDuration = false;
            if (!$lastsForever) {
                $chosenDuration = mt_rand(
                    $mod['ImbueMod']['min_duration'],
                    $mod['ImbueMod']['max_duration']
                );
            }

            $itemMod = array(
                'ItemMod' => array(
                    'bonus_type_id' => $mod['ImbueMod']['bonus_type_id'],
                ),
            );

            if (!$amountless)
                $itemMod['ItemMod']['amount'] = $chosenAmount;

            if (!$lastsForever)
                $itemMod['ItemMod']['duration'] = $chosenDuration;

            $itemMods[] = $itemMod;
        }

        return $itemMods;
    }
}

?>