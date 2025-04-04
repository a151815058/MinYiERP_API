<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dept;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;



class DeptController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/createdept",
     *     summary="新增部門資訊",
     *     description="新增部門資訊",
     *     operationId="createdept",
     *     tags={"Base_Dept"},
     *     @OA\Parameter(
     *         name="DeptNo",
     *         in="query",
     *         required=true,
     *         description="部門代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="DeptNM",
     *         in="query",
     *         required=true,
     *         description="部門名稱",
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
     *             @OA\Property(property="DeptNo", type="string", example="A02"),
     *             @OA\Property(property="DeptNM", type="string", example="財務處"),
     *             @OA\Property(property="Note", type="string", example="測試測試"),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到部門"
     *     )
     * )
     */
    // 儲存部門資料
    public function store(Request $request)
    {
        // 驗證請求
        $validated = $request->validate([
            'DeptNo'     => 'required|string|max:255|unique:depts,DeptNo',
            'DeptNM'     => 'required|string|max:255',
            'Note'       => 'nullable|string|max:255',
            'IsValid'    => 'required|boolean'
        ]);

        // 建立部門資料
        $dept = Dept::create([
            'uuid'       => Str::uuid(),  // 自動生成 UUID
            'DeptNo'     => $validated['DeptNo'],
            'DeptNM'     => $validated['DeptNM'],
            'Note'       => $validated['Note'] ?? null,
            'IsValid'    => $validated['IsValid']
        ]);

        if (!$dept) {
            return response()->json([
                'status' => false,
                'message' => '部門建立失敗',
                'output'    => null
            ], status: 404);
        }else {
            // 回應 JSON
            return response()->json([
                'status' => true,
                'message' => 'success',
                'output'    => $dept
            ], 200);
        }

    }
    /**
     * @OA\GET(
     *     path="/api/dept/{DeptNo}",
     *     summary="查詢特定部門資訊",
     *     description="查詢特定部門資訊",
     *     operationId="getdept",
     *     tags={"Base_Dept"},
     *     @OA\Parameter(
     *         name="DeptNo",
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
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="DeptNo", type="string", example="A02"),
     *             @OA\Property(property="DeptNM", type="string", example="財務處"),
     *             @OA\Property(property="Note", type="string", example="測試測試"),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到部門"
     *     )
     * )
     */
    // 🔍 查詢單一部門
    public function show($deptNo)
    {
        $dept = Dept::findByDeptNo($deptNo);
        
        if (!$dept) {
            return response()->json([
                'status' => false,
                'message' => '部門未找到',
                'output'    => null
            ], 404);
        }

        return response()->json([                
            'status' => true,
            'message' => 'success',
            'output'    => $dept
        ],200);
    }
    /**
     * @OA\GET(
     *     path="/api/depts/valid",
     *     summary="查詢所有有效部門資訊",
     *     description="查詢所有有效部門資訊",
     *     operationId="GetAllDept",
     *     tags={"Base_Dept"},
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="DeptNo", type="string", example="A02"),
     *             @OA\Property(property="DeptNM", type="string", example="財務處"),
     *             @OA\Property(property="Note", type="string", example="測試測試"),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到有效部門"
     *     )
     * )
     */
    // 🔍 查詢所有有效部門
    public function getValidDepts()
    {
        $depts = Dept::getValidDepts();
        if ($depts->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => '未找到有效部門',
                'output'    => null
            ], 404);
        }
        return response()->json([                
            'status' => true,
            'message' => 'success',
            'output'    => $depts
        ],200);
    }
    /**
     * @OA\patch(
     *     path="/api/dept/{deptNo}/disable",
     *     summary="刪除特定部門資訊",
     *     description="刪除特定部門資訊",
     *     operationId="DelteDept",
     *     tags={"Base_Dept"},
     *     @OA\Parameter(
     *         name="deptNo",
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
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="DeptNo", type="string", example="A02"),
     *             @OA\Property(property="DeptNM", type="string", example="財務處"),
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
     *         description="未找到部門"
     *     )
     * )
     */
    // 🔍 刪除特定部門
    public function disable($deptNo)
    {
        $dept = Dept::findByDeptNo($deptNo);
        
        if (!$dept) {
            return response()->json([
                'status' => false,
                'message' => '部門未找到',
                'output'    => null
            ], 404);
        }

        $dept->IsValid = 0;
        $dept->UpdateUser = 'admin';
        $dept->UpdateTime = now();
        $dept->save();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'output'    => $dept
        ], 200);
    }
}


