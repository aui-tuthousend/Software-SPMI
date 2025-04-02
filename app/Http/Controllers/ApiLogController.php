<?php

namespace App\Http\Controllers;

use App\Models\ApiLog;
use Illuminate\Http\Request;

class ApiLogController extends Controller {
    public function index(Request $request) {
//        $user = auth()->user();
//         if (!$user->roles()->where('role', 'Admin')->exists()) {
//             return response()->json(['message' => 'Forbidden'], 403);
//         }
//        $logs = ApiLog::query()
//            ->when($request->user_id, fn($q, $id) => $q->where('user_id', $id))
//            ->when($request->method, fn($q, $method) => $q->where('method', $method))
//            ->when($request->status_code, fn($q, $code) => $q->where('status_code', $code))
//            ->with('user')
//            ->latest()
//            ->paginate(20);
//
//        return response()->json($logs);
    }

    public function show(ApiLog $apiLog) {
        return response()->json($apiLog);
    }

    public function getUserHistory(Request $request) {
        $user = $request->input('username');
        if (!$user) {
            return response()->json(['message' => 'User is required'], 400);
        }

        $logs = ApiLog::query()
            ->whereHas('user', fn($q) => $q->where('name', 'LIKE', $user))
            ->with('user')
            ->latest()
            ->paginate(20);

        return response()->json($logs);
    }
}
