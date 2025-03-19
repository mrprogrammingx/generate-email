<?php

namespace App\Http\Controllers;

use App\Services\MailService;
use App\Http\Requests\LatestEmailRequest;

class EmailController extends Controller
{
    public string $token;

    public function __construct(public readonly MailService $mailService)
    {
    }

    public function createNewEmail()
    {   
        return response()->json($this->mailService->createNewEmail(), 200);
    }

    public function getTokenForEmail()
    {
        return response()->json($this->mailService->getTokenForEmail(), 200);
    }

    public function getLatestMessage(LatestEmailRequest $request): ?string
    {
        $data = $request->validated();

        return response()->json($this->mailService->getLatestMessage($data['email'], $data['subject']), 200);
    }
}
