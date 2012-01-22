<?

class Bonus extends AppModel {
    var $belongsTo = array(
        'Class',
        'BonusType',
    );

    //---------------------------------------------------------------------------------------------
    // Convert locations back to bitwise flag
    function beforeSave ($results) {
        if (isset($this->data['Bonus']['locations'])) {
            $this->data['Bonus']['location'] = BitLocationFromLocations($this->data['Bonus']['locations']);
            unset($this->data['Bonus']['locations']);
        }
        return true;
    }

    //---------------------------------------------------------------------------------------------
    // Convert location from bitwise flags to actual locations
    function afterFind ($results) {
        foreach ($results as &$bonus) {
            if (!isset($bonus['Bonus']['location']))
                continue;

            $bonus['Bonus']['locations'] = LocationsFromBitLocation($bonus['Bonus']['location']);
        }
        return $results;
    }
};

?>