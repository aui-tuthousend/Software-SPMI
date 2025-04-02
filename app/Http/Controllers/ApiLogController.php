<?php

namespace App\Http\Controllers;

use App\Models\ApiLog;
use Illuminate\Http\Request;

class ApiLogController extends Controller {
    public function index(Request $request) {
        if (!auth()->user()->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $logs = ApiLog::query()
            ->when($request->user_id, fn($q, $id) => $q->where('user_id', $id))
            ->when($request->method, fn($q, $method) => $q->where('method', $method))
            ->when($request->status_code, fn($q, $code) => $q->where('status_code', $code))
            ->with('user')
            ->latest()
            ->paginate(20);

        return response()->json($logs);
    }

    public function show(ApiLog $apiLog) {
        return response()->json($apiLog);
    }
}
