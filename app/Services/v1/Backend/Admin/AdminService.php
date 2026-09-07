<?php

namespace App\Services\v1\Backend\Admin;

use App\Exceptions\ApiException;
use App\Models\User;
use App\Services\MediaService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdminService
{
    public function getAdmins(array $data): LengthAwarePaginator
    {
        return User::with(['role:id,name', 'createdBy:id,name', 'media'])
            ->where('id', '!=', 1)
            ->DataFilter($data)
            ->select('id',  'role_id', 'created_by', 'name', 'email', 'deleteable', 'created_at', 'is_active')
            ->paginate($data['paginate'] ?? config('app.paginate'));
    }

    public function storeAdmin(array $data): User
    {

        $password = Hash::make($data['password']);
        $data['created_by'] = auth('api')->user()->id;
        $data['password'] = $password;
        $user = User::create($data);
        $this->handleMedia($data['image'] ?? null, 'image', $user, $data['image_width'] ?? 300, $data['image_height'] ?? 300);
        return $user->load(['role:id,name', 'createdBy:id,name', 'media']);
    }

    public function getAdmin(User $user): User
    {
        return $user->load(['role:id,name', 'media']);
    }

    public function updateAdmin(array $data, User $user): User
    {
        $data['created_by'] = auth('api')->user()->id;
        $password = isset($data['password']) ? Hash::make($data['password']) : $user->password;
        $data['password'] = $password;
        $user->update($data);
        $this->handleMedia($data['image'] ?? null, 'image', $user, $data['image_width'] ?? 300, $data['image_height'] ?? 300);
        logger()->info('AdminService updateAdmin data: ', $data);

        return $user->load(['role:id,name', 'createdBy:id,name', 'media']);
    }

    public function destroyAdmin(User $user): void
    {

        $user->delete();
    }

    public function handleMedia($file, $fileName, $model, $width, $height): void
    {
        if (empty($file)) {
            return;
        }

        $mediaService = app(MediaService::class);

        $path = uploadBase64Image($file, 'images/admin', $fileName, $width, $height);
        $media = $mediaService->createMediaRecord($path, basename($path), null, (int) $width, (int) $height, 'image');

        $mediaService->attachSingleToModel($model, $media, $fileName);
    }
    public function getUsers(): Collection
    {
        return User::where('is_active', true)
            ->select('id', 'name', 'email')
            ->get();
    }

    public function updatePassword(array $data): void
    {
        $user = auth('api')->user();
        if (!Hash::check($data['old_password'], $user->password)) {
            throw new ApiException('Old password is incorrect', 400);
        }
        $user->update([
            'password' => Hash::make($data['password'])
        ]);
    }
}
