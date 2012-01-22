<?

define('WIDTH', 100);
define('HEIGHT', 40);
define('NUM_LINES', 5);


define('FONT', ROOT . '/app/webroot/files/devroye.ttf');

define('CAPTCHA_SESSION_KEY', 'Captcha.answer');
define('CAPTCHA_ACTIONS_KEY', 'Captcha.numActions');
define('CAPTCHA_REQUIRED_KEY', 'Captcha.required');

define('RENDER_NUMBER_WIDTH', 35);
define('RENDER_NUMBER_HEIGHT', 35);

if (!file_exists(FONT)) {
    echo 'Font does not exist.';
    die();
}

if (!extension_loaded('gd')) {
    echo 'GD required for Captcha.';
    die();
}

class CaptchaComponent extends Object {

    var $components = array('Session', 'GameAuth');

    //--------------------------------------------------------------------------------------------
    function DemandCaptcha () {
        $this->Session->write(CAPTCHA_REQUIRED_KEY, true);
    }

    //--------------------------------------------------------------------------------------------
    function ReleaseCaptcha () {
        $this->Session->del(CAPTCHA_REQUIRED_KEY);
    }

    //--------------------------------------------------------------------------------------------
    function IsCaptchaDemanded () {
        return $this->Session->check(CAPTCHA_REQUIRED_KEY);
    }

    //--------------------------------------------------------------------------------------------
    function SetAnswer ($answer) {
        $this->Session->write(CAPTCHA_SESSION_KEY, $answer);
    }

    //--------------------------------------------------------------------------------------------
    function GetAnswer () {
        return $this->Session->read(CAPTCHA_SESSION_KEY);
    }

    //--------------------------------------------------------------------------------------------
    function ClearNumActions () {
        $this->Session->write(CAPTCHA_ACTIONS_KEY, 0);
    }

    //--------------------------------------------------------------------------------------------
    function IncrementAction () {
        $this->Session->write(CAPTCHA_ACTIONS_KEY, $this->GetNumActions() + 1);
    }

    //--------------------------------------------------------------------------------------------
    function GetNumActions () {
        return $this->Session->read(CAPTCHA_ACTIONS_KEY);
    }

    //---------------------------------------------------------------------------------------------
    function CheckCaptchaRequired () {
        $user = $this->GameAuth->GetLoggedInUser();
        $timeOfLastCaptcha = strtotime($user['User']['last_captcha']);
        $passedInterval =  $timeOfLastCaptcha < strtotime(CAPTCHA_INTERVAL);
        $timeSinceLastCaptcha = time() - $timeOfLastCaptcha;

        $numActions = $this->GetNumActions();
        $passedNumActions = $numActions > CAPTCHA_THRESHOLD_NUM_ACTIONS_PER_CAPTCHA;

        $apm = $numActions / ($timeSinceLastCaptcha / 60);
        $passedAPM = $apm > CAPTCHA_THRESHOLD_APM;

        $rand = mt_rand() / mt_getrandmax();
        $randSuccess = $rand < CAPTCHA_RANDOM_CHANCE;

        $result = ($passedInterval && $passedNumActions) || $randSuccess || $passedAPM;

        $this->log(
            sprintf(
                'Time: %d Actions: %d (has %d) APM: %d (cur: %f) Random: %d Result: %d',
                $passedInterval,
                $passedNumActions,
                $numActions,
                $passedAPM,
                $apm,
                $randSuccess,
                $result
            ),
            'captcha'
        );

        if ($result) {
            $this->DemandCaptcha();
        }

        return $result;
    }

    //--------------------------------------------------------------------------------------------
    function RenderNumber ($num, $randDistort = false) {
        $im = imagecreatetruecolor(RENDER_NUMBER_WIDTH, RENDER_NUMBER_HEIGHT);

        $color = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 0, 0, $color);
        imagecolortransparent($im, $color);

        $color = false;
        if ($randDistort) {
            $color = array(mt_rand(0, 50), mt_rand(0, 50), mt_rand(0, 50));
            $color = imagecolorallocate($im, $color[0], $color[1], $color[2]);
        } else {
            $color = imagecolorallocate($im, 0, 0, 0);
        }

        $x = false;
        if ($randDistort)
            $x = mt_rand(0, 3);
        else
            $x = 3;

        imagestring($im, 5, $x, 8, $num, $color);

        if ($randDistort) {
            for ($i = 0; $i < 3; $i++) {
                $x = mt_rand(0, 35);
                $y = mt_rand(0, 17);
                $color = array(mt_rand(0, 50), mt_rand(0, 50), mt_rand(0, 50));
                imagefilledellipse($im, $x, $y, 1, 1, $color);
            }
        }

        ob_start();
        imagepng($im);
        $image = ob_get_clean();

        return $image;
    }

    //--------------------------------------------------------------------------------------------
    function GenerateCaptcha () {
        $im = imagecreatetruecolor(WIDTH, HEIGHT);

        // Gradient
        $randColor1 = array(mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
        $randColor2 = array(mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));

        for ($x = 0; $x < WIDTH; $x++) {
            $a = 1 - $x / WIDTH;
            $color = array(
                $randColor1[0] * $a + $randColor2[0] * (1 - $a),
                $randColor1[1] * $a + $randColor2[1] * (1 - $a),
                $randColor1[2] * $a + $randColor2[2] * (1 - $a),
            );
            $color = imagecolorallocate($im, $color[0], $color[1], $color[2]);
            imageline($im, $x, 0, $x, HEIGHT, $color);
            imagecolordeallocate($im, $color);
        }

        $num1 = mt_rand(0, 50);
        $num2 = mt_rand(0, 50);
        $ans = $num1 + $num2;
        $string = sprintf('%d+%d', $num1, $num2);
        $startX = mt_rand(5, 8);

        $angle = mt_rand(0, 10);
        $color = array(mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));
        $color = imagecolorallocate($im, $color[0], $color[1], $color[2]);
        $success = imagettftext($im, 20, $angle, $startX, HEIGHT - 5, $color, FONT, $string);
        imagecolordeallocate($im, $color);

        if (!$success) {
            echo 'Failed to write string.';
            return;
        }

        // Lines
        for ($i = 0; $i < NUM_LINES; $i++) {
            $randStartX = mt_rand(0, WIDTH);
            $randEndX = mt_rand(0, WIDTH);
            $color = array(mt_rand(100, 150), mt_rand(100, 150), mt_rand(100, 150));
            $color = imagecolorallocate($im, $color[0], $color[1], $color[2]);
            imageline($im, $randStartX, 0, $randEndX, HEIGHT, $color);
            imagecolordeallocate($im, $color);
        }

        imagefilter($im, IMG_FILTER_MEAN_REMOVAL);
        imagefilter($im, IMG_FILTER_GAUSSIAN_BLUR);

        ob_start();
        imagepng($im);
        $image = ob_get_clean();

        return array('image' => $image, 'answer' => $ans);
    }
}

?>
