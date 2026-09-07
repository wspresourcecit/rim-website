<?php

namespace App\Services\v1\Backend\Setting;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected $clientId;
    protected $clientSecret;
    protected $refreshToken;
    protected $folderId;

    public function __construct()
    { 
        $extensionService = new ExtensionService();
        $credentials = $extensionService->getCredentials('google_drive');

        $this->clientId     = $credentials['client_id'] ?? null;
        $this->clientSecret = $credentials['client_secret'] ?? null;
        $this->refreshToken = $credentials['refresh_token'] ?? null;
        $this->folderId     = $credentials['folder_id'] ?? null;

    }

    protected function getAccessToken()
    {
        try {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $this->refreshToken,
                'grant_type'    => 'refresh_token',
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            Log::error('Google Auth Failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Google Auth Connection Error: ' . $e->getMessage());
            return null;
        }
    }

    public function uploadFile($filePath, $fileName)
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            Log::error('Google Drive Upload aborted: Could not get Access Token.');
            return false;
        }

        try {
            $metadata = [
                'name'     => $fileName,
                'parents'  => [$this->folderId]
            ];

            $response = Http::withToken($accessToken)
                ->attach('metadata', json_encode($metadata), 'metadata.json', [
                    'Content-Type' => 'application/json; charset=UTF-8'
                ])
                ->attach('file', file_get_contents($filePath), $fileName, [
                    'Content-Type' => 'application/octet-stream'
                ])
                ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&fields=id');

            if ($response->successful()) {
                return $response->json()['id'];
            }

            if (file_exists($filePath)) {
                unlink($filePath);
            }
            Log::error('Google Drive Upload Failed API Response: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('Google Drive HTTP Upload Error: ' . $e->getMessage());
            return false;
        }
    }
}