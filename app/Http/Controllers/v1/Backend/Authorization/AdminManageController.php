<?php

namespace App\Http\Controllers\v1\Backend\Authorization;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Backend\Authorization\AdminRequest;
use App\Http\Resources\v1\Backend\Authorization\AdminResource;
use App\Models\User;
use App\Services\v1\Backend\Admin\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AdminManageController extends Controller
{
    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection | JsonResponse
    {
        $admins = $this->adminService->getAdmins($request->all());
        return AdminResource::collection($admins);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $request): JsonResponse
    {
        $admin = $this->adminService->storeAdmin($request->validated());
        return successResponse(new AdminResource($admin), 'Admin created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $admin): JsonResponse
    {
        $admin = $this->adminService->getAdmin($admin);
        return successResponse(new AdminResource($admin), 'Admin retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminRequest $request, User $admin): JsonResponse
    {
        $admin = $this->adminService->updateAdmin($request->validated(), $admin);
        return successResponse(new AdminResource($admin), 'Admin updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $admin): JsonResponse
    {
        $this->adminService->destroyAdmin($admin);
        return successResponse(new AdminResource($admin), 'Admin deleted successfully');
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'old_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'same:password'],
        ]);

        $this->adminService->updatePassword(['old_password' => $request->old_password, 'password' => $request->password]);
        return successResponse([], 'Admin password changed successfully');
    }
}
