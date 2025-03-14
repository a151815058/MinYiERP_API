<?php

namespace App\Http\Controllers;
use App\Models\Dept;
use App\Models\SysUser;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;

class DeptSysUserController extends Controller
{
     /**
     * 新增部門與使用者關聯 (包含 'IsVaild','Createuser', 'CreateTime','UpdateUser', 'UpdateTime')
     */
    // 驗證請求
    public function store(Request $request)
    {
        $validated = $request->validate([
            'DeptNo'   => 'required|exists:depts,DeptNo',
            'UsrNo'   => 'required|exists:sysusers,UsrNo',
            'IsVaild'    => 'required|boolean',
            'Createuser' => 'required|string|max:255',
            'UpdateUser' => 'required|string|max:255',
        ]);

        // 取得使用者與部門ID
        $user = SysUser::where('UsrNo', $validated['UsrNo'])->first(); // 使用 `first()` 獲取模型
        $dept = Dept::where('DeptNo', $validated['DeptNo'])->first(); // 使用 `first()` 獲取模型

        if (!$dept || !$user) {
            return response()->json(['error' => '使用者或部門不存在'], 404);
        }

        // 新增關聯
        $dept->sysusers()->attach($user->uuid, [
            'IsVaild'    => $validated['IsVaild'],
            'Createuser' => $validated['Createuser'],
            'UpdateUser' => $validated['UpdateUser'],
            'CreateTime' => now(),  // 設定當前時間
            'UpdateTime' => now()  // 設定當前時間
        ]);


        return response()->json(['message' => '使用者成功加入部門', 'user' => $user->UsrNM, 'dept' => $dept->DeptNM], 201);
    }

    // 讀取某個部門的所有使用者
    public function getUsersByDept($deptNo)
    {
        $dept = Dept::with('sysusers')->where('DeptNo', $deptNo)->first();

        if (!$dept) {
            return response()->json(['error' => '部門不存在'], 404);
        }

        return response()->json([
            'dept' => $dept->DeptNM,
            'users' => $dept->sysusers->map(function ($user) {
                return [
                    'id' => $user->id,
                    'username' => $user->username,
                    'IsVaild' => $user->pivot->IsVaild,
                    'Createuser' => $user->pivot->Createuser,
                    'CreateTime' => $user->pivot->CreateTime,
                    'UpdateUser' => $user->pivot->UpdateUser,
                    'UpdateTime' => $user->pivot->UpdateTime
                ];
            }),
        ]);
    }

    //讀取某個使用者所屬的部門
    public function getDeptsByUser($userNo)
    {
        $user = SysUser::with('depts')->where('UsrNo', $userNo)->first();

        if (!$user) {
            return response()->json(['error' => '使用者不存在'], 404);
        }

        return response()->json([
            'user' => $user->username,
            'departments' => $user->depts->map(function ($dept) {
                return [
                    'id' => $dept->id,
                    'DeptNM' => $dept->DeptNM,
                    'IsVaild' => $dept->pivot->IsVaild,
                    'Createuser' => $dept->pivot->Createuser,
                    'CreateTime' => $dept->pivot->CreateTime,
                    'UpdateUser' => $dept->pivot->UpdateUser,
                    'UpdateTime' => $dept->pivot->UpdateTime
                ];
            }),
        ]);

    }
}
