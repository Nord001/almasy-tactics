<?

define('SKILL_CACHE', 'skill');
define('SKILL_DURATION', 'long');

define('SKILL_LISTING_CACHE', 'skill_listing');
define('SKILL_LISTING_CACHE_DURATION', 'long');

class Skill extends AppModel {

    var $belongsTo = array('BonusType');

    //--------------------------------------------------------------------------------------------
    function GetSkillByName ($name) {
        $skill = $this->find('first', array(
            'fields' => array(
                'Skill.id',
            ),
            'conditions' => array(
                'Skill.name' => $name,
            ),
        ));

        if ($skill === false)
            return false;

        return $this->GetSkill($skill['Skill']['id']);
    }

    //--------------------------------------------------------------------------------------------
    function GetSkill ($skillId) {
        CheckNumeric($skillId);

        $cacheKey = GenerateCacheKey(SKILL_CACHE, $skillId);
        $skill = Cache::read($cacheKey, SKILL_CACHE_DURATION);
        if ($skill === false) {
            $skill = $this->find('first', array(
                'conditions' => array(
                    'Skill.id' => $skillId,
                ),
                'contain' => array(
                    'BonusType',
                ),
            ));

            if ($skill === false)
                return false;

            $skill['Skill']['BonusType'] = $skill['BonusType'];
            unset($skill['BonusType']);

            Cache::write($cacheKey, $skill, SKILL_CACHE_DURATION);
        }

        return $skill;
    }

    //--------------------------------------------------------------------------------------------
    function GetSkillListing () {
        $skills = Cache::read(SKILL_LISTING_CACHE, SKILL_LISTING_CACHE_DURATION);
        if ($skills !== false)
            return $skills;

        $skills = $this->find('all', array(
            'fields' => array(
                'Skill.id',
                'Skill.name',
            ),
        ));

        $skills = Set::combine($skills, '{n}.Skill.id', '{n}.Skill.name');

        Cache::write(SKILL_LISTING_CACHE, $skills, SKILL_LISTING_CACHE_DURATION);

        return $skills;
    }
}

?>