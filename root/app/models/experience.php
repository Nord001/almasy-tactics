<?

// Caches the entire exp table
define('EXPERIENCE_CACHE', 'experience');
define('EXPERIENCE_CACHE_DURATION', 'long');

class Experience extends AppModel {
    var $useTable = 'experience';

    //--------------------------------------------------------------------------------------------
    function GetExpForNextLevel ($level) {
        CheckNumeric($level);

        $experience = Cache::read(EXPERIENCE_CACHE, EXPERIENCE_CACHE_DURATION);
        if (!$experience) {
            $experience = $this->find('all');
            $experience = Set::combine($experience, '{n}.Experience.id', '{n}.Experience.value');

            Cache::write(EXPERIENCE_CACHE, $experience, EXPERIENCE_CACHE_DURATION);
        }

        return isset($experience[$level]) ? $experience[$level] : -1;
    }
};

?>