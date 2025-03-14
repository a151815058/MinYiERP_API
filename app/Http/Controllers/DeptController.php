<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dept;
use Illuminate\Support\Str;

class DeptController extends Controller
{
    // 儲存部門資料
    public function store(Request $request)
    {
        // 驗證請求
        $validated = $request->validate([
            'DeptNo'     => 'required|string|max:255|unique:depts,DeptNo',
            'DeptNM'     => 'required|string|max:255',
            'Note'       => 'nullable|string|max:255',
            'IsVaild'    => 'required|boolean',
            'Createuser' => 'required|string|max:255',
            'UpdateUser' => 'required|string|max:255',
        ]);

        // 建立部門資料
        $dept = Dept::create([
            'uuid'       => Str::uuid(),  // 自動生成 UUID
            'DeptNo'     => $validated['DeptNo'],
            'DeptNM'     => $validated['DeptNM'],
            'Note'       => $validated['Note'] ?? null,
            'IsVaild'    => $validated['IsVaild'],
            'Createuser' => $validated['Createuser'],
            'UpdateUser' => $validated['UpdateUser'],
            'CreateTime' => now(),  // 設定當前時間
            'UpdateTime' => now(),
        ]);

        // 回應 JSON
        return response()->json([
            'message' => '部門建立成功',
            'Dept'    => $dept
        ], 201);
    }

    // 🔍 查詢單一部門
    public function show($deptNo)
    {
        $dept = Dept::findByDeptNo($deptNo);
        
        if (!$dept) {
            return response()->json(['message' => '部門未找到'], 404);
        }

        return response()->json($dept);
    }

    // 🔍 查詢所有有效部門
    public function getValidDepts()
    {
        return response()->json(Dept::getValidDepts());
    }
}


