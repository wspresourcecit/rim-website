<?php

namespace App\Services\v1\Backend\Setting;

use App\Models\Setting\Extension;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class ExtensionAPIService
{
   const ERROR_MESSAGE = "Something was wrong!";

   public function verifyRecaptcha(array $data): void
   {
      $recaptcha = Extension::where('short_code', 'recaptcha')
         ->where('is_active', true)
         ->select('credentials')
         ->first()
         ?->credentials;

      if (!$recaptcha) {
         return;
      }

      $secret = $recaptcha[1]['value'] ?? null;

      if (blank($secret)) {
         throw new Exception('reCAPTCHA secret key is not configured.');
      }

      $token = $data['recaptcha_token'] ?? null;

      if (blank($token)) {
         throw ValidationException::withMessages([
               'recaptcha_token' => [
                  'The captcha verification failed. Please try again.',
               ],
         ]);
      }

      $response = Http::asForm()
         ->timeout(10)
         ->post(
               'https://www.google.com/recaptcha/api/siteverify',
               [
                  'secret'   => $secret,
                  'response' => $token,
                  'remoteip' => request()->ip(),
               ]
         );

      if (!$response->successful()) {
         throw new Exception('Unable to verify reCAPTCHA.');
      }

      $result = $response->json();

      if (!($result['success'] ?? false)) {
         throw ValidationException::withMessages([
               'recaptcha_token' => [
                  'The captcha verification failed. Please try again.',
               ],
         ]);
      }

      unset($data['recaptcha_token']);
   }
}
