<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use InvalidArgumentException;

class MailService
{
    public string $domain;

    public string $host;

    public ?string $email = null;

    public string $password;

    public ?string $token = null;

    public function __construct()
    {
        $this->host = config('services.mail.host');
        $this->domain = config('services.mail.domain');
        $this->password = config('services.mail.password');
    }

    public function createNewEmail(): string
    {
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $this->email = $this->generateEmailAddress();

        $response = Http::withHeaders($headers)->post("{$this->host}accounts", [
            "address" => $this->email,
            "password" => $this->password,
        ]);

        if ($response->successful() === true) {
            return $this->email;
        } else {
            throw new RequestException($response);
        }
    }

    public function generateEmailAddress()
    {
        return strtolower(str()->random() . "@$this->domain");
    }

    public function getTokenForEmail()
    {
        ($this->emailExists()) ? $this->email : $this->createNewEmail();

        $headers = [
            'Content-Type' => 'application/json'
        ];

        $response = Http::withHeaders($headers)->post("{$this->host}token", [
            "address" => $this->email,
            "password" => $this->password,
        ]);

        if ($response->successful()) {
            $response = json_decode($response->getBody());
            $this->token = $response->token;
            return $this->token;
        }

        throw new RequestException($response);
    }

    public function getLatestMessage(string $email, string $subject): string
    {
        ($this->emailExists()) ? $this->email : $this->createNewEmail();
        ($this->tokenExists()) ? $this->token : $this->getTokenForEmail();

        $headers = [
            'Authorization' => "Bearer $this->token",
        ];
        
        // var_dump($headers);
        // var_dump($this->email);

        $response = Http::withHeaders($headers)->get("{$this->host}messages");

        if (!$response->successful()) {
            throw new RequestException($response);
        }

        $messages = collect($response->json('hydra:member'));

        $filteredMessages = $messages->where('from.address', $email)
            ->where('subject', $subject)
            ->sortByDesc('createdAt');

        if ($filteredMessages->isEmpty()) {
            return '';
        }

        $latestMessageId = $filteredMessages->first()['id'];

        $messageResponse = Http::withHeaders($headers)->get("{$this->host}messages/$latestMessageId");

        if ($messageResponse->successful()) {
            return $messageResponse->json('html')[0] ?? null;
        }

        throw new RequestException($messageResponse);
    }

    public function emailExists(): bool
    {
        if (!$this->email) {
            return false;
        }

        return true;
    }

    private function tokenExists(): bool
    {
        if (!$this->token) {
            return false;
        }

        return true;
    }
}
