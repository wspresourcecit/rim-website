<?php

namespace App\Http\Controllers\v1\Backend;

use App\Services\v1\Backend\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\v1\Backend\AuthRequest;

class AuthController
{
    protected AuthService $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    function login(AuthRequest $request): JsonResponse
    {
        try {
            $data = $this->service->loginWithEmail($request->validated());
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $message = $this->service->logout();
            return response()->json([
                'message' => $message
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function check(): JsonResponse
    {
        try {
            $status = $this->service->check();
            return response()->json([
                'status' => $status
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function permissions(): JsonResponse
    {
        try {
            $permissions = $this->service->permissions();
            return response()->json([
                'permissions' => $permissions
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
