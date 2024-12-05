<?php

namespace App\Traits;

trait GeneratesUserCredentials
{
    protected function generateCredentials(string $firstName, string $lastName): array
    {
        $username = strtolower($firstName . '.' . $lastName . '.' . now()->format('His'));
        $email = $username . '@' . config('app.domain', 'example.com');

        return [
            'username' => $username,
            'email' => $email
        ];
    }
}
