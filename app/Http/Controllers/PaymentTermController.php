<?php

namespace App\Http\Controllers;

use App\Models\PaymentTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;

class PaymentTermController extends Controller
{
        /**
     * @OA\POST(
     *     path="/api/createPaymentTerm",
     *     summary="新增付款條件",
     *     description="新增付款條件",
     *     operationId="createPaymentTerm",
     *     tags={"PaymentTerm"},
     *     @OA\Parameter(
     *         name="TermsNo",
     *         in="query",
     *         required=true,
     *         description="付款條件代碼",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="TermsNM",
     *         in="query",
     *         required=true,
     *         description="付款條件名稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="TermsDays",
     *         in="query",
     *         required=true,
     *         description="付款條件月結天數",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="PayMode",
     *         in="query",
     *         required=true,
     *         description="付款條件 當月/隔月",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="PayDay",
     *         in="query",
     *         required=true,
     *         description="付款時間",
     *         @OA\Schema(type="integer")
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
     *             @OA\Property(property="TermsNo", type="string", example="T001"),
     *             @OA\Property(property="TermsNM", type="string", example="月結30天"),
     *             @OA\Property(property="TermsDays", type="integer", example="30"),
     *             @OA\Property(property="PayMode", type="string", example="M001"),
     *             @OA\Property(property="PayDay", type="integer", example="30"),
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
    // 儲存付款條件
    public function store(Request $request)
    {
        // 驗證請求
        $validated = $request->validate([
            'TermsNo'     => 'required|string|max:255|unique:paymentterms,TermsNo',
            'TermsNM'     => 'required|string|max:255',
            'TermsDays'     => 'required|integer|max:31',
            'PayMode'     => 'required|string|max:255',
            'PayDay'     => 'required|integer|max:31',
            'Note'       => 'nullable|string|max:255',
            'IsValid'    => 'required|boolean',
            'Createuser' => 'required|string|max:255',
            'UpdateUser' => 'required|string|max:255',
        ]);

        // 建立付款條件
        $PaymentTerm = PaymentTerm::create([
            'TermsNo'     => $validated['TermsNo'],
            'TermsNM'     => $validated['TermsNM'],
            'TermsDays'     => $validated['TermsDays'],
            'PayMode'     => $validated['PayMode'],
            'PayDay'     => $validated['PayDay'],
            'Note'       => $validated['Note'] ?? null,
            'IsValid'    => $validated['IsValid'],
            'Createuser' => $validated['Createuser'],
            'UpdateUser' => $validated['UpdateUser'],
            'CreateTime' => now(),  // 設定當前時間
            'UpdateTime' => now()
        ]);

        // 回應 JSON
        if (!$PaymentTerm) {
            return response()->json([
                'status' => false,
                'message' => '付款條件建立失敗',
                'PaymentTerm'    => null
            ], status: 404);
        }else {
            // 回應 JSON
            return response()->json([
                'status' => true,
                'message' => 'success',
                'PaymentTerm'   => $PaymentTerm
            ], 200);
        }
    }
    /**
     * @OA\GET(
     *     path="/api/PaymentTerm/{TermNo}",
     *     summary="查詢特定付款條件",
     *     description="查詢特定付款條件",
     *     operationId="getPaymentTerm",
     *     tags={"PaymentTerm"},
     *     @OA\Parameter(
     *         name="TermNo",
     *         in="path",
     *         required=true,
     *         description="付款條件代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="TermsNo", type="string", example="T001"),
     *             @OA\Property(property="TermsNM", type="string", example="月結30天"),
     *             @OA\Property(property="TermsDays", type="integer", example="30"),
     *             @OA\Property(property="PayMode", type="string", example="M001"),
     *             @OA\Property(property="PayDay", type="integer", example="30"),
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
     *         description="未找到付款條件"
     *     )
     * )
     */
    // 🔍 查詢單一付款條件
    public function show($TermsNo)
    {
        $PaymentTerm = PaymentTerm::findByTermsNo($TermsNo);
        // 如果找不到付款條件，回傳錯誤訊息
        if (!$PaymentTerm) {
            return response()->json([
                'status' => false,
                'message' => '付款條件未找到',
                'PaymentTerm'    => null
            ], 404);
        }

        return response()->json([                
            'status' => true,
            'message' => 'success',
            'PaymentTerm'    => $PaymentTerm
        ],200);
    }
    /**
     * @OA\GET(
     *     path="/api/PaymentTerms/valid",
     *     summary="查詢所有有效付款條件",
     *     description="查詢所有有效付款條件",
     *     operationId="GetAllPaymentTerm",
     *     tags={"PaymentTerm"},
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="TermsNo", type="string", example="T001"),
     *             @OA\Property(property="TermsNM", type="string", example="月結30天"),
     *             @OA\Property(property="TermsDays", type="integer", example="30"),
     *             @OA\Property(property="PayMode", type="string", example="M001"),
     *             @OA\Property(property="PayDay", type="integer", example="30"),
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
     *         description="未找到有效付款條件"
     *     )
     * )
     */
    // 🔍 查詢所有有效付款條件
    public function getValidTerms()
    {
        $PaymentTerm = PaymentTerm::getValidTerms();
        
        if ($PaymentTerm->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => '未找到有效付款條件',
                'PaymentTerm'    => null
            ], 404);
        }
        return response()->json([                
            'status' => true,
            'message' => 'success',
            'PaymentTerm'    => $PaymentTerm
        ],200);
    }
    /**
     * @OA\patch(
     *     path="/api/PaymentTerm/{TermNo}/disable",
     *     summary="刪除特定付款條件",
     *     description="刪除特定付款條件",
     *     operationId="DeletePaymentTerm",
     *     tags={"PaymentTerm"},
     *     @OA\Parameter(
     *         name="TermNo",
     *         in="path",
     *         required=true,
     *         description="付款條件代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="TermsNo", type="string", example="T001"),
     *             @OA\Property(property="TermsNM", type="string", example="月結30天"),
     *             @OA\Property(property="TermsDays", type="integer", example="30"),
     *             @OA\Property(property="PayMode", type="string", example="M001"),
     *             @OA\Property(property="PayDay", type="integer", example="30"),
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
     *         description="未找到付款條件"
     *     )
     * )
     */
    // 🔍 刪除特定部門
    public function disable($TermsNo)
    {
        $PaymentTerm = PaymentTerm::findByTermsNo($TermsNo);
        
        if (!$PaymentTerm) {
            return response()->json([
                'status' => false,
                'message' => '付款條件未找到',
                'Dept'    => null
            ], 404);
        }

        $PaymentTerm->IsValid = 0;
        $PaymentTerm->UpdateTime = now();
        $PaymentTerm->save();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'Dept'    => $PaymentTerm
        ], 200);
    }
}
