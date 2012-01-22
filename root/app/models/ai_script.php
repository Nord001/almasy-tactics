<?

define('SCRIPT_CACHE', 'ai_script');
define('SCRIPT_CACHE_DURATION', 'long');

define('SCRIPTS_BY_USER_CACHE', 'user_scripts');
define('SCRIPTS_BY_USER_CACHE_DURATION', 'long');

function STARTING_SCRIPTS () {
    return array(
        10,
        11,
        12,
        13,
        14
    );
}

class AiScript extends AppModel {

    var $belongsTo = array(
        'User',
    );

    //--------------------------------------------------------------------------------------------
    function GetAiScript ($scriptId) {
        CheckNumeric($scriptId);

        $cacheKey = GenerateCacheKey(SCRIPT_CACHE, $scriptId);
        $script = Cache::read($cacheKey, SCRIPT_CACHE_DURATION);
        if ($script === false) {
            $script = $this->findById($scriptId);
            if ($script === false)
                return false;

            Cache::write($cacheKey, $script, SCRIPT_CACHE_DURATION);
        }

        return $script;
    }

    //--------------------------------------------------------------------------------------------
    function GetAiScripts ($scriptIds) {
        $data = array();
        foreach ($scriptIds as $scriptId)
            $data[] = $this->GetAiScript($scriptId);

        return $data;
    }

    //--------------------------------------------------------------------------------------------
    function ClearAiScriptCache ($scriptId) {
        CheckNumeric($scriptId);

        $cacheKey = GenerateCacheKey(SCRIPT_CACHE, $scriptId);
        Cache::delete($cacheKey, SCRIPT_CACHE_DURATION);
    }

    //--------------------------------------------------------------------------------------------
    function ClearAiScriptIdsCache ($userId) {
        CheckNumeric($userId);

        $cacheKey = GenerateCacheKey(SCRIPTS_BY_USER_CACHE, $userId);
        Cache::delete($cacheKey, SCRIPTS_BY_USER_CACHE_DURATION);
    }

    //--------------------------------------------------------------------------------------------
    function GetAiScriptIdsByUserId ($userId) {
        CheckNumeric($userId);

        $cacheKey = GenerateCacheKey(SCRIPTS_BY_USER_CACHE, $userId);
        $scriptIds = Cache::read($cacheKey, SCRIPTS_BY_USER_CACHE_DURATION);

        if ($scriptIds !== false)
            return $scriptIds;

        $scriptIds = $this->find('all', array(
            'fields' => array(
                'AiScript.id',
            ),
            'conditions' => array(
                'AiScript.user_id' => $userId,
            ),
        ));
        $scriptIds = Set::classicExtract($scriptIds, '{n}.AiScript.id');

        Cache::write($cacheKey, $scriptIds, SCRIPTS_BY_USER_CACHE_DURATION);
        return $scriptIds;
    }

    //--------------------------------------------------------------------------------------------
    function GiveStartingScripts ($userId) {
        CheckNumeric($userId);

        $startingScripts = STARTING_SCRIPTS();
        $this->begin();
        foreach ($startingScripts as $scriptId) {
            $script = $this->GetAiScript($scriptId);
            $this->create();
            $success = $this->save(array(
                'user_id' => $userId,
                'name' => $script['AiScript']['name'],
                'contents' => $script['AiScript']['contents'],
                'created' => date(DB_FORMAT)
            ));
            if ($success === false)
                break;
        }
        if ($success === false) {
            $this->rollback();
            return false;
        } else {
            $this->commit();
            $this->ClearAiScriptIdsCache($userId);
            return true;
        }
    }

};

?>