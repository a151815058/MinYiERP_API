<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sysuser;
use App\Models\User;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;

class SysuserController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/createuser",
     *     summary="新增人員資訊",
     *     description="新增人員資訊",
     *     operationId="createuser",
     *     tags={"user"},
     *     @OA\Parameter(
     *         name="UsrNo",
     *         in="query",
     *         required=true,
     *         description="人員代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="UsrNM",
     *         in="query",
     *         required=true,
     *         description="人員名稱",
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
     *     @OA\Parameter(
     *         name="Createuser",
     *         in="query",
     *         required=true,
     *         description="建立者",
     *         @OA\Schema(type="string", example="admin")
     *     ),
     *     @OA\Parameter(
     *         name="UpdateUser",
     *         in="query",
     *         required=true,
     *         description="更新者",
     *         @OA\Schema(type="string", example="admin")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="UsrNo", type="string", example="U001"),
     *             @OA\Property(property="UsrNM", type="string", example="姚佩彤"),
     *             @OA\Property(property="Note", type="string", example=""),
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
    // 儲存人員資料
    public function store(Request $request)
    {
        // 驗證請求
        $validated = $request->validate([
            'UsrNo'     => 'required|string|max:255|unique:sysusers,UsrNo',
            'UsrNM'     => 'required|string|max:255',
            'Note'       => 'nullable|string|max:255',
            'IsValid'    => 'required|boolean',
            'Createuser' => 'required|string|max:255',
            'UpdateUser' => 'required|string|max:255',
        ]);

        // 建立部門資料
        $user = Sysuser::create([
            'uuid'       => Str::uuid(),  // 自動生成 UUID
            'UsrNo'     => $validated['UsrNo'],
            'UsrNM'     => $validated['UsrNM'],
            'Note'       => $validated['Note'] ?? null,
            'IsValid'    => $validated['IsValid'],
            'Createuser' => $validated['Createuser'],
            'UpdateUser' => $validated['UpdateUser'],
            'CreateTime' => now(),  // 設定當前時間
            'UpdateTime' => now(),
        ]);

        // 回應 JSON
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => '客戶端請求錯誤',
                'User'    => null
            ], status: 400);
        }else {
            // 回應 JSON
            return response()->json([
                'status' => true,
                'message' => 'success',
                'User'    => $user
            ], 201);
        }
    }
    /**
     * @OA\GET(
     *     path="/api/user/{UsrNo}",
     *     summary="查詢特定人員資訊",
     *     description="查詢特定人員資訊",
     *     operationId="getuser",
     *     tags={"user"},
     *     @OA\Parameter(
     *         name="UsrNo",
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
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="UsrNo", type="string", example="U001"),
     *             @OA\Property(property="UsrNM", type="string", example="姚佩彤"),
     *             @OA\Property(property="Note", type="string", example=""),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到人員"
     *     )
     * )
     */
    // 🔍 查詢單一人員
    public function show($UsrNo)
    {
        $user = SysUser::where('UsrNo', $UsrNo)->first();

        // 回應 JSON
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => '未找到人員',
                'User'    => null
            ], status: 404);
        }else {
            // 回應 JSON
            return response()->json([
                'status' => true,
                'message' => 'success',
                'User'    => $user
             ], 200);
        }
    }
    /**
     * @OA\GET(
     *     path="/api/users/valid",
     *     summary="查詢所有有效人員資訊",
     *     description="查詢所有有效人員資訊",
     *     operationId="GetAllUser",
     *     tags={"user"},
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="UsrNo", type="string", example="U001"),
     *             @OA\Property(property="UsrNM", type="string", example="姚佩彤"),
     *             @OA\Property(property="Note", type="string", example=""),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未有效找到人員"
     *     )
     * )
     */
    // 🔍 查詢所有有效人員
    public function getValidusers()
    {
        $user = SysUser::getValidusers();
        //return response()->json(SysUser::getValidusers());
        // 回應 JSON
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => '未有效找到人員',
                'User'    => null
            ], status: 404);
        }else {
        // 回應 JSON
            return response()->json([
                'status' => true,
                'message' => 'success',
                'User'    => $user
                ], 200);
        }
    }
    /**
     * @OA\patch(
     *     path="/api/user/{UsrNo}/disable",
     *     summary="刪除特定人員資訊",
     *     description="刪除特定人員資訊",
     *     operationId="DelteUser",
     *     tags={"user"},
     *     @OA\Parameter(
     *         name="UsrNo",
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
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="UsrNo", type="string", example="U001"),
     *             @OA\Property(property="UsrNM", type="string", example="姚佩彤"),
     *             @OA\Property(property="Note", type="string", example="測試測試"),
     *             @OA\Property(property="IsValid", type="boolean", example=false),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )   
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到人員"
     *     )
     * )
     */
    // 🔍 刪除特定部門
    public function disable($UsrNo)
    {
        $user = SysUser::where('UsrNo', $UsrNo)->first();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => '人員未找到',
                'Dept'    => null
            ], 404);
        }

        $user->IsValid = 0;
        $user->UpdateTime = now();
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'Dept'    => $user
        ], 200);
    }
}
