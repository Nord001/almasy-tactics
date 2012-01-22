<?

class BonusType extends AppModel {
    var $hasMany = array(
        'Bonus',
        'ItemMod',
        'ImbueMod',
    );
};

?>