<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * PawaPay Mobile Money Aggregator
 *
 * Agrégateur pan-africain de mobile money (deposits, payouts, refunds).
 * API asynchrone : l'initiation répond ACCEPTED/REJECTED puis le statut
 * final est reçu via callback ou en interrogeant les endpoints de statut.
 *
 * Documentation : https://docs.pawapay.io
 */
class PawaPay
{
    protected string $token;
    protected string $environment;
    protected bool $signRequests;
    protected ?string $keyId;
    protected ?string $privateKeyPath;
    protected string $baseUrl;

    public function __construct(?array $config = null)
    {
        $config = $config ?? app('config')->get('services.pawapay', []);

        $this->token = $config['token'] ?? '';
        $this->environment = $config['environment'] ?? 'sandbox';
        $this->signRequests = (bool) ($config['sign_requests'] ?? false);
        $this->keyId = $config['key_id'] ?? null;
        $this->privateKeyPath = $config['private_key_path'] ?? null;

        $this->baseUrl = $config['base_url']
            ?? ($this->environment === 'production'
                ? ($config['production_url'] ?? 'https://api.pawapay.io')
                : ($config['sandbox_url'] ?? 'https://api.sandbox.pawapay.io'));
    }

    public function isConfigured(): bool
    {
        return !empty($this->token);
    }

    public function isEnabled(): bool
    {
        return (bool) config('services.pawapay.enabled', false);
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Normalise un numéro de téléphone au format MSISDN (chiffres uniquement,
     * avec indicatif pays, sans '+' ni zéros initiaux).
     */
    public function normalizePhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^\d]/', '', $phone);

