<?php

namespace App\Http\Controllers;

use App\Models\PaymentTerm;
use App\Models\SysCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;

class PaymentTermController extends Controller
{
        /**
     * @OA\POST(
     *     path="/api/createPaymentTerm",
     *     summary="新增付款條件",
     *     description="新增付款條件",
     *     operationId="createPaymentTerm",
     *     tags={"Base_PaymentTerm"},
     *     @OA\Parameter(
     *         name="terms_no",
     *         in="query",
     *         required=true,
     *         description="付款條件代碼",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="terms_nm",
     *         in="query",
     *         required=true,
     *         description="付款條件名稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="terms_days",
     *         in="query",
     *         required=true,
     *         description="付款條件月結天數",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="pay_mode",
     *         in="query",
     *         required=true,
     *         description="付款條件 當月/隔月",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="pay_day",
     *         in="query",
     *         required=true,
     *         description="付款時間",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="note",
     *         in="query",
     *         required=false,
     *         description="備註",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="is_valid",
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
     *             @OA\Property(property="terms_no", type="string", example="T001"),
     *             @OA\Property(property="terms_nm", type="string", example="月結30天"),
     *             @OA\Property(property="terms_days", type="integer", example="30"),
     *             @OA\Property(property="pay_mode", type="string", example="M001"),
     *             @OA\Property(property="pay_day", type="integer", example="30"),
     *             @OA\Property(property="note", type="string", example="測試測試"),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
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
            'terms_no'     => 'required|string|max:255|unique:paymentterms,terms_no',
            'terms_nm'     => 'required|string|max:255',
            'terms_days'     => 'required|integer|max:31',
            'pay_mode'     => 'required|string|max:255',
            'pay_day'     => 'required|integer|max:31',
            'note'       => 'nullable|string|max:255',
            'is_valid'    => 'required|boolean'
        ]);

        // 建立付款條件
        $PaymentTerm = PaymentTerm::create([
            'terms_no'     => $validated['terms_no'],
            'terms_nm'     => $validated['terms_nm'],
            'terms_days'     => $validated['terms_days'],
            'pay_mode'     => $validated['pay_mode'],
            'pay_day'     => $validated['pay_day'],
            'note'       => $validated['note'] ?? null,
            'is_valid'    => $validated['is_valid']
        ]);

        // 回應 JSON
        if (!$PaymentTerm) {
            return response()->json([
                'status' => false,
                'message' => '付款條件建立失敗',
                'output'    => null
            ], status: 404);
        }else {
            // 回應 JSON
            return response()->json([
                'status' => true,
                'message' => 'success',
                'output'   => $PaymentTerm
            ], 200);
        }
    }
    /**
     * @OA\GET(
     *     path="/api/PaymentTerm/{TermNo}",
     *     summary="查詢特定付款條件",
     *     description="查詢特定付款條件",
     *     operationId="getPaymentTerm",
     *     tags={"Base_PaymentTerm"},
     *     @OA\Parameter(
     *         name="terms_no",
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
     *             @OA\Property(property="terms_no", type="string", example="T001"),
     *             @OA\Property(property="terms_nm", type="string", example="月結30天"),
     *             @OA\Property(property="terms_days", type="integer", example="30"),
     *             @OA\Property(property="pay_mode", type="string", example="M001"),
     *             @OA\Property(property="pay_day", type="integer", example="30"),
     *             @OA\Property(property="note", type="string", example="測試測試"),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
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
                'output'    => null
            ], 404);
        }

        return response()->json([                
            'status' => true,
            'message' => 'success',
            'output'    => $PaymentTerm
        ],200);
    }
    /**
     * @OA\GET(
     *     path="/api/PaymentTerms/valid",
     *     summary="查詢所有有效付款條件",
     *     description="查詢所有有效付款條件",
     *     operationId="GetAllPaymentTerm",
     *     tags={"Base_PaymentTerm"},
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="terms_no", type="string", example="T001"),
     *             @OA\Property(property="terms_nm", type="string", example="月結30天"),
     *             @OA\Property(property="terms_days", type="integer", example="30"),
     *             @OA\Property(property="pay_mode", type="string", example="M001"),
     *             @OA\Property(property="pay_day", type="integer", example="30"),
     *             @OA\Property(property="note", type="string", example="測試測試"),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
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
                'output'    => null
            ], 404);
        }
        return response()->json([                
            'status' => true,
            'message' => 'success',
            'output'    => $PaymentTerm
        ],200);
    }
    /**
     * @OA\patch(
     *     path="/api/PaymentTerm/{TermNo}/disable",
     *     summary="刪除特定付款條件",
     *     description="刪除特定付款條件",
     *     operationId="DeletePaymentTerm",
     *     tags={"Base_PaymentTerm"},
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
     *             @OA\Property(property="terms_no", type="string", example="T001"),
     *             @OA\Property(property="terms_nm", type="string", example="月結30天"),
     *             @OA\Property(property="terms_days", type="integer", example="30"),
     *             @OA\Property(property="pay_mode", type="string", example="M001"),
     *             @OA\Property(property="pay_day", type="integer", example="30"),
     *             @OA\Property(property="note", type="string", example="測試測試"),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )   
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到付款條件"
     *     )
     * )
     */
    // 🔍 刪除特定付款條件
    public function disable($TermsNo)
    {
        $PaymentTerm = PaymentTerm::findByTermsNo($TermsNo);
        
        if (!$PaymentTerm) {
            return response()->json([
                'status' => false,
                'message' => '付款條件未找到',
                'output'    => null
            ], 404);
        }

        $PaymentTerm->IsValid = 0;
        $PaymentTerm->UpdateUser = 'admin';
        $PaymentTerm->UpdateTime = now();
        $PaymentTerm->save();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'output'    => $PaymentTerm
        ], 200);
    }
    /**
     * @OA\get(
     *     path="/api/PaymentTerms/showConst",
     *     summary="列出所有付款條件需要的常用(下拉、彈窗)",
     *     description="列出所有付款條件需要的常用(下拉、彈窗)",
     *     operationId="Show_PaymentTerm_ALL_Const",
     *     tags={"Base_PaymentTerm"},
     *     @OA\Response(
     *         response=200,
     *         description="成功"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="有效付款條件未找到"
     *     )
     * )
     */
    // 列出所有付款條件需要的常用(下拉、彈窗)
    public function showConst($constant='all'){
        // 查詢 '所有付款條件' 的資料
        $SysCode = SysCode::where('param_sn', '02')->get();
        try {
            // 檢查是否有結果
            if ($SysCode->isEmpty() ) {
                return response()->json([
                    'status' => false,
                    'message' => '常用資料未找到',
                    'paymenttermoption' => null
                ], 404);
            }
    
            // 返回查詢結果
            return response()->json([
                'status' => true,
                'message' => 'success',
                'paymenttermoption' => $SysCode
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // 捕捉驗證失敗，並返回錯誤訊息
            return response()->json([
                'status' => false,
                'message' => '驗證錯誤',
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            // 其他例外處理，並紀錄錯誤訊息
            Log::error('資料錯誤：' . $e->getMessage(), [
                'exception' => $e,
                'stack' => $e->getTraceAsString() // 可選，根據需要可增加更多上下文信息
            ]);
    
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => env('APP_DEBUG') ? $e->getMessage() : '請稍後再試'
            ], 500);
        }
    }
}
