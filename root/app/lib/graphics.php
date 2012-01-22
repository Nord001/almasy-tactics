<?

//---------------------------------------------------------------------------------------------
function HSLValuesToRGBValues ($hsl) {
    $h = $hsl[0]/360;
    $s = $hsl[1]/100;
    $l = $hsl[2]/100;
    if ($s == 0.0) { $r = $g = $b = $l; }
    else {
        if ($l<=0.5) { $m2 = $l*($s+1); }
        else { $m2 = $l+$s-($l*$s); }
        $m1 = $l*2 - $m2;
        $r = Hue($m1, $m2, ($h+1/3));
        $g = Hue($m1, $m2, $h);
        $b = Hue($m1, $m2, ($h-1/3));
    }
    return array(round($r*255), round($g*255), round($b*255));
}

//---------------------------------------------------------------------------------------------
function Hue ($m1, $m2, $h) {
    if ($h<0) { $h = $h+1; }
    if ($h>1) { $h = $h-1; }
    if ($h*6<1) { return $m1+($m2-$m1)*$h*6; }
    if ($h*2<1) { return $m2; }
    if ($h*3<2) { return $m1+($m2-$m1)*(2/3-$h)*6; }
    return $m1;
}

//---------------------------------------------------------------------------------------------
function ColorStrToValues ($str) {
    $str = substr($str, 4, -1);
    $values = split(', ', $str);

    return $values;
}

function RGBValuesToHexStr ($values) {
    list($r, $g, $b) = $values;
    $r = intval($r); $g = intval($g);
    $b = intval($b);

    $r = dechex($r<0?0:($r>255?255:$r));
    $g = dechex($g<0?0:($g>255?255:$g));
    $b = dechex($b<0?0:($b>255?255:$b));

    $color = (strlen($r) < 2?'0':'').$r;
    $color .= (strlen($g) < 2?'0':'').$g;
    $color .= (strlen($b) < 2?'0':'').$b;
    return '#'.$color;
}

//---------------------------------------------------------------------------------------------
// Generate a two-stop gradient from top to bottom, where the bottom color is $percent
// of the top color.
function SimpleGradientBackground ($color, $percent) {
    if (strpos($color, 'rgb(') !== 0)
        return $color;

    $rgb = substr($color, 4, -1);
    $values = split(', ', $rgb);

    $secondValues = array(
        intval($values[0] * $percent),
        intval($values[1] * $percent),
        intval($values[2] * $percent),
    );

    $secondValues = 'rgb(' . implode(', ', $secondValues) . ')';

    GradientBackground(array(
        array(0, $color),
        array(1, $secondValues),
    ));
}

//---------------------------------------------------------------------------------------------
function GradientBackground ($stops) {
    // Webkit.
    echo 'background-image:-webkit-gradient(linear, 0 0, 0 100%,';
    $stopStrs = array();
    foreach ($stops as $stop) {
        $stopStrs[] = "color-stop({$stop[0]}, $stop[1])";
    }
    echo implode(',', $stopStrs);
    echo ");\n";

    // Mozilla.
    echo 'background-image:-moz-linear-gradient(top,';
    $stopStrs = array();
    foreach ($stops as $stop) {
        $percent = $stop[0] * 100;
        $stopStrs[] = "{$stop[1]} {$percent}%";
    }
    echo implode(',', $stopStrs);
    echo ");\n";

    // IE.
    $firstColor = '';
    $secondColor = '';
    if (strpos($stops[0][1], 'rgb(') === 0) {
        $firstColor = RGBValuesToHexStr(ColorStrToValues($stops[0][1]));
        $secondColor = RGBValuesToHexStr(ColorStrToValues($stops[1][1]));
    } else if (strpos($stops[0][1], 'hsl(') === 0) {
        $firstColor = RGBValuesToHexStr(HSLValuesToRGBValues(ColorStrToValues($stops[0][1])));
        $secondColor = RGBValuesToHexStr(HSLValuesToRGBValues(ColorStrToValues($stops[1][1])));
    }
    echo "filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='{$firstColor}', endColorstr='{$secondColor}');\n";
}

//---------------------------------------------------------------------------------------------
function BoxShadow ($string) {
    echo "box-shadow: {$string};\n";
    echo "-moz-box-shadow: {$string};\n";
    echo "-webkit-box-shadow: {$string};\n";
}

//---------------------------------------------------------------------------------------------
function RoundedCorners ($string) {
    echo "border-radius: {$string};\n";
    echo "-moz-border-radius: {$string};\n";
    echo "-webkit-border-radius: {$string};\n";
}

?>