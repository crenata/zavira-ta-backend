<?php

namespace App\Http\Middleware;

use App\Constants\AdminTypeConstant;
use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next) {
        $path = str_replace("api/v1/admin/", "", $request->path());
        if ((int) auth()->user()->type === AdminTypeConstant::ADMINISTRATOR) return $next($request);
        if ((int) auth()->user()->type === AdminTypeConstant::CHIEF && $this->canChief($path)) return $next($request);
        if ((int) auth()->user()->type === AdminTypeConstant::STAFF && $this->canStaff($path)) return $next($request);
        if ((int) auth()->user()->type === AdminTypeConstant::MANAGER && $this->canManager($path)) return $next($request);
        return ResponseHelper::response(null, "Unauthenticated", 401);
    }

    public function canChief(string $path) {
        return match (true) {
            str_contains($path, "auth/get"),
            str_contains($path, "auth/register"),
            str_contains($path, "auth/edit"),
            str_contains($path, "auth/delete"),
            str_contains($path, "transaction/get/manager"),
            str_contains($path, "transaction/check"),
            str_contains($path, "complaint/get"),
            str_contains($path, "complaint/delete")
            => false,
            default => true
        };
    }

    public function canStaff(string $path) {
        return $this->canChief($path);
    }

    public function canManager(string $path) {
        return match (true) {
            str_contains($path, "auth/self"),
            str_contains($path, "auth/logout"),
            str_contains($path, "transaction/get/manager"),
            str_contains($path, "transaction/check")
            => true,
            default => false
        };
    }
}
