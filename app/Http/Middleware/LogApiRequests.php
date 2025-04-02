<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests {
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next) {
        // Start timer
        $startTime = microtime(true);

        // Process request
        $response = $next($request);

        // Calculate response time
        $responseTime = microtime(true) - $startTime;

        // Get authenticated user
        $user = auth()->user();

        // Log the request
        ApiLog::create([
            'user_id' => $user ? $user->id : null,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'request_headers' => $this->getHeaders($request),
            'request_body' => $this->getRequestData($request),
            'status_code' => $response->getStatusCode(),
            'response_body' => $this->getResponseData($response),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'response_time' => $responseTime * 1000, // convert to milliseconds
        ]);

        return $response;
    }


    protected function getHeaders(Request $request) {
        return collect($request->headers->all())
            ->map(function ($item) {
                return $item[0] ?? null;
            })
            ->toArray();
    }

    protected function getRequestData(Request $request) {
        $data = $request->all();

        // Mask sensitive data
        if (isset($data['password'])) {
            $data['password'] = '******';
        }
        if (isset($data['password_confirmation'])) {
            $data['password_confirmation'] = '******';
        }
        if (isset($data['token'])) {
            $data['token'] = '******';
        }

        return $data;
    }

    protected function getResponseData($response) {
        $content = $response->getContent();
        $json = json_decode($content, true);

        return json_last_error() === JSON_ERROR_NONE ? $json : $content;
    }
}
