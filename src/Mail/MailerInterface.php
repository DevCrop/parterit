<?php

declare(strict_types=1);

namespace Mail;

interface MailerInterface
{
    public function send(string $to, string $subject, string $htmlBody): bool;
}
