<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(Request $request, AttendanceService $service): JsonResponse
    {
        $data = $request->validate([
            'uid'        => 'required|string|max:64',
            'scanned_at' => 'nullable|date', // opsional; kalau kosong dipakai waktu server
        ]);

        $at = ! empty($data['scanned_at']) ? Carbon::parse($data['scanned_at']) : now();

        $result = $service->record($request->attributes->get('device'), $data['uid'], $at);

        return response()->json($result, $result['code'] === 'unregistered' ? 404 : 200);
    }

    public function ping(Request $request): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'device' => $request->attributes->get('device')->name,
            'server_time' => now()->toDateTimeString(),
        ]);
    }
}
