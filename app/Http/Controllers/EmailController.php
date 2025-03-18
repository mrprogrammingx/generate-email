<?php

namespace App\Http\Controllers;

use App\Services\MailService;

class EmailController extends Controller
{
    public string $token;

    public function __construct(public readonly MailService $mailService)
    {
        // $this->createNewEmail();
        // $this->getTokenForEmail();
    }

    public function createNewEmail()
    {   
        return response()->json($this->mailService->createNewEmail(), 200);
    }

    public function getTokenForEmail()
    {
        return response()->json($this->mailService->getTokenForEmail(), 200);
    }

    public function getLatestMessage(): ?string
    {
        $subject = '';

        return response()->json($this->mailService->getLatestMessage($subject), 200);
    }
}
