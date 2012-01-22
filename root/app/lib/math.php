<?

//=============================================================================================
// Math
//=============================================================================================

//---------------------------------------------------------------------------------------------
function Clamp ($value, $min, $max) {
    return min(max($min, $value), $max);
}

//---------------------------------------------------------------------------------------------
// Normal distribution using:
// http://en.wikipedia.org/wiki/Normal_distribution#Standardizing_normal_random_variables
$cache = null;
function NormalDistribution ($mean, $variance) {
    global $cache;
    if ($cache !== null) {
        $value = $cache * sqrt($variance) + $mean;
        $cache = null;
        return $value;
    }

    $U = mt_rand() / mt_getrandmax();
    $V = mt_rand() / mt_getrandmax();

    $X = sqrt(-2 * log($U)) * cos(2 * M_PI * $V);
    $Y = sqrt(-2 * log($U)) * sin(2 * M_PI * $V);

    $cache = $Y;

    return $X * sqrt($variance) + $mean;
}

?>