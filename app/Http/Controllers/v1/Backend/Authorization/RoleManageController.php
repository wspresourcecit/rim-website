<?php

namespace App\Http\Controllers\v1\Backend\Authorization;

use Exception;
use Illuminate\Http\Request;
use App\Models\Authorization\Role;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Services\v1\Backend\Authorization\RoleManageService;
use App\Http\Requests\v1\Backend\Authorization\RoleManageRequest;
use App\Http\Resources\v1\Backend\Authorization\RoleManageResource;
use App\Http\Resources\v1\Backend\Authorization\PermissionGroupResource;

class RoleManageController extends Controller
{
    protected RoleManageService $service;

    public function __construct(RoleManageService $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection | JsonResponse
    {

        $roles = $this->service->getRoles($request->all());
        return RoleManageResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleManageRequest $request): JsonResponse
    {

        $role = $this->service->storeRole($request->validated(), auth('api')->user()->id);
        return successResponse(new RoleManageResource($role), "Successfully stored.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): JsonResponse
    {

        $role = $this->service->getRole($role);
        return successResponse(new RoleManageResource($role));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleManageRequest $request, Role $role): JsonResponse
    {

        $role = $this->service->updateRole($request->validated(), $role, auth('api')->user()->id);
        return successResponse(new RoleManageResource($role), "Successfully updated.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): JsonResponse
    {

        $this->service->deleteRole($role);
        return successResponse(new RoleManageResource($role), "Successfully deleted.");
    }

    /**
     * Get permission group wise permissions
     */
    public function groupWisePermissions(): JsonResponse
    {

        $permissions = $this->service->getPermissions();
        return successResponse(PermissionGroupResource::collection($permissions));
    }

    function activeRoles(): ResourceCollection | JsonResponse
    {

        $roles = $this->service->getActiveRoles();
        return RoleManageResource::collection($roles);
    }
}
