<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\SysCode;
use Illuminate\Support\Str;
use \Illuminate\Support\Facades\Http;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;

class CurrencyController extends Controller
{
        /**
     * @OA\POST(
     *     path="/api/createCurrency",
     *     summary="新增貨幣資訊",
     *     description="新增貨幣資訊",
     *     operationId="createCurrency",
     *     tags={"Base_Currency"},
     *     @OA\Parameter(
     *         name="currency_no",
     *         in="query",
     *         required=true,
     *         description="貨幣代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="currency_nm",
     *         in="query",
     *         required=true,
     *         description="貨幣名稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="currency_rate",
     *         in="query",
     *         required=false,
     *         description="現在匯率(以台幣為基準)",
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
     *             @OA\Property(property="currency_no", type="string", example="C001"),
     *             @OA\Property(property="currency_nm", type="string", example="台幣"),
     *             @OA\Property(property="currency_rate", type="string", example="取得當下匯率"),
     *             @OA\Property(property="note", type="string", example="測試測試"),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="建立失敗"
     *     )
     * )
     */
    // 儲存部門資料
    public function store(Request $request)
    {
        // 驗證請求
        $validated = $request->validate([
            'currency_no'     => 'required|string|max:255|unique:currencys,currency_no',
            'currency_nm'     => 'required|string|max:255',
            'currency_rate'     => 'nullable|string|max:255',
            'note'       => 'nullable|string|max:255',
            'is_valid'    => 'required|string'
        ]);

        // 取得建立當下匯率存入資料表(以台幣為基準)
        $curr_Rate = CurrencyController::getExchangeRate('TWD');
        foreach ($curr_Rate['conversion_rates'] as $currency => $rate) {
            if ($currency == $validated['currency_no']) {
                $validated['currency_rate'] = $rate;
                break;
            }
        }


        // 建立幣別資料
        $currency = Currency::create([
            'uuid'       => Str::uuid(),  // 自動生成 UUID
            'currency_no'     => $validated['currency_no'],
            'currency_nm'     => $validated['currency_nm'],
            'currency_rate'     => $validated['currency_rate']?? null,
            'note'       => $validated['note'] ?? null,
            'is_valid'    => $validated['is_valid']
        ]);

        // 回應 JSON
        if (!$currency) {
            return response()->json([
                'status' => false,
                'message' => '幣別建立失敗',
                'output'    => null
            ], status: 404);
        }else {
            // 回應 JSON
            return response()->json([
                'status' => true,
                'message' => 'success',
                'output'  => $currency
            ], 200);
        }
    }
    /**
     * @OA\GET(
     *     path="/api/Currency/{CurrencyNo}",
     *     summary="查詢特定貨幣資訊",
     *     description="查詢特定貨幣資訊",
     *     operationId="getCurrency",
     *     tags={"Base_Currency"},
     *     @OA\Parameter(
     *         name="CurrencyNo",
     *         in="path",
     *         required=true,
     *         description="貨幣代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="CurrencyNo", type="string", example="C001"),
     *             @OA\Property(property="CurrencyNM", type="string", example="台幣"),
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
     *         description="未找到貨幣資訊"
     *     )
     * )
     */
        // 🔍 查詢單一幣別
    public function show($CurrencyNo)
    {
        $Currency = Currency::findByCurrencyNo($CurrencyNo);
        
        if (!$Currency) {
            return response()->json([
                'status' => false,
                'message' => '未找到貨幣資訊',
                'output' => null
            ], 404);
        }
        // 回應 JSON
        return response()->json([                
            'status' => true,
            'message' => 'success',
            'output'    => $Currency
        ],200);
    }
    /**
     * @OA\GET(
     *     path="/api/Currencys/valid",
     *     summary="查詢所有有效貨幣資訊",
     *     description="查詢所有有效貨幣資訊",
     *     operationId="GetAllCurrency",
     *     tags={"Base_Currency"},
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="CurrencyNo", type="string", example="C001"),
     *             @OA\Property(property="CurrencyNM", type="string", example="台幣"),
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
     *         description="未找到有效貨幣資訊"
     *     )
     * )
     */
    // 🔍 查詢所有有效幣別
    public function getValidCurrencys()
    {
        $currencys = Currency::getValidCurrencys();
        if ($currencys->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => '未找到有效貨幣資訊',
                'output' => null
            ], 404);
        }
        return response()->json([                
            'status' => true,
            'message' => 'success',
            'output'    => Currency::getValidCurrencys()
        ],200);
    }
    /**
     * 取得指定貨幣的匯率
     */
    /**
     * @OA\GET(
     *     path="/api/exchange-rate/{CurrencyNo}",
     *     summary="讀取匯率",
     *     description="讀取匯率(不對外)",
     *     operationId="exchangeRate",
     *     tags={"Base_Currency"},
     *     @OA\Parameter(
     *         name="CurrencyNo",
     *         in="path",
     *         required=true,
     *         description="貨幣代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="CurrencyNo", type="string", example="C001"),
     *             @OA\Property(property="CurrencyNM", type="string", example="台幣"),
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
     *         description="未找到貨幣資訊"
     *     )
     * )
     */
    public function getExchangeRate($baseCurrency)
    {
        // 驗證貨幣代號
        if (!$baseCurrency) {
            return response()->json([
                'status' => false,
                'message' => '無效的貨幣代號',
                'output' => null
            ], 400);
        }

        // 檢查 API KEY 和 URL 是否存在
        if (empty(env('EXCHANGE_RATE_API_KEY')) || empty(env('EXCHANGE_RATE_API_URL'))) {
            return response()->json([
                'status' => false,
                'message' => 'API 金鑰或 URL 未設定',
                'output' => 'API 金鑰或 URL 未設定'
            ], 500);
        }
        // 從 .env 讀取 API KEY 和 URL
        $apiKey = env('EXCHANGE_RATE_API_KEY');
        $apiUrl = env('EXCHANGE_RATE_API_URL') . "$apiKey/latest/$baseCurrency";

        // 發送 HTTP GET 請求
        $response = Http::get($apiUrl);

        // 檢查是否請求成功
        if ($response->failed()) {
            return response()->json([
                'status' => false,
                'message' => '無法獲取匯率資訊',
                'output' => null
            ], 500);
        }

        // 解析 API 回應
        $data = $response->json();
        return $data;
        //return response()->json([
        //    'status' => true,
        //    'message' => 'success',
        //    'base_currency' => $data['base_code'],
        //    'exchange_rates' => $data['conversion_rates'],
        //]);
    }
    /**
     * @OA\patch(
     *     path="/api/Currencys/{CurrencyNo}/disable",
     *     summary="刪除特定貨幣資訊",
     *     description="刪除特定貨幣資訊",
     *     operationId="DelteCurrency",
     *     tags={"Base_Currency"},
     *     @OA\Parameter(
     *         name="CurrencyNo",
     *         in="path",
     *         required=true,
     *         description="貨幣代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="CurrencyNo", type="string", example="C001"),
     *             @OA\Property(property="CurrencyNM", type="string", example="台幣"),
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
     *         description="未找到幣別"
     *     )
     * )
     */
    // 🔍 刪除特定幣別
    public function disable($CurrencyNo)
    {
        $Currency = Currency::findByCurrencyNo($CurrencyNo);
        
        if (!$Currency) {
            return response()->json([
                'status' => false,
                'message' => '貨幣未找到',
                'output'    => null
            ], 404);
        }

        $Currency->IsValid = 0;
        $Currency->UpdateUser = 'admin'; // 這裡可以根據實際情況設置更新者
        $Currency->UpdateTime = now();
        $Currency->save();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'output'    => $Currency
        ], 200);
    }
    /**
     * @OA\get(
     *     path="/api/Currencys/showConst",
     *     summary="列出所有幣別需要的常用(下拉、彈窗)",
     *     description="列出所有幣別需要的常用(下拉、彈窗)",
     *     operationId="Show_Currency_ALL_Const",
     *     tags={"Base_Currency"},
     *     @OA\Response(
     *         response=200,
     *         description="成功"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="有效單據類型未找到"
     *     )
     * )
     */
    // 列出所有幣別需要的常用(下拉、彈窗)
    public function showConst($constant='all'){
        // 查詢 '所有幣別資料' 的資料
        $SysCode = SysCode::where('note', '幣別資料')->get();
        try {
            // 檢查是否有結果
            if ($SysCode->isEmpty() ) {
                return response()->json([
                    'status' => false,
                    'message' => '常用資料未找到',
                    'currency_no(option)' => null
                ], 404);
            }
    
            // 返回查詢結果
            return response()->json([
                'status' => true,
                'message' => 'success',
                'currency_no(option)' => $SysCode
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
