<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait TracksUserActions
{
   protected static function getUserId(): ?int
   {
      return auth()->check() ? auth()->id() : null;
   }

   public static function bootTracksUserActions(): void
   {
      // Handle created_by
      static::creating(function ($model) {
         if ($userId = static::getUserId()) {
            $model->created_by = $userId;
            $model->updated_by = $userId;
         }
      });

      // Handle updated_by
      static::updating(function ($model) {
         if ($userId = static::getUserId()) {
            $model->updated_by = $userId;
         }
      });
   }

   public function createdBy(): BelongsTo
   {
      return $this->belongsTo(User::class, 'created_by');
   }

   public function updatedBy(): BelongsTo
   {
      return $this->belongsTo(User::class, 'updated_by');
   }
}
