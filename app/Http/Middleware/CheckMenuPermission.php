<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckMenuPermission
{
    public function handle(Request $request, Closure $next, $menuId)
    {
        $user = Auth::user(); // 假設登入後有 user

        if (!$user || !$user->role_id) {
            return response()->json(['message' => '未登入或無角色資訊'], 403);
        }

        $hasPermission = DB::table('role_menus')
            ->where('role_id', $user->role_id)
            ->where('menu_id', $menuId)
            ->where('is_valid', 1)
            ->exists();

        if (!$hasPermission) {
            return response()->json(['message' => '您沒有操作此功能的權限'], 403);
        }

        return $next($request);
    }
}