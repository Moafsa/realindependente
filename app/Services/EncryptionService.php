<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Log;

class EncryptionService
{
    /**
     * Encrypt sensitive data.
     *
     * @param  mixed  $data
     * @return string|null
     */
    public function encrypt($data): ?string
    {
        if (empty($data)) {
            return null;
        }

        try {
            return Crypt::encryptString(json_encode($data));
        } catch (\Exception $e) {
            Log::error('Encryption failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Decrypt sensitive data.
     *
     * @param  string|null  $encryptedData
     * @return mixed
     */
    public function decrypt(?string $encryptedData)
    {
        if (empty($encryptedData)) {
            return null;
        }

        try {
            $decrypted = Crypt::decryptString($encryptedData);
            return json_decode($decrypted, true);
        } catch (DecryptException $e) {
            Log::error('Decryption failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Encrypt medical information.
     *
     * @param  array  $medicalInfo
     * @return string|null
     */
    public function encryptMedicalInfo(array $medicalInfo): ?string
    {
        return $this->encrypt([
            'data' => $medicalInfo,
            'encrypted_at' => now()->toIso8601String(),
            'type' => 'medical_info',
        ]);
    }

    /**
     * Decrypt medical information.
     *
     * @param  string|null  $encryptedData
     * @return array|null
     */
    public function decryptMedicalInfo(?string $encryptedData): ?array
    {
        $decrypted = $this->decrypt($encryptedData);
        
        if (!$decrypted || !isset($decrypted['data'])) {
            return null;
        }

        return $decrypted['data'];
    }

    /**
     * Encrypt financial data.
     *
     * @param  array  $financialData
     * @return string|null
     */
    public function encryptFinancialData(array $financialData): ?string
    {
        return $this->encrypt([
            'data' => $financialData,
            'encrypted_at' => now()->toIso8601String(),
            'type' => 'financial_data',
        ]);
    }

    /**
     * Decrypt financial data.
     *
     * @param  string|null  $encryptedData
     * @return array|null
     */
    public function decryptFinancialData(?string $encryptedData): ?array
    {
        $decrypted = $this->decrypt($encryptedData);
        
        if (!$decrypted || !isset($decrypted['data'])) {
            return null;
        }

        return $decrypted['data'];
    }

    /**
     * Hash sensitive string (one-way).
     *
     * @param  string  $data
     * @return string
     */
    public function hash(string $data): string
    {
        return hash('sha256', $data);
    }

    /**
     * Verify hashed data.
     *
     * @param  string  $data
     * @param  string  $hash
     * @return bool
     */
    public function verifyHash(string $data, string $hash): bool
    {
        return hash_equals($hash, $this->hash($data));
    }

    /**
     * Sanitize input data to prevent XSS.
     *
     * @param  mixed  $data
     * @return mixed
     */
    public function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }

        if (is_string($data)) {
            // Remove HTML tags
            $data = strip_tags($data);
            
            // Convert special characters to HTML entities
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
            
            // Remove potential JavaScript
            $data = preg_replace('/javascript:/i', '', $data);
            $data = preg_replace('/on\w+\s*=/i', '', $data);
            
            return $data;
        }

        return $data;
    }

    /**
     * Mask sensitive data for display.
     *
     * @param  string  $data
     * @param  int  $visibleChars
     * @return string
     */
    public function mask(string $data, int $visibleChars = 4): string
    {
        $length = strlen($data);
        
        if ($length <= $visibleChars) {
            return str_repeat('*', $length);
        }

        $masked = str_repeat('*', $length - $visibleChars);
        $visible = substr($data, -$visibleChars);

        return $masked . $visible;
    }

    /**
     * Mask email address.
     *
     * @param  string  $email
     * @return string
     */
    public function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        
        if (count($parts) !== 2) {
            return $this->mask($email);
        }

        $username = $parts[0];
        $domain = $parts[1];

        $visibleChars = min(2, strlen($username) - 1);
        $maskedUsername = substr($username, 0, $visibleChars) . str_repeat('*', strlen($username) - $visibleChars);

        return $maskedUsername . '@' . $domain;
    }

    /**
     * Mask phone number.
     *
     * @param  string  $phone
     * @return string
     */
    public function maskPhone(string $phone): string
    {
        // Remove non-numeric characters
        $numeric = preg_replace('/\D/', '', $phone);
        
        if (strlen($numeric) < 4) {
            return str_repeat('*', strlen($numeric));
        }

        $visible = substr($numeric, -4);
        $masked = str_repeat('*', strlen($numeric) - 4);

        return $masked . $visible;
    }

    /**
     * Generate secure random token.
     *
     * @param  int  $length
     * @return string
     */
    public function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Validate data integrity.
     *
     * @param  string  $data
     * @param  string  $signature
     * @param  string  $key
     * @return bool
     */
    public function validateIntegrity(string $data, string $signature, string $key): bool
    {
        $expectedSignature = hash_hmac('sha256', $data, $key);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Sign data for integrity verification.
     *
     * @param  string  $data
     * @param  string  $key
     * @return string
     */
    public function signData(string $data, string $key): string
    {
        return hash_hmac('sha256', $data, $key);
    }
}
