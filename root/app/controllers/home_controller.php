<?php

class HomeController extends AppController {

    var $uses = array('News', 'Character', 'Formation', 'User');

    var $authList = array(
        'index' => AUTH_ALL,
        'feedback_form' => AUTH_ALL,
        'feedback' => AUTH_ALL,
    );

    var $components = array('Captcha');

    //---------------------------------------------------------------------------------------------
    function index () {
        $news = $this->News->GetLatestNews();

        $this->set('news', $news);

        $user = $this->GameAuth->GetLoggedInUser();
        if ($user !== false) {
            $characterIds = $this->User->Character->GetCharacterIdsByUserId($user['User']['id']);
            $characters = $this->User->Character->GetCharacters($characterIds);
            $this->set('characters', $characters);

            $topFormations = $this->Formation->GetFormationRankings();
            $topFormationIds = array_slice($topFormations['FormationRankings'], 0, 5);
            $formations = $this->Formation->GetFormations($topFormationIds);
            foreach ($formations as &$formation) {
                $user = $this->User->GetUser($formation['Formation']['user_id']);
                $formation['User'] = $user['User'];
            }

            $this->set('topFormations', $formations);

            $topFormationIds = $this->Formation->GetTopFormationsByBounty();
            $formations = $this->Formation->GetFormations($topFormationIds);
            foreach ($formations as &$formation) {
                $user = $this->User->GetUser($formation['Formation']['user_id']);
                $formation['User'] = $user['User'];
            }
            $this->set('topFormationsByBounty', $formations);

            $this->setPageTitle('Headquarters');
            $this->render('index');
        } else {
            $this->setPageTitle('A free strategy RPG browser game');
            $this->render('home_guest');
        }
    }

    //---------------------------------------------------------------------------------------------
    function feedback () {
        $this->autoRender = false;
        Configure::write('ajaxMode', 1);

        // Receiving via ajax form, so it's in $this->params['form']
        if (!empty($this->params['form'])) {

            $to = 'almasytactics@gmail.com';

            $subject = 'User Suggestion';

            $user = $this->GameAuth->GetLoggedInUser();

            $username = $user !== false ? $user['User']['username'] : 'guest';

            // message
            $message = '
            <html>
            <body>
              <p>User suggestion by ' . $username . ' on page ' . htmlspecialchars($this->params['form']['current_page']) . ':</p>

              <p>' . htmlspecialchars($this->params['form']['feedback']) . '</p>

              <p>
                This is an automated message from Almasy.
              </p>
            </body>
            </html>
            ';

            // To send HTML mail, the Content-type header must be set
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

            // Additional headers
            $headers .= 'From: Almasy Tactics <dontreply@almasytactics.com>' . "\r\n";

            // Mail it
            $success = @mail($to, $subject, $message, $headers);

            if (!$success) {
                IERR('Could not send suggestion email.');
                return;
            }
            return 1;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function feedback_form () {
        if ($this->ShouldUseAjax()) {
            if (!isset($this->params['form']['url'])) {
                IERR('Data incomplete.');
                return;
            }

            $this->set('url', $this->params['form']['url']);
            return;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function captcha_required () {
        if ($this->ShouldUseAjax()) {
            Configure::write('ajaxMode', 1);
            $this->autoRender = false;
            $captchaRequired = $this->Captcha->CheckCaptchaRequired();
            return $captchaRequired ? 1 : 0;
        }
        $this->fof();
    }

    //---------------------------------------------------------------------------------------------
    function captcha () {
        if ($this->ShouldUseAjax()) {
            if (!empty($this->params['form'])) {
                Configure::write('ajaxMode', 1);
                $this->autoRender = false;

                if (!$this->Captcha->IsCaptchaDemanded()) {
                    IERR('Captcha wasn\'t demanded but form submitted.');
                    return 1;
                }

                $answer = $this->Captcha->GetAnswer();
                if ($answer === false) {
                    IERR('No answer stored for captcha.');
                    return 1;
                }

                if (!isset($this->params['form']['answer'])) {
                    IERR('Form data incomplete.');
                    return 0;
                }

                if ($this->params['form']['answer'] != $answer) {
                    return 0;
                }

                $this->Captcha->ClearNumActions();
                $this->Captcha->ReleaseCaptcha();
                $this->User->id = $this->GameAuth->GetLoggedInUserId();
                $this->User->fastSave('last_captcha', date(DB_FORMAT));
                $this->User->ClearUserCache($this->User->id);
                return 1;
            } else {
                $captcha = $this->Captcha->GenerateCaptcha();
                $this->Captcha->SetAnswer($captcha['answer']);
                $this->set('captcha', $captcha);
            }
            return;
        }
        $this->fof();
    }
}
?>
