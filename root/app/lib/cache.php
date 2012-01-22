<?

uses('cache');

//----------------------------------------------------------------------------------------------
function GenerateCacheKey (/** Variable arguments **/) {
    $numArgs = func_num_args();

    $data = array();

    $args = func_get_args();
    for ($i = 0; $i < $numArgs; $i++) {
        if (is_array($args[$i]))
            $data[] = implode('__', array_values($args[$i]));
        else
            $data[] = $args[$i];
    }

    return implode("_", $data);
}

//----------------------------------------------------------------------------------------------
function CacheRead ($key, $duration = 'long') {
    return Cache::read($key, $duration);
}

//----------------------------------------------------------------------------------------------
function CacheWrite ($key, $data, $duration = 'long') {
    return Cache::write($key, $data, $duration);
}

//----------------------------------------------------------------------------------------------
function CacheDelete ($key, $duration = 'long') {
    return Cache::delete($key, $duration);
}

?>