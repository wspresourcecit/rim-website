<?php

namespace App\Services\v1\Backend\Setting;

use App\Services\MediaService;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting\Extension;

class ExtensionService
{
   const ERROR_MESSAGE = "Something went wrong!";

   public function getExtensions(array $data): LengthAwarePaginator
   {
      try {
         return Extension::select('id', 'name', 'short_code', 'is_api', 'is_active', 'created_at')
            ->orderBy('id', 'desc')
            ->DataFilter($data)
            ->paginate($data['paginate'] ?? config('app.paginate'));
      } catch (Exception $e) {
         throw new Exception(self::ERROR_MESSAGE, 500);
      }
   }

   public function getActiveExtensions(): Collection
   {
      try {
         return Extension::with('media')
            ->where('is_active', true)
            ->select('id', 'short_code', 'credentials', 'is_api', 'is_active')
            ->orderBy('id', 'desc')
            ->get();
      } catch (Exception $e) {
         throw new Exception(self::ERROR_MESSAGE, 500);
      }
   }

   public function updateExtension(array $data, Extension $extension): Extension
   {
      try {
         if (isset($data['image'])) {
            if ($extension->image) {
               deleteImage($extension->image);
            }
            $data['image'] = uploadBase64Image($data['image'], 'images/extensions', 'extension_');
         } else {
            $data['image'] = $extension->image;
         }

         $extension->update($data);

         return $extension;
      } catch (Exception $e) {
         throw new Exception(self::ERROR_MESSAGE, 500);
      }
   }


   public function getCredentials(string $shortCode): array
   {
      try {
         $cacheKey = "extension_credentials_{$shortCode}";

         return Cache::rememberForever($cacheKey, function () use ($shortCode) {
            $credentials = Extension::where('short_code', $shortCode)
               ->where('is_active', true)
               ->value('credentials');

            if (empty($credentials)) {
               return [];
            }

            return collect($credentials)->pluck('value', 'key')->toArray();
         });
      } catch (Exception $e) {
         throw new Exception(self::ERROR_MESSAGE, 500);
      }
   }
}
