<?php

namespace App\Services\v1\Backend;

use Exception;
use Illuminate\Support\Collection;
use App\Models\Authorization\Permission;

class AuthService
{
   const ERROR_CREDENTIAL_DID_NOT_MATCH = "Credentials did not match!";
   const ERROR_SOMETHING_WAS_WRONG = "Something was wrong!";

   public function loginWithEmail(array $data): array
   {
      if (auth()->attempt(['email' => $data['email'], 'password' => $data['password'], 'is_active' => true])) {
         $admin = auth()->user();
         return [
            'data' => $this->setCredential($admin),
            'message' => "Successfully logged in"
         ];
      }
      throw new Exception(self::ERROR_CREDENTIAL_DID_NOT_MATCH, 404);
   }

   public function logout(): string
   {
      try {
         auth('api')->user()->token()->revoke();
         return "Successfully logged out.";
      } catch (Exception $e) {
         throw new Exception(self::ERROR_SOMETHING_WAS_WRONG, 500);
      }
   }

   private function setCredential(object $admin): array
   {
      $data = [];
      $data['role'] = $admin->role->name;
      $data['name'] = $admin->name;
      $data['email'] = $admin->email;
      $data['image'] = $admin->image ? asset($admin->image) : asset('seeder-images/S-Admin.png');
      $data['token'] = $admin->createToken('tokenName', ['admin'])->accessToken;
      return $data;
   }

   public function check(): bool
   {
      try {
         if (auth('api')->check()) {
            return true;
         }
         return false;
      } catch (Exception $e) {
         throw new Exception(self::ERROR_SOMETHING_WAS_WRONG, 500);
      }
   }

   function permissions(): Collection
   {
      try {
         return Permission::whereHas('roles', function ($q) {
            $q->where('permission_role.role_id', auth('api')->user()->role_id);
         })->select('slug')->get();
      } catch (Exception $e) {
         throw new Exception(self::ERROR_SOMETHING_WAS_WRONG, 500);
      }
   }
}
