<?php

namespace App\Providers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    protected $mailer;

    public function __construct()

    {
        $this->mailer = new PHPMailer(true);
        require base_path('vendor/autoload.php');

        // SMTP Configuration
        $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
        $this->mailer->isSMTP();
        $this->mailer->Host       = 'globemergelogistics.com';
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = 'support@globemergelogistics.com';
        $this->mailer->Password   = 'Danvincis12$';
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mailer->Port = 465;

        // Other configurations
        $this->mailer->setFrom('support@globemergelogistics.com', 'GlobeMerge Logistics');
        $this->mailer->isHTML(true);
    }

    public function sendMail($to, $subject, $body)
    {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;

            $this->mailer->send();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
