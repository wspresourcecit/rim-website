<?php

namespace App\Services\v1\Backend;

use App\Models\PrivacyPolicy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PrivacyPolicyService
{
    public function getPrivacyPolicies(array $data): LengthAwarePaginator
    {
        return PrivacyPolicy::with(['createdBy:id,name,email,image', 'updatedBy:id,name,email,image'])
            ->DataFilter($data)
            ->select('id', 'title', 'description', 'is_active', 'created_at', 'updated_by', 'created_by')
            ->orderBy('id', 'asc')
            ->paginate($data['paginate'] ?? config('app.paginate'));
    }

    public function storePrivacyPolicy(array $data): PrivacyPolicy
    {
        $privacyPolicy = PrivacyPolicy::create($data);
        return $this->loadData($privacyPolicy);
    }

    public function getPrivacyPolicy(PrivacyPolicy $privacyPolicy): PrivacyPolicy
    {
        return $this->loadData($privacyPolicy);
    }

    public function updatePrivacyPolicy(array $data, PrivacyPolicy $privacyPolicy): PrivacyPolicy
    {
        $privacyPolicy->update($data);
        return $this->loadData($privacyPolicy);
    }

    public function deletePrivacyPolicy(PrivacyPolicy $privacyPolicy): bool
    {
        return $privacyPolicy->delete();
    }

    private function loadData(PrivacyPolicy $privacyPolicy): PrivacyPolicy
    {
        return $privacyPolicy->load(['createdBy:id,name,email,image', 'updatedBy:id,name,email,image']);
    }
}