        // Numéro local congolais avec zéro initial (ex: 0812345678)
        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            return '243' . substr($phone, 1);
        }

        // Numéro local sans indicatif (ex: 812345678 -> RDC)
        if (strlen($phone) === 9 && !str_starts_with($phone, '0')) {
            return '243' . $phone;
        }

        return $phone;
    }

    /**
     * Normalise le montant au format attendu par PawaPay (string, sans
     * zéros superflus, décimales optionnelles).
     */
    protected function formatAmount(float $amount): string
    {
        $formatted = number_format($amount, 2, '.', '');
        return rtrim(rtrim($formatted, '0'), '.');
    }

    /**
     * Génère un ID unique UUIDv4 (depositId/payoutId) requis par PawaPay.
     */
    public function generatePaymentId(): string
    {
        return (string) Str::uuid();
    }

    /**
     * Calcule le Content-Digest (SHA-256 ou SHA-512) d'un body JSON.
     */
    public function computeContentDigest(string $body, string $algorithm = 'sha-512'): string
    {
        $hash = hash($algorithm === 'sha-512' ? 'sha512' : 'sha256', $body, true);
        return $algorithm . '=:' . base64_encode($hash) . ':';
    }

    /**
     * Construit les headers de signature RFC-9421 pour une requête
     * financière sortante (dépôt/payout). Retourne les headers à fusionner
     * avec la requête, ou [] si la signature n'est pas activée/configurée.
     */
    protected function buildSignedHeaders(string $method, string $url, string $body, string $path): array
    {
        if (!$this->signRequests || empty($this->keyId) || empty($this->privateKeyPath) || !is_file($this->privateKeyPath)) {
            return [];
        }

        try {
            $privateKey = openssl_pkey_get_private(file_get_contents($this->privateKeyPath));
            if (!$privateKey) {
                throw new \Exception('Impossible de charger la clé privée PawaPay');
            }

            $algorithm = 'rsa-pss-sha512';
            $hashAlgo = OPENSSL_ALGO_SHA512;

            $created = time();
            $expires = $created + 60;

            $contentDigest = $this->computeContentDigest($body, 'sha-512');
            $signatureDate = gmdate('Y-m-d\TH:i:s.u\Z', $created);
            $authority = parse_url($url, PHP_URL_HOST);
            if (($port = parse_url($url, PHP_URL_PORT))) {
                $authority .= ':' . $port;
            }

            $components = [
                '@method' => strtoupper($method),
                '@authority' => $authority,
                '@path' => $path,
                'signature-date' => $signatureDate,
                'content-digest' => $contentDigest,
                'content-type' => 'application/json',
            ];

            $signatureInput = '("' . implode('" "', array_keys($components)) . '");'
                . 'alg="' . $algorithm . '";keyid="' . $this->keyId . '";'
                . 'created=' . $created . ';expires=' . $expires;

            $signatureBase = $this->buildSignatureBase($components, $signatureInput);

            $signature = $this->signPss($privateKey, $signatureBase, 'sha512');

            return [
                'Content-Digest' => $contentDigest,
                'Signature-Date' => $signatureDate,
                'Signature' => 'sig-pp=:' . base64_encode($signature) . ':',
                'Signature-Input' => 'sig-pp=' . $signatureInput,
            ];
        } catch (\Exception $e) {
            Log::error('PawaPay: Impossible de signer la requête', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Signe un message en RSASSA-PSS (RSA-PSS avec MGF1).
     */
    protected function signPss($privateKey, string $message, string $hashName): string
    {
        $details = openssl_pkey_get_details($privateKey);
        $n = $this->bytesToInt($details['rsa']['n']);
        $d = $this->bytesToInt($details['rsa']['d']);

        $hLen = strlen(hash($hashName, '', true));
        $emBits = $details['bits'] - 1;
        $emLen = intdiv($emBits + 7, 8);
        $k = $details['bits'] / 8;

        // MGF1-SHA-x(salt, hLen) puis EMSA-PSS encode
        $mHash = hash($hashName, $message, true);
        $salt = random_bytes($hLen);
        $m1 = $mHash . $salt;
        $h = hash($hashName, $m1, true);
        $ps = str_repeat("\0", $emLen - strlen($mHash) - $hLen - 2);
        $db = $ps . "\x01" . $salt;
        $dbMask = $this->mgf1($h, $emLen - $hLen - 1, $hashName);
        $maskedDb = $db ^ $dbMask;
        $maskedDb = substr($maskedDb, 0, $emLen - $hLen - 1);

        // RSASSA-PSS : EM doit être < 2^emBits, donc bit de poids fort à 0
        if (ord($maskedDb[0]) & 0x80) {
            $maskedDb[0] = chr(ord($maskedDb[0]) & 0x7f);
        }

        $em = $maskedDb . $h . "\xbc";

        $emInt = $this->bytesToInt($em);
        $s = $this->intToBytes($this->modpow($emInt, $d, $n), $k);

        return $s;
    }

    /**
     * Vérifie la signature RFC-9421 d'un callback.
     *
     * @param Request $request
     * @param array $publicKeys Liste des clés publiques [id => PEM] (optionnel,
     *                          sinon récupérées via l'endpoint public-keys)
     * @return bool
     */
    public function verifyCallback(Request $request, ?array $publicKeys = null): bool
    {
        $body = $request->getContent();

        // 1. Vérifier l'intégrité du body (Content-Digest)
        $digestHeader = $request->header('Content-Digest');
        if (!$digestHeader || !$this->verifyContentDigest($body, $digestHeader)) {
            Log::warning('PawaPay: Content-Digest invalide ou manquant', [
                'digest' => $digestHeader ? substr($digestHeader, 0, 24) . '...' : null,
            ]);

            return false;
        }

        // 2. Vérifier la signature RFC-9421 si présente
        $signatureHeader = $request->header('Signature');
        $signatureInputHeader = $request->header('Signature-Input');
        $signatureDate = $request->header('Signature-Date');

        if (!$signatureHeader || !$signatureInputHeader) {
            // Callbacks non signés (fonctionnalité non activée côté PawaPay)
            return true;
        }

        $publicKeys = $publicKeys ?? $this->getPublicKeys();
        if (empty($publicKeys)) {
            Log::warning('PawaPay: Aucune clé publique disponible pour vérifier le callback');
            return false;
        }

        try {
            [$label, $input] = $this->parseSignatureInput($signatureInputHeader);
            $signature = $this->decodeSignatureValue($signatureHeader, $label);
            $components = $this->buildCallbackComponents($request, $input, $signatureDate);
            $signatureBase = $this->buildSignatureBase($components, $signatureInputHeader);

            $keyId = $input['params']['keyid'] ?? null;
            $alg = $input['params']['alg'] ?? null;

            if (!$keyId || !isset($publicKeys[$keyId])) {
                Log::warning('PawaPay: Clé publique introuvable pour keyid', ['keyid' => $keyId]);
                return false;
            }

            $verified = $this->verifySignature($alg, $signatureBase, $signature, $publicKeys[$keyId]);

            if (!$verified) {
                Log::warning('PawaPay: Signature de callback invalide', [
                    'keyid' => $keyId,
                    'alg' => $alg,
                ]);
            }

            return $verified;
        } catch (\Exception $e) {
            Log::error('PawaPay: Erreur lors de la vérification de signature', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Vérifie qu'un Content-Digest correspond bien au body reçu.
     */
    public function verifyContentDigest(string $body, string $digestHeader): bool
    {
        // Format : <algo>=:<base64>:
        $eq = strpos($digestHeader, '=');
        if ($eq === false) {
            return false;
        }

        $algorithm = strtolower(substr($digestHeader, 0, $eq));
        $encoded = trim(substr($digestHeader, $eq + 1), ':');

        if (!in_array($algorithm, ['sha-256', 'sha-512'], true)) {
            return false;
        }

        $expected = base64_encode(hash($algorithm === 'sha-512' ? 'sha512' : 'sha256', $body, true));

        return hash_equals($expected, $encoded);
    }

    /**
     * Construit la signature base d'un callback selon RFC-9421.
     */
    protected function buildCallbackComponents(Request $request, array $input, ?string $signatureDate): array
    {
        $components = [];

        foreach ($input['components'] as $name) {
            $name = strtolower($name);

            $value = match ($name) {
                '@method' => $request->method(),
                '@authority' => $request->getHttpHost(),
                '@path' => $request->getRequestUri() ?: '/',
                'signature-date' => $signatureDate ?? '',
                'content-digest' => $request->header('Content-Digest') ?? '',
                'content-type' => $request->header('Content-Type') ?? '',
                default => $request->header($name) ?? '',
            };

            $components[$name] = $value;
        }

        return $components;
    }

    /**
     * Construit une signature base RFC-9421 à partir des composants.
     */
    protected function buildSignatureBase(array $components, string $signatureInput): string
    {
        $lines = [];

        foreach ($components as $name => $value) {
            $lines[] = '"' . strtolower($name) . '": ' . $this->quoteHeaderValue($value);
        }

        $lines[] = '"@signature-params": ' . $signatureInput;

        return implode("\n", $lines);
    }

    /**
     * Quote une valeur de header selon les règles RFC-9421.
     */
    protected function quoteHeaderValue(string $value): string
    {
        $value = str_replace(['\\', '"'], ['\\\\', '\\"'], $value);
        $value = preg_replace('/[\x00-\x1f\x7f]/', '', $value) ?? $value;

        return '"' . $value . '"';
    }

    /**
     * Parse un header Signature-Input RFC-9421.
     */
    protected function parseSignatureInput(string $header): array
    {
        $eq = strpos($header, '=');
        if ($eq === false) {
            throw new \Exception('Signature-Input invalide');
        }

        $label = substr($header, 0, $eq);
        $rest = substr($header, $eq + 1);

        if (str_starts_with($rest, '(')) {
            $close = strpos($rest, ')');
            if ($close === false) {
                throw new \Exception('Signature-Input invalide (parenthèses)');
            }

            $components = array_map(
                fn ($component) => trim($component, '"'),
                preg_split('/\s+/', substr($rest, 1, $close - 1)) ?: []
            );
            $paramsPart = substr($rest, $close + 1);
        } else {
            throw new \Exception('Signature-Input invalide (pas de liste)');
        }

        $params = [];
        if (preg_match_all('/([a-z0-9-]+)="?([^";]*)"?/', $paramsPart, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $params[$match[1]] = $match[2];
            }
        }

        return [
            $label,
            [
                'label' => $label,
                'components' => $components,
                'params' => $params,
            ],
        ];
    }

    /**
     * Décode la valeur d'un header Signature RFC-9421.
     */
    protected function decodeSignatureValue(string $header, string $label): string
    {
        foreach (explode(',', $header) as $part) {
            $part = trim($part);
            $eq = strpos($part, '=');
            if ($eq === false) {
                continue;
            }

            if (trim(substr($part, 0, $eq)) === $label) {
                $value = trim(substr($part, $eq + 1));
                if (str_starts_with($value, ':')) {
                    return base64_decode(substr($value, 1, -1), true);
                }

                return base64_decode($value, true);
            }
        }

        throw new \Exception('Signature introuvable pour le label ' . $label);
    }

    /**
     * Vérifie une signature selon l'algorithme RFC-9421 indiqué.
     */
    protected function verifySignature(?string $alg, string $signatureBase, string $signature, string $pem): bool
    {
        $publicKey = openssl_pkey_get_public($pem);
        if (!$publicKey) {
            return false;
        }

        switch ($alg) {
            case 'ecdsa-p256-sha256':
            case 'ecdsa-p384-sha384':
                $hashAlgo = $alg === 'ecdsa-p256-sha256' ? OPENSSL_ALGO_SHA256 : OPENSSL_ALGO_SHA384;
                $der = $this->ecdsaRawToDer($signature);

                return openssl_verify($signatureBase, $der, $publicKey, $hashAlgo) === 1;

            case 'rsa-pss-sha512':
            case 'rsa-pss-sha256':
                $hashName = $alg === 'rsa-pss-sha512' ? 'sha512' : 'sha256';

                return $this->verifyPss($publicKey, $signatureBase, $signature, $hashName);

            case 'rsa-v1_5-sha256':
                return openssl_verify($signatureBase, $signature, $publicKey, OPENSSL_ALGO_SHA256) === 1;

            default:
                return false;
        }
    }

    /**
     * Vérifie une signature RSASSA-PSS (MGF1) manuellement.
     */
    protected function verifyPss($publicKey, string $message, string $signature, string $hashName): bool
    {
        $details = openssl_pkey_get_details($publicKey);
        if (!isset($details['rsa']['n'])) {
            return false;
        }

        $n = $this->bytesToInt($details['rsa']['n']);
        $e = $this->bytesToInt($details['rsa']['e']);
        $bits = $details['bits'];
        $k = intdiv($bits + 7, 8);

        if (strlen($signature) > $k) {
            return false;
        }

        $hLen = strlen(hash($hashName, '', true));
        $emBits = $bits - 1;
        $emLen = intdiv($emBits + 7, 8);

        $s = $this->bytesToInt($signature);
        if (gmp_cmp($s, $n) >= 0) {
            return false;
        }

        $em = $this->intToBytes($this->modpow($s, $e, $n), $k);
        if (strlen($em) > $emLen) {
            return false;
        }

        // Trailer byte
        if (substr($em, -1) !== "\xbc") {
            return false;
        }

        $maskedDb = substr($em, 0, $emLen - $hLen - 1);
        $h = substr($em, $emLen - $hLen - 1, $hLen);

        $dbMask = $this->mgf1($h, $emLen - $hLen - 1, $hashName);
        $db = $maskedDb ^ $dbMask;

        $db = substr($db, 0, $emLen - $hLen - 1);

        // EMSA-PSS : remettre à 0 le bit de poids fort du premier octet de DB
        if (ord($db[0]) & 0x80) {
            $db[0] = chr(ord($db[0]) & 0x7f);
        }

        // Vérifier PS (octets nuls) + séparateur 0x01
        $psLen = $emLen - $hLen - $hLen - 2;
        if (substr($db, 0, $psLen) !== str_repeat("\0", $psLen)) {
            return false;
        }

        if (substr($db, $psLen, 1) !== "\x01") {
            return false;
        }

        $salt = substr($db, $psLen + 1, $hLen);

        $mHash = hash($hashName, $message, true);
        $h2 = hash($hashName, $mHash . $salt, true);

        return hash_equals($h, $h2);
    }

    /**
     * Convertis une signature ECDSA brute (r||s) en DER pour openssl_verify.
     */
    protected function ecdsaRawToDer(string $raw): string
    {
        $len = strlen($raw);
        if ($len % 2 !== 0 || $len < 8) {
            throw new \Exception('Signature ECDSA brute invalide');
        }

        $r = substr($raw, 0, $len / 2);
        $s = substr($raw, $len / 2);

        $content = $this->encodeDerInteger($r) . $this->encodeDerInteger($s);

        return "\x30" . chr(strlen($content)) . $content;
    }

    protected function encodeDerInteger(string $integer): string
    {
        $integer = ltrim($integer, "\0");
        if ($integer === '') {
            $integer = "\0";
        }

        if (ord($integer[0]) & 0x80) {
            $integer = "\0" . $integer;
        }

        return "\x02" . chr(strlen($integer)) . $integer;
    }

    /**
     * Récupère les clés publiques utilisées par PawaPay pour signer les callbacks.
     *
     * @return array<string, string> [id => PEM]
     */
    public function getPublicKeys(): array
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(15)
                ->get($this->baseUrl . '/v2/public-key/http');

            if (!$response->successful()) {
                return [];
            }

            $keys = [];
            foreach ($response->json() ?? [] as $item) {
                if (isset($item['id'], $item['key'])) {
                    $keys[$item['id']] = $item['key'];
                }
            }

            return $keys;
        } catch (\Exception $e) {
            Log::error('PawaPay: Erreur récupération clés publiques', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Initie un dépôt (encaissement).
     *
     * @param array $data [
     *   'depositId' => string UUIDv4,
     *   'amount' => float,
     *   'currency' => string (ex: CDF, USD, ZMW, RWF...),
     *   'phoneNumber' => string (MSISDN),
     *   'provider' => string (ex: VODACOM_CD, MTN_MOMO_RWA...),
     *   'clientReferenceId' => string|null,
     *   'metadata' => array|null,
     * ]
     * @return array
     */
    public function initiateDeposit(array $data): array
    {
        $depositId = $data['depositId'] ?? $this->generatePaymentId();

        $payload = [
            'depositId' => $depositId,
            'amount' => $this->formatAmount((float) $data['amount']),
            'currency' => strtoupper($data['currency'] ?? 'USD'),
            'payer' => [
                'type' => 'MMO',
                'accountDetails' => [
                    'phoneNumber' => $this->normalizePhoneNumber($data['phoneNumber']),
                    'provider' => $data['provider'],
                ],
            ],
        ];

        if (!empty($data['clientReferenceId'])) {
            $payload['clientReferenceId'] = $data['clientReferenceId'];
        }

        if (!empty($data['metadata']) && is_array($data['metadata'])) {
            $payload['metadata'] = array_map(
                fn ($value, $key) => ['fieldName' => (string) $key, 'value' => (string) $value],
                $data['metadata'],
                array_keys($data['metadata'])
            );
        }

        return $this->request('POST', '/v2/deposits', $payload, $depositId);
    }

    /**
     * Vérifie le statut d'un dépôt.
     */
    public function checkDepositStatus(string $depositId): array
    {
        return $this->request('GET', '/v2/deposits/' . $depositId, null, $depositId);
    }

    /**
     * Initie un payout (décaissement).
     *
     * @param array $data [
     *   'payoutId' => string UUIDv4,
     *   'amount' => float,
     *   'currency' => string,
     *   'phoneNumber' => string (MSISDN),
     *   'provider' => string,
     *   'clientReferenceId' => string|null,
     *   'customerMessage' => string|null (4-22 caractères alphanumériques),
     *   'metadata' => array|null,
     * ]
     * @return array
     */
    public function initiatePayout(array $data): array
    {
        $payoutId = $data['payoutId'] ?? $this->generatePaymentId();

        $payload = [
            'payoutId' => $payoutId,
            'amount' => $this->formatAmount((float) $data['amount']),
            'currency' => strtoupper($data['currency'] ?? 'USD'),
            'recipient' => [
                'type' => 'MMO',
                'accountDetails' => [
                    'phoneNumber' => $this->normalizePhoneNumber($data['phoneNumber']),
                    'provider' => $data['provider'],
                ],
            ],
        ];

        if (!empty($data['clientReferenceId'])) {
            $payload['clientReferenceId'] = $data['clientReferenceId'];
        }

        if (!empty($data['customerMessage'])) {
            $payload['customerMessage'] = substr($data['customerMessage'], 0, 22);
        }

        if (!empty($data['metadata']) && is_array($data['metadata'])) {
            $payload['metadata'] = array_map(
                fn ($value, $key) => ['fieldName' => (string) $key, 'value' => (string) $value],
                $data['metadata'],
                array_keys($data['metadata'])
            );
        }

        return $this->request('POST', '/v2/payouts', $payload, $payoutId);
    }

    /**
     * Vérifie le statut d'un payout.
     */
    public function checkPayoutStatus(string $payoutId): array
    {
        return $this->request('GET', '/v2/payouts/' . $payoutId, null, $payoutId);
    }

    /**
     * Configuration active du compte (pays, providers, limites, devises).
     */
    public function getActiveConfiguration(?string $country = null, string $operationType = 'DEPOSIT'): array
    {
        $path = '/v2/active-conf' . (($country || $operationType)
            ? '?' . http_build_query(array_filter([
                'country' => $country,
                'operationType' => $operationType,
            ]))
            : '');

        return $this->request('GET', $path, null, null);
    }

    /**
     * Valide et prédit le provider mobile money d'un numéro (MSISDN).
     */
    public function predictProvider(string $phoneNumber): array
    {
        return $this->request('POST', '/v2/predict-provider', [
            'phoneNumber' => $this->normalizePhoneNumber($phoneNumber),
        ], null);
    }

    /**
     * Envoie une requête à l'API PawaPay et normalise la réponse.
     */
    protected function request(string $method, string $path, ?array $payload, ?string $paymentId): array
    {
        $url = $this->baseUrl . $path;
        $body = $payload ? json_encode($payload) : null;

        $headers = $this->getHeaders();

        if ($body && $this->signRequests) {
            $signedHeaders = $this->buildSignedHeaders($method, $url, $body, $path);
            $headers = array_merge($headers, $signedHeaders);
        }

        try {
            $http = Http::withHeaders($headers)->timeout(30);

            $response = $body !== null
                ? $http->send($method, $url, ['body' => $body])
                : $http->send($method, $url);

            $result = $response->json() ?? [];

            Log::info('PawaPay: Requête', [
                'method' => $method,
                'path' => $path,
                'payment_id' => $paymentId,
                'status' => $response->status(),
                'response' => $result,
            ]);

            if ($response->successful()) {
                return $this->normalizeSuccess($method, $path, $result, $paymentId);
            }

            return $this->normalizeFailure($method, $path, $result, $response->status(), $paymentId);
        } catch (\Exception $e) {
            Log::error('PawaPay: Exception', [
                'method' => $method,
                'path' => $path,
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'payment_id' => $paymentId,
                'status' => 'error',
                'message' => 'Erreur de connexion au service PawaPay',
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function normalizeSuccess(string $method, string $path, array $result, ?string $paymentId): array
    {
        // Endpoints de statut : { status: FOUND|NOT_FOUND, data: {...} }
        if (isset($result['status']) && in_array($result['status'], ['FOUND', 'NOT_FOUND'], true)) {
            if ($result['status'] === 'NOT_FOUND') {
                return [
                    'success' => true,
                    'payment_id' => $paymentId,
                    'status' => 'NOT_FOUND',
                    'message' => 'Paiement introuvable chez PawaPay',
                    'data' => null,
                ];
            }

            $data = $result['data'] ?? [];

            return [
                'success' => true,
                'payment_id' => $data['depositId'] ?? $data['payoutId'] ?? $paymentId,
                'status' => $data['status'] ?? 'UNKNOWN',
                'status_found' => true,
                'message' => $data['failureReason']['failureMessage'] ?? null,
                'failure_code' => $data['failureReason']['failureCode'] ?? null,
                'data' => $data,
            ];
        }

        // Réponse d'initiation : { depositId/payoutId, status: ACCEPTED|REJECTED|DUPLICATE_IGNORED }
        $status = $result['status'] ?? 'UNKNOWN';
        $isInitiation = in_array($status, ['ACCEPTED', 'REJECTED', 'DUPLICATE_IGNORED'], true);

        if ($isInitiation && $status === 'ACCEPTED') {
            return [
                'success' => true,
                'payment_id' => $result['depositId'] ?? $result['payoutId'] ?? $paymentId,
                'status' => 'ACCEPTED',
                'message' => 'Paiement accepté par PawaPay',
                'data' => $result,
            ];
        }

        if ($isInitiation && $status === 'DUPLICATE_IGNORED') {
            return [
                'success' => true,
                'payment_id' => $result['depositId'] ?? $result['payoutId'] ?? $paymentId,
                'status' => 'DUPLICATE_IGNORED',
                'message' => 'Paiement déjà traité (doublon ignoré)',
                'data' => $result,
            ];
        }

        // Endpoints toolkit (predict-provider, active-conf, public-keys...)
        if (str_contains($path, 'predict-provider')) {
            return [
                'success' => true,
                'status' => 'SUCCESS',
                'message' => 'Provider prédit',
                'data' => $result,
            ];
        }

        return [
            'success' => true,
            'status' => $status,
            'message' => 'Requête traitée',
            'data' => $result,
        ];
    }

    protected function normalizeFailure(string $method, string $path, array $result, int $httpStatus, ?string $paymentId): array
    {
        $failureCode = $result['failureReason']['failureCode']
            ?? $result['error']
            ?? 'HTTP_' . $httpStatus;
        $failureMessage = $result['failureReason']['failureMessage']
            ?? $result['message']
            ?? 'Erreur PawaPay (' . $httpStatus . ')';

        return [
            'success' => false,
            'payment_id' => $result['depositId'] ?? $result['payoutId'] ?? $paymentId,
            'status' => 'REJECTED',
            'message' => $failureMessage,
            'failure_code' => $failureCode,
            'data' => $result,
            'http_status' => $httpStatus,
        ];
    }

    /**
     * Mappe un statut PawaPay vers le statut interne de l'application
     * (pending/completed/failed).
     */
    public function mapStatus(?string $status): string
    {
        return match (strtoupper($status ?? '')) {
            'COMPLETED' => 'completed',
            'FAILED', 'REJECTED' => 'failed',
            'ACCEPTED', 'ENQUEUED', 'PROCESSING', 'IN_RECONCILIATION', 'PENDING' => 'pending',
            'DUPLICATE_IGNORED' => 'completed',
            default => 'pending',
        };
    }

    public function isFinalStatus(?string $status): bool
    {
        return in_array(strtoupper($status ?? ''), ['COMPLETED', 'FAILED'], true);
    }

    // --- Helpers arithmétiques big integers ---

    protected function bytesToInt(string $bytes): string
    {
        return gmp_import($bytes ?: "\0", 1, GMP_BIG_ENDIAN);
    }

    protected function intToBytes(string $int, int $length): string
    {
        $bytes = gmp_export($int, 1, GMP_BIG_ENDIAN);
        if (strlen($bytes) < $length) {
            return str_pad($bytes, $length, "\0", STR_PAD_LEFT);
        }

        return substr($bytes, -$length);
    }

    protected function modpow(string $base, string $exp, string $mod): string
    {
        return gmp_powm($base, $exp, $mod);
    }

    protected function mgf1(string $seed, int $length, string $hashName): string
    {
        $output = '';
        $counter = 0;

        while (strlen($output) < $length) {
            $output .= hash($hashName, $seed . pack('N', $counter), true);
            $counter++;
        }

        return substr($output, 0, $length);
    }
}
