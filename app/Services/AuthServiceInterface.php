<?php

namespace App\Services;

use App\Models\User;

interface AuthServiceInterface
{
    public function register(array $data): User;
    public function login(string $login, string $password, bool $remember = false): array;
    public function logout(): void;
    public function getCurrentUser(): ?User;
    public function refreshToken(): string;
    public function verifyEmail(string $code): bool;
    public function verifyPhone(string $code): bool;
    public function resendVerificationCode(string $type): void;
    public function sendPasswordResetLink(string $email): void;
    public function resetPassword(array $data): bool;
}
