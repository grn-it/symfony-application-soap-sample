<?php

declare(strict_types=1);

namespace App\Service\Sms;

class SmsSender
{
    public function send(string $phoneNumber, string $text): string
    {
        // SMS sending process...
        
        return sprintf('ok. Message "%s" sent to phone number "%s"', $text, $phoneNumber);
    }
}
