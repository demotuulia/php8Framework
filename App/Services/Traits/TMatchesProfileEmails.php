<?php

namespace App\Services\Traits;


use App\Factory\FModel;
use App\Helpers\HConfig;
use App\Helpers\HLogStatement;
use App\Models\ContentTexts;
use App\Models\MatchesForm;
use App\Models\MatchesUsers;
use App\Traits\TString;

/**
 * Send emails
 */
trait TMatchesProfileEmails
{
    use TString;

    /**
     * Email to admin
     *
     * @param array $profile
     * @return void
     */
    private function sendAdminNotifications(array $profile): void
    {
        $formId = $profile[0]['matches_form_id'];

        /** @var MatchesForm $mMatchesForm */
        $mMatchesForm = FModel::build('MatchesForm');
        $form = $mMatchesForm->get($formId);
        $emails = explode(',', $form[0]['application_alert_emails']);

        list('body' => $body, 'content' => $content) = $this->getAdminMailContent($form, $profile);
        $this->sendProfileMail(
            $body,
            strip_tags($content),
            'Nieuwe aanmeldening bij test, ' . $form[0]['name'],
            $emails
        );

    }

    /**
     * send profile mail
     *
     * @param string $htmlBody
     * @param string $textContent
     * @param string $subject
     * @param array $emails
     * @return void
     */
    private function sendProfileMail(
        string $htmlBody,
        string $textContent,
        string $subject,
        array  $emails
    ): void
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            if ($_SERVER['HTTP_HOST'] != HConfig::getConfig('liveServer')) {
                $emails = [HConfig::getConfig('mailData.testEmail')];
            }
        } else {
            $emails = [];
        }

        foreach ($emails as $email) {
            $to = $email;
            $subject = $subject;
            $message = $htmlBody;
            $headers = 'From: ' . HConfig::getConfig('mailData.fromEmail') . "\r\n" .
                'Reply-To: ' . HConfig::getConfig('mailData.replyEmail') . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            HLogStatement::set('sending mail');

            HLogStatement::set(
                'SEND MAIL: ' . $to. "\n" .
                'subject: ' .$subject. "\n" .
                'message: ' . $message. "\n" .
                'headers: ' . $headers
            );

            mail($to, $subject, $message, $headers);
            HLogStatement::set('mail sent');
        }

    }

    /**
     * get admin mail content
     *
     * @param array $form
     * @param array $profile
     * @return array
     */
    private function getAdminMailContent(array $form, array $profile): array
    {
        $profileId = $profile[0]['id'];
        $profileName = $profile[0]['name'];
        $formId = $profile[0]['matches_form_id'];

        $frontEndHost = HConfig::getConfig('frontEndHost');

        $link = $frontEndHost . '/admin/forms?formId=' . $formId . '&profileId=' . $profileId;
        $content = '<b>Nieuwe aanmeldeing</b> <br> <br>' .
            'Groep: ' . $form[0]['name'] . '<br>' .
            'Naam: ' . $profileName . '<br>' .
            'Link : ' . $this->htmlLink($link) . '<br><br>' .
            'Let op: u moet eerst naar de site  ' . $this->htmlLink($frontEndHost . '/admin') . ' inloggen.';
        $body = $this->template($content);

        return [
            'body' => $body,
            'content' => $content,
        ];
    }

    /**
     * Send welcome mail
     *
     * @param array $profile
     * @return void
     */
    private function sendApplicationWelcomeMail(array $profile): void
    {
        $formId = $profile[0]['matches_form_id'];
        $userId = $profile[0]['user_id'];
        /** @var MatchesForm $mMatchesForm */
        $mMatchesForm = FModel::build('MatchesForm');
        $form = $mMatchesForm->get($formId);

        /** @var MatchesUsers $mMatchesUsers */
        $mMatchesUsers = FModel::build('MatchesUsers');
        $user = $mMatchesUsers->get($userId);

        $textContent = $form[0]['welcome_mail'];
        $textContent = str_replace(
            '@NAME',
            $profile[0]['name'],
            $textContent
        );

        $htmlBody = $this->template(nl2br($textContent));

        $this->sendProfileMail(
            $htmlBody,
            $textContent,
            $form[0]['welcome_mail_title'],
            [$user[0]['f0_email']]
        );

    }

    /**
     * Mail template
     *
     * @param string $content  HTML content to add to the template
     * @return string
     */
    private function template(string $content): string
    {
        $body = <<<EOF
   <html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Simple Transactional Email</title>
    <style media="all" type="text/css">
    /* -------------------------------------
    GLOBAL RESETS
------------------------------------- */
    
    body {
      font-family: Helvetica, sans-serif;
      -webkit-font-smoothing: antialiased;
      font-size: 16px;
      line-height: 1.3;
      -ms-text-size-adjust: 100%;
      -webkit-text-size-adjust: 100%;
    }
    
    table {
      border-collapse: separate;
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
      width: 100%;
    }
    
    table td {
      font-family: Helvetica, sans-serif;
      font-size: 14px;
      vertical-align: top;
      padding: 0.5em;
    }
    /* -------------------------------------
    BODY & CONTAINER
------------------------------------- */
    
    body {
      background-color: #f4f5f6;
      margin: 0;
      padding: 0;
    }
    
    .body {
      background-color: #f4f5f6;
      width: 100%;
    }
    
    .container {
      margin: 0 auto !important;
      max-width: 600px;
      padding: 0;
      padding-top: 24px;
      width: 600px;
    }
    
    .content {
      box-sizing: border-box;
      display: block;
      margin: 0 auto;
      max-width: 600px;
      padding: 0;
    }
    /* -------------------------------------
    HEADER, FOOTER, MAIN
------------------------------------- */
    
    .main {
      background: #ffffff;
      border: 1px solid #eaebed;
      border-radius: 16px;
      width: 100%;
    }
    
    .wrapper {
      box-sizing: border-box;
      padding: 24px;
    }
    
    .footer {
      clear: both;
      padding-top: 24px;
      text-align: center;
      width: 100%;
      font-size: 12px;
    text-align: left;
    background-color: #444444;
    padding: 15px;
    margin-top: 10px;
    }
    
    .footer td,
    .footer p,
    .footer span,
    .footer a {
      color: #9a9ea6;
      text-align: left;
      font-size: 12px;
    }
    /* -------------------------------------
    TYPOGRAPHY
------------------------------------- */
    
    p {
      font-family: Helvetica, sans-serif;
      font-size: 16px;
      font-weight: normal;
      margin: 0;
      margin-bottom: 16px;
    }
    
    a {
      color: #0867ec;
      text-decoration: underline;
    }
    /* -------------------------------------
    BUTTONS
------------------------------------- */
    
    .btn {
      box-sizing: border-box;
      min-width: 100% !important;
      width: 100%;
    }
    
    .btn > tbody > tr > td {
      padding-bottom: 16px;
    }
    
    .btn table {
      width: auto;
    }
    
    .btn table td {
      background-color: #ffffff;
      border-radius: 4px;
      text-align: center;
    }
    
    .btn a {
      background-color: #ffffff;
      border: solid 2px #0867ec;
      border-radius: 4px;
      box-sizing: border-box;
      color: #0867ec;
      cursor: pointer;
      display: inline-block;
      font-size: 16px;
      font-weight: bold;
      margin: 0;
      padding: 12px 24px;
      text-decoration: none;
      text-transform: capitalize;
    }
    
    .btn-primary table td {
      background-color: #0867ec;
    }
    
    .btn-primary a {
      background-color: #0867ec;
      border-color: #0867ec;
      color: #ffffff;
    }
    
    @media all {
      .btn-primary table td:hover {
        background-color: #ec0867 !important;
      }
      .btn-primary a:hover {
        background-color: #ec0867 !important;
        border-color: #ec0867 !important;
      }
    }
    
    /* -------------------------------------
    OTHER STYLES THAT MIGHT BE USEFUL
------------------------------------- */
    
    .last {
      margin-bottom: 0;
    }
    
    .first {
      margin-top: 0;
    }
    
    .align-center {
      text-align: center;
    }
    
    .align-right {
      text-align: right;
    }
    
    .align-left {
      text-align: left;
    }
    
    .text-link {
      color: #0867ec !important;
      text-decoration: underline !important;
    }
    
    .clear {
      clear: both;
    }
    
    .mt0 {
      margin-top: 0;
    }
    
    .mb0 {
      margin-bottom: 0;
    }
    
    .preheader {
      color: transparent;
      display: none;
      height: 0;
      max-height: 0;
      max-width: 0;
      opacity: 0;
      overflow: hidden;
      mso-hide: all;
      visibility: hidden;
      width: 0;
    }
    
    .powered-by a {
      text-decoration: none;
    }
    
    /* -------------------------------------
    RESPONSIVE AND MOBILE FRIENDLY STYLES
------------------------------------- */
    
    @media only screen and (max-width: 640px) {
      .main p,
      .main td,
      .main span {
        font-size: 16px !important;
      }
      .wrapper {
        padding: 8px !important;
      }
      .content {
        padding: 0 !important;
      }
      .container {
        padding: 0 !important;
        padding-top: 8px !important;
        width: 100% !important;
      }
      .main {
        border-left-width: 0 !important;
        border-radius: 0 !important;
        border-right-width: 0 !important;
      }
      .btn table {
        max-width: 100% !important;
        width: 100% !important;
      }
      .btn a {
        font-size: 16px !important;
        max-width: 100% !important;
        width: 100% !important;
      }
    }
    /* -------------------------------------
    PRESERVE THESE STYLES IN THE HEAD
------------------------------------- */
    
    @media all {
      .ExternalClass {
        width: 100%;
      }
      .ExternalClass,
      .ExternalClass p,
      .ExternalClass span,
      .ExternalClass font,
      .ExternalClass td,
      .ExternalClass div {
        line-height: 100%;
      }
      .apple-link a {
        color: inherit !important;
        font-family: inherit !important;
        font-size: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
        text-decoration: none !important;
      }
      #MessageViewBody a {
        color: inherit;
        text-decoration: none;
        font-size: inherit;
        font-family: inherit;
        font-weight: inherit;
        line-height: inherit;
      }
    }
    </style>
  </head>
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td>&nbsp;</td>
        <td class="container">
          <div class="content">

            <!-- START CENTERED WHITE CONTAINER -->
            <span class="preheader">This is preheader text. Some clients will show this text as a preview.</span>
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="main">
            <tr>
            <td align="left">
                <img style="height: 10em; margin-bottom: 1em" src="https://aanmelden.test.nl/emailImages/logo.png">
            </td>
            </tr>
                <tr>
                <td>
                    <!-- START MAIN CONTENT AREA -->
                        --@CONTENT@--
                    <!-- END MAIN CONTENT AREA -->        
                </td>
               </tr>
               <tr><td>&nbsp;</td></tr>
              <tr>
                  <td class=" footer">
                   --@FOOTER@--
                
                  </td>
                </tr>
              </table>

           
            
<!-- END CENTERED WHITE CONTAINER --></div>
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>
  </body>
</html>
EOF;
        /** @var ContentTexts $mContentTexts */
        $mContentTexts = FModel::build('ContentTexts');
        $footer = $mContentTexts->get('FOOTER' , ['column'=>'text_key'])[0]['text'];

        $body = str_replace('--@CONTENT@--', $content, $body);
        $body = str_replace('--@FOOTER@--', $footer, $body);
        return $body;
    }

}