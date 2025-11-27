<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AfribaPay Payment Gateway Integration
 * 
 * @version 1.0.0
 * @link https://api-sandbox.afribapay.com/docs
 */
class AfribaPay
{
    /**
     * API Base URLs
     */
    const API_PRODUCTION = 'https://api.afribapay.com';
    const API_SANDBOX = 'https://api-sandbox.afribapay.com';

    /**
     * API Version
     */
    const API_VERSION = 'v1';

    /**
     * Bearer Token for authentication
     */
    protected string $token;

    /**
     * Environment (sandbox or production)
     */
    protected string $environment;

    /**
     * Base URL for API calls
     */
    protected string $baseUrl;

    /**
     * Constructor
     */
    public function __construct(?string $token = null, ?string $environment = null)
    {
        $this->token = $token ?? config('services.afribapay.token');
        $this->environment = $environment ?? config('services.afribapay.environment', 'sandbox');
        
        $this->baseUrl = $this->environment === 'production' 
            ? self::API_PRODUCTION 
            : self::API_SANDBOX;

        if (empty($this->token)) {
            throw new Exception("AfribaPay token is required");
        }
    }

    /**
     * Get list of supported countries, currencies and operators
     * 
     * @return array
     * @throws Exception
     */
    public function getCountries(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/' . self::API_VERSION . '/countries');

            if (!$response->successful()) {
                throw new Exception("Failed to fetch countries: " . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('AfribaPay getCountries error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Initiate a payment transaction
     * 
     * @param array $params Payment parameters
     * @return array
     * @throws Exception
     */
    public function initiatePayment(array $params): array
    {
        $this->validatePaymentParams($params);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/' . self::API_VERSION . '/pay/init', $params);

            $data = $response->json();

            if (!$response->successful()) {
                $errorMsg = $data['message'] ?? 'Payment initiation failed';
                throw new Exception($errorMsg);
            }

            return $data;
        } catch (Exception $e) {
            Log::error('AfribaPay initiatePayment error: ' . $e->getMessage(), [
                'params' => $params
            ]);
            throw $e;
        }
    }

    /**
     * Verify OTP for transactions requiring confirmation
     * 
     * @param string $transactionId
     * @param string $otp
     * @return array
     * @throws Exception
     */
    public function verifyOTP(string $transactionId, string $otp): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/' . self::API_VERSION . '/pay/otp', [
                'transaction_id' => $transactionId,
                'otp' => $otp,
            ]);

            $data = $response->json();

            if (!$response->successful()) {
                $errorMsg = $data['message'] ?? 'OTP verification failed';
                throw new Exception($errorMsg);
            }

            return $data;
        } catch (Exception $e) {
            Log::error('AfribaPay verifyOTP error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId
            ]);
            throw $e;
        }
    }

    /**
     * Check transaction status
     * 
     * @param string $transactionId
     * @return array
     * @throws Exception
     */
    public function checkStatus(string $transactionId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/' . self::API_VERSION . '/pay/status/' . $transactionId);

            $data = $response->json();

            if (!$response->successful()) {
                $errorMsg = $data['message'] ?? 'Status check failed';
                throw new Exception($errorMsg);
            }

            return $data;
        } catch (Exception $e) {
            Log::error('AfribaPay checkStatus error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId
            ]);
            throw $e;
        }
    }

    /**
     * Get operator info from country data
     * 
     * @param string $countryCode
     * @param string $currency
     * @param string $operatorCode
     * @return array|null
     */
    public function getOperatorInfo(string $countryCode, string $currency, string $operatorCode): ?array
    {
        try {
            $countries = $this->getCountries();
            
            if (!isset($countries['data'][$countryCode])) {
                return null;
            }

            $country = $countries['data'][$countryCode];
            
            if (!isset($country['currencies'][$currency])) {
                return null;
            }

            $operators = $country['currencies'][$currency]['operators'];
            
            foreach ($operators as $operator) {
                if ($operator['operator_code'] === $operatorCode) {
                    return $operator;
                }
            }

            return null;
        } catch (Exception $e) {
            Log::error('AfribaPay getOperatorInfo error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate payment parameters
     * 
     * @param array $params
     * @throws Exception
     */
    protected function validatePaymentParams(array $params): void
    {
        $required = [
            'reference',
            'amount',
            'currency',
            'country_code',
            'phone_number',
            'operator_code',
        ];

        foreach ($required as $field) {
            if (empty($params[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }

        // Validate amount
        if (!is_numeric($params['amount']) || $params['amount'] <= 0) {
            throw new Exception("Amount must be a positive number");
        }

        // Validate phone number format (basic check)
        if (!preg_match('/^\d{10,15}$/', $params['phone_number'])) {
            throw new Exception("Invalid phone number format");
        }
    }

    /**
     * Generate unique transaction reference
     * 
     * @param string $prefix
     * @return string
     */
    public static function generateReference(string $prefix = 'AFRIBA'): string
    {
        return $prefix . '-' . date('YmdHis') . '-' . rand(1000, 9999);
    }

    /**
     * Check if operator requires OTP
     * 
     * @param string $countryCode
     * @param string $currency
     * @param string $operatorCode
     * @return bool
     */
    public function requiresOTP(string $countryCode, string $currency, string $operatorCode): bool
    {
        $operator = $this->getOperatorInfo($countryCode, $currency, $operatorCode);
        return $operator ? (bool) $operator['otp_required'] : false;
    }

    /**
     * Get USSD code for operator
     * 
     * @param string $countryCode
     * @param string $currency
     * @param string $operatorCode
     * @return string|null
     */
    public function getUSSDCode(string $countryCode, string $currency, string $operatorCode): ?string
    {
        $operator = $this->getOperatorInfo($countryCode, $currency, $operatorCode);
        return $operator['ussd_code'] ?? null;
    }

    /**
     * Format phone number with country prefix
     * 
     * @param string $phone
     * @param string $countryCode
     * @return string
     */
    public function formatPhoneNumber(string $phone, string $countryCode): string
    {
        try {
            $countries = $this->getCountries();
            $prefix = $countries['data'][$countryCode]['prefix'] ?? '';
            
            // Remove any non-digit characters
            $phone = preg_replace('/\D/', '', $phone);
            
            // Remove leading zeros
            $phone = ltrim($phone, '0');
            
            // Remove prefix if already present
            if (str_starts_with($phone, $prefix)) {
                $phone = substr($phone, strlen($prefix));
            }
            
            // Add prefix
            return $prefix . $phone;
        } catch (Exception $e) {
            return $phone;
        }
    }

    /**
     * Get environment
     * 
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * Check if in sandbox mode
     * 
     * @return bool
     */
    public function isSandbox(): bool
    {
        return $this->environment === 'sandbox';
    }
}
