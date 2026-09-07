<?php

namespace App\Services\v1\Backend;

use App\Models\PartnerCompany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PartnerCompanyService
{
    private const UPLOAD_DIR = 'images/cms/partner-company';

    public function getPartnerCompanies(array $data): LengthAwarePaginator
    {
        return PartnerCompany::with(['createdBy:id,name,email,image', 'updatedBy:id,name,email,image'])
            ->DataFilter($data)
            ->orderBy('id', 'desc')
            ->select('id', 'logo', 'type', 'is_active', 'created_by', 'updated_by', 'created_at')
            ->paginate($data['paginate'] ?? config('app.paginate'));
    }

    /**
     * Store one partner company per uploaded logo (max 12), all sharing the selected type.
     *
     * @return Collection<int, PartnerCompany>
     */
    public function storePartnerCompanies(array $data)
    {
        return DB::transaction(function () use ($data) {

            return collect($data['logos'])->map(
                fn ($logo) => $this->createPartnerCompany($logo, $data)
            );
        });
    }

    private function createPartnerCompany(string $logo, array $data): PartnerCompany
    {
        $partnerCompany = PartnerCompany::create([
            'logo' => uploadBase64Image(
                $logo,
                self::UPLOAD_DIR,
                'partner'
            ),
            'type' => $data['type'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        return $this->loadData($partnerCompany);
    }

    public function getPartnerCompany(PartnerCompany $partnerCompany): PartnerCompany
    {
        return $this->loadData($partnerCompany);
    }

    public function updatePartnerCompany(array $data, PartnerCompany $partnerCompany): PartnerCompany
    {
        if (! empty($data['logo'])) {
            $newLogo = uploadBase64Image($data['logo'], null, null, 'partner', self::UPLOAD_DIR);
            deleteImage($partnerCompany->logo);
            $data['logo'] = $newLogo;
        } else {
            unset($data['logo']);
        }

        $partnerCompany->update($data);

        return $this->loadData($partnerCompany);
    }

    public function deletePartnerCompany(PartnerCompany $partnerCompany): bool
    {
        deleteImage($partnerCompany->logo);

        return $partnerCompany->delete();
    }

    public function activePartnerCompanies($type): Collection
    {
        return PartnerCompany::cacheRows(
            "active:{$type}",
            [PartnerCompany::class.':list'],
            fn () => PartnerCompany::where('is_active', true)
                ->where('type', $type)
                ->select('id', 'logo', 'type', 'is_active')
                ->orderBy('id', 'desc')
                ->get(),
        );
    }

    private function loadData(PartnerCompany $partnerCompany): PartnerCompany
    {
        return $partnerCompany->load(['createdBy:id,name,email,image', 'updatedBy:id,name,email,image']);
    }
}
