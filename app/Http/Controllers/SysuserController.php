<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sysuser;
use Illuminate\Support\Str;

class SysuserController extends Controller
{
    // 儲存人員資料
    public function store(Request $request)
    {
        // 驗證請求
        $validated = $request->validate([
            'UsrNo'     => 'required|string|max:255|unique:sysusers,UsrNo',
            'UsrNM'     => 'required|string|max:255',
            'Note'       => 'nullable|string|max:255',
            'IsVaild'    => 'required|boolean',
            'Createuser' => 'required|string|max:255',
            'UpdateUser' => 'required|string|max:255',
        ]);

        // 建立部門資料
        $user = Sysuser::create([
            'uuid'       => Str::uuid(),  // 自動生成 UUID
            'UsrNo'     => $validated['UsrNo'],
            'UsrNM'     => $validated['UsrNM'],
            'Note'       => $validated['Note'] ?? null,
            'IsVaild'    => $validated['IsVaild'],
            'Createuser' => $validated['Createuser'],
            'UpdateUser' => $validated['UpdateUser'],
            'CreateTime' => now(),  // 設定當前時間
            'UpdateTime' => now(),
        ]);

        // 回應 JSON
        return response()->json([
            'message' => '人員建立成功',
            'User'    => $user
        ], 201);
    }

    // 🔍 查詢單一人員
    public function show($UsrNo)
    {
        $user = SysUser::where('UsrNo', $UsrNo)->first();
        
        if (!$user) {
            return response()->json(['message' => '人員未找到'], 404);
        }

        return response()->json($user);
    }

    // 🔍 查詢所有有效人員
    public function getValidusers()
    {
        return response()->json(SysUser::getValidusers());
    }
}
