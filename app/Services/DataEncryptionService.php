<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

class DataEncryptionService
{
    /**
     * Chiffrer un numéro de téléphone
     */
    public function encryptPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }
        
        return Crypt::encryptString($phone);
    }

    /**
     * Déchiffrer un numéro de téléphone
     */
    public function decryptPhone(?string $encryptedPhone): ?string
    {
        if (empty($encryptedPhone)) {
            return null;
        }

        try {
            return Crypt::decryptString($encryptedPhone);
        } catch (\Exception $e) {
            // Si le déchiffrement échoue, retourner null
            return null;
        }
    }

    /**
     * Chiffrer une adresse
     */
    public function encryptAddress(?string $address): ?string
    {
        if (empty($address)) {
            return null;
        }
        
        return Crypt::encryptString($address);
    }

    /**
     * Déchiffrer une adresse
     */
    public function decryptAddress(?string $encryptedAddress): ?string
    {
        if (empty($encryptedAddress)) {
            return null;
        }

        try {
            return Crypt::decryptString($encryptedAddress);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Masquer un numéro de téléphone pour l'affichage
     * Exemple: +237612345678 → +237******78
     */
    public function maskPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $length = strlen($phone);
        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        $start = substr($phone, 0, 4);
        $end = substr($phone, -2);
        $middle = str_repeat('*', $length - 6);

        return $start . $middle . $end;
    }

    /**
     * Masquer une adresse email
     * Exemple: user@example.com → u***@example.com
     */
    public function maskEmail(?string $email): ?string
    {
        if (empty($email)) {
            return null;
        }

        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }

        $username = $parts[0];
        $domain = $parts[1];

        if (strlen($username) <= 2) {
            $masked = str_repeat('*', strlen($username));
            return $masked . '@' . $domain;
        }

        $first = $username[0];
        $stars = str_repeat('*', strlen($username) - 1);
        $masked = $first . $stars . '@' . $domain;
        return $masked;
    }

    /**
     * Hashage sécurisé pour tokens (non réversible)
     */
    public function hashToken(string $token): string
    {
        return hash_hmac('sha256', $token, config('app.key'));
    }
}
