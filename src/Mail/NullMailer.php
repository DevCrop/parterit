<?php

declare(strict_types=1);

namespace Mail;

final class NullMailer implements MailerInterface
{
    public function send(string $to, string $subject, string $htmlBody): bool
    {
        return true;
    }
}
