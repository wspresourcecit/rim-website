<?php

namespace App\Http\Controllers\v1\Backend\Setting;

use App\Http\Controllers\Controller;
use Exception;
use App\Http\Requests\v1\Backend\Setting\BackupRequest;
use App\Services\v1\Backend\Setting\BackupService;

class BackupController extends Controller
{
    protected $service;

    public function __construct(BackupService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        try {
            return successResponse($this->service->getBackups());
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function create()
    {
        try {
            $filename = $this->service->createLocalBackup();
            return successResponse($filename, 'Backup created successfully!', 201);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function download($fileName)
    {
        try {
            $file = $this->service->downloadLocalFile($fileName);
            return $file;
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($fileName)
    {
        try {
            $this->service->deleteLocalFile($fileName);
            return successResponse([], 'Backup file deleted successfully!');
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function couldBackup(BackupRequest $request)
    {
        try {
            $this->service->createCloudBackup($request->validated());
            return successResponse([]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), $e->getCode());
        }
    }
}