<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelebirrService
{
    public function generateAESKey(): string
    {
        return bin2hex(random_bytes(16));
    }

    public function encryptWithAES(string $data, string $key): string
    {
        $iv = substr($key, 0, 16);
        return base64_encode(openssl_encrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv));
    }

    public function encryptWithRSA(string $aesKey): string
{
    $keyPath = storage_path('telebirr_public.pem');
    $keyContent = file_get_contents($keyPath);

     Log::info('Telebirr public key content:', [$keyContent]);
    if (!$keyContent) {
        throw new \Exception("Telebirr public key not found at $keyPath");
    }

    $publicKey = openssl_pkey_get_public($keyContent);

    if (!$publicKey) {
        throw new \Exception("Invalid public key format");
    }

    openssl_public_encrypt($aesKey, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING);
    return base64_encode($encrypted);
}


    public function buildPayload(array $data): array
    {
        $aesKey = $this->generateAESKey();
        $bizContent = json_encode($data, JSON_UNESCAPED_UNICODE);
        $encryptedBizContent = $this->encryptWithAES($bizContent, $aesKey);
        $encryptedAESKey = $this->encryptWithRSA($aesKey);

        return [
            'appId' => env('TELEBIRR_APP_ID'),
            'bizContent' => $encryptedBizContent,
            'sign' => $encryptedAESKey,
            'signType' => 'RSA',
            'charset' => 'UTF-8',
            'version' => '1.0',
        ];
    }

    public function sendToGateway(array $payload): array
    {
        $response = Http::post('https://app.telebirr.com/service/openapipayment', $payload);
        return $response->json();
    }
}
