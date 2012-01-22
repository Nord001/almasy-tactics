<?php

class HomeController extends AppController {

    var $uses = array('News', 'Character', 'Formation', 'User');

    var $authList = array(
        'index' => AUTH_ALL,
    );

    //---------------------------------------------------------------------------------------------
    function index () {
        $news = $this->News->GetLatestNews();

        $this->set('news', $news, false); // Don't HTML escape news.

        if ($this->GameAuth->GetLoggedInUser() !== false) {
            $this->pageTitle = 'Home';
            $this->render('index');
        } else {
            $this->pageTitle = 'A free strategy RPG browser game';
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

            // message
            $message = '
            <html>
            <body>
              <p>User suggestion on page ' . htmlspecialchars($this->params['form']['current_page']) . ':</p>

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
                $this->log('Could not send suggestion email.');
            }
        }
        $this->fof();
    }
}
?>
