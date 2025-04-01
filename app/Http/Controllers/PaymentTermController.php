<?php

namespace App\Http\Controllers;

use App\Models\PaymentTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
    /**
     * @OA\Get(
     *     path="/PaymentTerm/{deptNo}",
     *     summary="取得單一部門資訊",
     *     description="根據部門編號查詢部門資訊",
     *     operationId="getPaymentTerm",
     *     tags={"PaymentTerm"},
     *     @OA\Parameter(
     *         name="deptNo",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="DeptNo", type="string", example="D001"),
     *             @OA\Property(property="DeptNM", type="string", example="資訊部"),
     *             @OA\Property(property="IsVaild", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到部門"
     *     )
     * )
     */
class PaymentTermController extends Controller
{
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
            'IsVaild'    => 'required|boolean',
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
            'IsVaild'    => $validated['IsVaild'],
            'Createuser' => $validated['Createuser'],
            'UpdateUser' => $validated['UpdateUser'],
            'CreateTime' => now(),  // 設定當前時間
            'UpdateTime' => now()
        ]);

        // 回應 JSON
        return response()->json([
            'message' => '付款條件建立成功',
            'PaymentTerm'    => $PaymentTerm
        ], 201);
    }

    // 🔍 查詢單一付款條件
    public function show($TermsNo)
    {
        $dept = PaymentTerm::findByTermsNo($TermsNo);
        
        if (!$dept) {
            return response()->json(['message' => '付款條件未找到'], 404);
        }

        return response()->json($dept);
    }

    // 🔍 查詢所有有效付款條件
    public function getValidTerms()
    {
        return response()->json(PaymentTerm::getValidTerms());
    }
}
