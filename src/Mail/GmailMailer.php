<?php

declare(strict_types=1);

namespace Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

final class GmailMailer implements MailerInterface
{
    private string $from;
    private string $fromName;
    private string $appPassword;

    public function __construct(string $from, string $fromName, string $appPassword)
    {
        $this->from        = $from;
        $this->fromName    = $fromName;
        $this->appPassword = $appPassword;
    }

    public function send(string $to, string $subject, string $htmlBody): bool
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $this->from;
        $mail->Password   = $this->appPassword;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = PHPMailer::CHARSET_UTF8;
        $mail->SMTPDebug  = SMTP::DEBUG_OFF;

        $mail->setFrom($this->from, $this->fromName);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;

        return $mail->send();
    }
}
