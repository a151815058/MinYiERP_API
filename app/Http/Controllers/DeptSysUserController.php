<?php

namespace App\Http\Controllers;
use App\Models\Dept;
use App\Models\SysUser;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class DeptSysUserController extends Controller
{
     /**
     * 新增部門與使用者關聯 (包含 'IsValid','Createuser', 'CreateTime','UpdateUser', 'UpdateTime')
     */
    /**
     * @OA\POST(
     *     path="/api/assign-userdept",
     *     summary="新增人員部門關聯",
     *     description="新增人員部門關聯",
     *     operationId="assign-userdept",
     *     tags={"Base_AssignUserDept"},
     *     @OA\Parameter(
     *         name="UsrNo",
     *         in="query",
     *         required=true,
     *         description="人員代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="DeptNo",
     *         in="query",
     *         required=true,
     *         description="部門代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Note",
     *         in="query",
     *         required=false,
     *         description="備註",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="IsValid",
     *         in="query",
     *         required=true,
     *         description="是否有效",
     *         @OA\Schema(type="string", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="UsrNo", type="string", example="U001"),
     *             @OA\Property(property="DeptNo", type="string", example="A001"),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="客戶端請求錯誤"
     *     )
     * )
     */
    // 驗證請求
    public function store(Request $request)
    {
        $validated = $request->validate([
            'DeptNo'   => 'required|exists:depts,DeptNo',
            'UsrNo'   => 'required|exists:sysusers,UsrNo',
            'IsValid'    => 'required|boolean'
        ]);

        // 取得使用者與部門ID
        $user = SysUser::where('UsrNo', $validated['UsrNo'])->first(); // 使用 `first()` 獲取模型
        $dept = Dept::where('DeptNo', $validated['DeptNo'])->first(); // 使用 `first()` 獲取模型

        if (!$dept || !$user) {
            return response()->json([
                'status' => false,
                'message' => '使用者或部門不存在',
                'User'    =>  $user->UsrNM,
                'Dept'    =>  $dept->DeptNM
            ], status: 400);
        }

        // 新增關聯
        $dept->sysusers()->attach($user->uuid, [
            'IsValid'    => $validated['IsValid']
        ]);


        return response()->json([
                'status' => true,
                'message' => 'success',
                'user' => $user->UsrNO,
                'dept' => $dept->DeptNO,
            ], 201);
    }

    /**
     * @OA\GET(
     *     path="/api/dept-users/{deptId}",
     *     summary="讀取部門成員",
     *     description="讀取部門成員",
     *     operationId="dept-users",
     *     tags={"Base_AssignUserDept"},
     *     @OA\Parameter(
     *         name="deptId",
     *         in="path",
     *         required=true,
     *         description="部門代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="dept", type="string", example="財務部"),
     *             @OA\Property(property="userId", type="string", example="cd7edb27-a1e2-4df1-aa1a-2f0346935cb2"),
     *             @OA\Property(property="userNo", type="string", example="U001"),
     *             @OA\Property(property="username", type="string", example="姚佩彤"),
     *             @OA\Property(property="IsValid", type="boolean", example=true),   
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", format="date-time", example="2023-10-01T12:00:00Z"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="UpdateTime", type="string", format="date-time", example="2023-10-01T12:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="部門未找到人員"
     *     )
     * )
     */
    // 讀取某個部門的所有使用者
    public function getUsersByDept($deptNo)
    {
        $dept = Dept::with('sysusers')->where('DeptNo', $deptNo)->first();

        if (!$dept) {
            return response()->json([
                'status' => false,
                'message' => '未找到部門',
                'dept'    => null,
                'users'   => null
            ], 404);
        }else{
            return response()->json([
                'status' => true,
                'message' => 'success',
                'dept' => $dept->DeptNM,
                'output' => $dept->sysusers->map(function ($user) {
                    return [
                        'id' => $user->uuid,
                        'userNo' => $user->UsrNo,
                        'username' => $user->UsrNM,
                        'IsValid' => $user->IsValid,
                        'Createuser' => $user->pivot->Createuser,
                        'CreateTime' => $user->pivot->CreateTime,
                        'UpdateUser' => $user->pivot->UpdateUser,
                        'UpdateTime' => $user->pivot->UpdateTime
                    ];
                }),
            ]);
        }
    }


    /**
     * @OA\GET(
     *     path="/api/user-depts/{userId}",
     *     summary="讀取使用者部門",
     *     description="讀取使用者部門",
     *     operationId="user-depts",
     *     tags={"Base_AssignUserDept"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="人員代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="user", type="string", example="姚佩彤"),
     *             @OA\Property(property="Deptid", type="string", example="cd7edb27-a1e2-4df1-aa1a-2f0346935cb2"),
     *             @OA\Property(property="DeptNo", type="string", example="A002"),
     *             @OA\Property(property="DeptNM", type="string", example="財務部"),
     *             @OA\Property(property="IsValid", type="boolean", example=true),   
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", format="date-time", example="2023-10-01T12:00:00Z"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="UpdateTime", type="string", format="date-time", example="2023-10-01T12:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="人員未找到部門"
     *     )
     * )
     */
    //讀取某個使用者所屬的部門
    public function getDeptsByUser($userNo)
    {
        $user = SysUser::with('depts')->where('UsrNo', $userNo)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => '使用者不存在',
                'user' => $user
            ], 404);
        }

        return response()->json([         
            'status' => true,
            'message' => 'success',
            'user' => $user->username,
            'output' => $user->depts->map(function ($dept) {
                return [
                    'Deptid' => $dept->id,
                    'DeptNo' => $dept->DeptNo,
                    'DeptNM' => $dept->DeptNM,
                    'IsValid' => $dept->pivot->IsValid,
                    'Createuser' => $dept->pivot->Createuser,
                    'CreateTime' => $dept->pivot->CreateTime,
                    'UpdateUser' => $dept->pivot->UpdateUser,
                    'UpdateTime' => $dept->pivot->UpdateTime
                ];
            }),
        ]);

    }
}
