<?php

namespace App\Http\Controllers;

use App\Models\PaymentTerm;
use App\Models\SysCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentTermController extends Controller
{
        /**
     * @OA\POST(
     *     path="/api/createpaymentterm",
     *     summary="新增付款條件",
     *     description="新增付款條件",
     *     operationId="createpaymentterm",
     *     tags={"base_paymentterm"},
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
     *         description="付款條件 1:當月/2:隔月",
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
        try {
                // 驗證請求
                $validator = Validator::make($request->all(),[
                    'terms_no'     => 'required|string|max:255|unique:paymentterms,terms_no',
                    'terms_nm'     => 'required|string|max:255',
                    'terms_days'     => 'required|integer|max:31',
                    'pay_mode'     => 'required|string|max:255',
                    'pay_day'     => 'required|integer|max:31',
                    'note'       => 'nullable|string|max:255',
                    'is_valid'    => 'required|boolean'
                ]);

                if($validator->fails()){
                    return response()->json([
                        'status' => true,
                        'message' => '資料驗證失敗',
                        'errors' => $validator->errors()
                    ], 200);
                }

                // 建立付款條件
                $PaymentTerm = PaymentTerm::create([
                    'terms_no'     => $request['terms_no'],
                    'terms_nm'     => $request['terms_nm'],
                    'terms_days'     => $request['terms_days'],
                    'pay_mode'     => $request['pay_mode'],
                    'pay_day'     => $request['pay_day'],
                    'note'       => $request['note'] ?? null,
                    'is_valid'    => $request['is_valid']
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            // 捕捉驗證失敗
            return response()->json([
                'status' => false,
                'message' => '驗證錯誤',
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            // 其他例外處理
            Log::error('建立單據資料錯誤：' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        }

    }
    /**
     * @OA\GET(
     *     path="/api/paymentterm/{termno}",
     *     summary="查詢特定付款條件",
     *     description="查詢特定付款條件",
     *     operationId="getpaymentterm",
     *     tags={"base_paymentterm"},
     *     @OA\Parameter(
     *         name="termno",
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
        try{
            $PaymentTerm = PaymentTerm::findByTermsNo($TermsNo)->where('is_valid', 1)->first();
            // 如果找不到付款條件，回傳錯誤訊息
            if (!$PaymentTerm) {
                return response()->json([
                    'status' => true,
                    'message' => '付款條件未找到',
                    'output'    => null
                ], 404);
            }
    
            return response()->json([                
                'status' => true,
                'message' => 'success',
                'output'    => $PaymentTerm
            ],200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // 捕捉驗證失敗
            return response()->json([
                'status' => false,
                'message' => '驗證錯誤',
                'errors' => $e->errors()
            ], 422);
        
        } catch (\Exception $e) {
            // 其他例外處理
            Log::error('建立資料錯誤：' . $e->getMessage());
        
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
            }

    }
    /**
     * @OA\GET(
     *     path="/api/paymentterms/valid",
     *     summary="查詢所有有效付款條件(含關鍵字查詢)",
     *     description="查詢所有有效付款條件(含關鍵字查詢)",
     *     operationId="getallpaymentterm",
     *     tags={"base_paymentterm"},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         required=false,
     *         description="關鍵字查詢",
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
     *         description="未找到有效付款條件"
     *     )
     * )
     */
    // 🔍 查詢所有有效付款條件
    public function getvalidterms(Request $request)
    {
        try{
            $keyword = $request->query('keyword'); // 可為 null

            // 使用 DB::select 進行關鍵字查詢
            if($keyword != null) {
                $likeKeyword = '%' . $keyword . '%';
                $sql = "select  *
                        from paymentterms
                        where paymentterms.is_valid = '1'  
                        and ( paymentterms.terms_no LIKE ? OR paymentterms.terms_nm LIKE ?)
                        order by update_time,create_time asc;";

                $PaymentTerm = DB::select($sql, [$likeKeyword, $likeKeyword]);

            } else {
                $PaymentTerm = PaymentTerm::where('is_valid', '1')->get();
            }

        
            if (!$PaymentTerm) {
                return response()->json([
                    'status' => true,
                    'message' => '未找到有效付款條件',
                    'output'    => $PaymentTerm
                ], 404);
            }
            return response()->json([                
                'status' => true,
                'message' => 'success',
                'output'    => $PaymentTerm
            ],200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // 捕捉驗證失敗
            return response()->json([
                'status' => false,
                'message' => '驗證錯誤',
                'errors' => $e->errors()
            ], 422);
        
        } catch (\Exception $e) {
            // 其他例外處理
            Log::error('建立資料錯誤：' . $e->getMessage());
        
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        }

    }
    /**
     * @OA\patch(
     *     path="/api/paymentterm/{termno}/disable",
     *     summary="刪除特定付款條件",
     *     description="刪除特定付款條件",
     *     operationId="deletepaymentterm",
     *     tags={"base_paymentterm"},
     *     @OA\Parameter(
     *         name="termno",
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
        try{
            $PaymentTerm = PaymentTerm::findByTermsNo($TermsNo)->where('is_valid', 1)->first();
        
            if (!$PaymentTerm) {
                return response()->json([
                    'status' => false,
                    'message' => '付款條件未找到',
                    'output'    => null
                ], 404);
            }
    
            $PaymentTerm->is_valid = 0;
            $PaymentTerm->update_user = 'admin';
            $PaymentTerm->update_time = now();
            $PaymentTerm->save();
    
            return response()->json([
                'status' => true,
                'message' => 'success',
                'output'    => $PaymentTerm
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // 捕捉驗證失敗
            return response()->json([
                'status' => false,
                'message' => '驗證錯誤',
                'errors' => $e->errors()
            ], 422);
        
        } catch (\Exception $e) {
            // 其他例外處理
            Log::error('建立資料錯誤：' . $e->getMessage());
        
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        }
    }
    /**
     * @OA\get(
     *     path="/api/paymentterms/showconst",
     *     summary="列出所有付款條件需要的常用(下拉、彈窗)",
     *     description="列出所有付款條件需要的常用(下拉、彈窗)",
     *     operationId="show_paymentterm_all_const",
     *     tags={"base_paymentterm"},
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
    public function showconst($constant='all'){
        // 查詢 '所有付款條件' 的資料
        $SysCode = SysCode::where('param_sn', '02')->where('is_valid','1')->get();
        try {
            // 檢查是否有結果
            if ($SysCode->isEmpty() ) {
                return response()->json([
                    'status' => true,
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
