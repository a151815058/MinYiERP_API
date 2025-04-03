<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
use Illuminate\Support\Str;
use \Illuminate\Support\Facades\Http;
use OpenApi\Annotations as OA;

class CurrencyController extends Controller
{
        /**
     * @OA\POST(
     *     path="/api/createCurrency",
     *     summary="新增貨幣資訊",
     *     description="新增貨幣資訊",
     *     operationId="createCurrency",
     *     tags={"Currency"},
     *     @OA\Parameter(
     *         name="CurrencyNo",
     *         in="query",
     *         required=true,
     *         description="貨幣代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="CurrencyNM",
     *         in="query",
     *         required=true,
     *         description="貨幣名稱",
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
            'CurrencyNo'     => 'required|string|max:255|unique:currencys,CurrencyNo',
            'CurrencyNM'     => 'required|string|max:255',
            'Note'       => 'nullable|string|max:255',
            'IsValid'    => 'required|boolean',
            'Createuser' => 'required|string|max:255',
            'UpdateUser' => 'required|string|max:255',
        ]);

        // 建立幣別資料
        $currency = Currency::create([
            'uuid'       => Str::uuid(),  // 自動生成 UUID
            'CurrencyNo'     => $validated['CurrencyNo'],
            'CurrencyNM'     => $validated['CurrencyNM'],
            'Note'       => $validated['Note'] ?? null,
            'IsValid'    => $validated['IsValid'],
            'Createuser' => $validated['Createuser'],
            'UpdateUser' => $validated['UpdateUser'],
            'CreateTime' => now(),  // 設定當前時間
            'UpdateTime' => now(),
        ]);

        // 回應 JSON
        if (!$currency) {
            return response()->json([
                'status' => false,
                'message' => '部門建立失敗',
                'CurrencyNM'    => null
            ], status: 404);
        }else {
            // 回應 JSON
            return response()->json([
                'status' => true,
                'message' => 'success',
                'CurrencyNM'  => $currency
            ], 200);
        }
    }
    /**
     * @OA\GET(
     *     path="/api/Currency/{CurrencyNo}",
     *     summary="查詢特定貨幣資訊",
     *     description="查詢特定貨幣資訊",
     *     operationId="getCurrency",
     *     tags={"Currency"},
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
                'Currency' => null
            ], 404);
        }
        // 回應 JSON
        return response()->json([                
            'status' => true,
            'message' => 'success',
            'Currency'    => $Currency
        ],200);
    }
    /**
     * @OA\GET(
     *     path="/api/Currencys/valid",
     *     summary="查詢所有有效貨幣資訊",
     *     description="查詢所有有效貨幣資訊",
     *     operationId="GetAllCurrency",
     *     tags={"Currency"},
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
                'Currencys' => null
            ], 404);
        }
        return response()->json([                
            'status' => true,
            'message' => 'success',
            'Currencys'    => Currency::getValidCurrencys()
        ],200);
    }
    /**
     * 取得指定貨幣的匯率
     */
    /**
     * @OA\GET(
     *     path="/api/exchange-rate/{CurrencyNo}",
     *     summary="讀取匯率",
     *     description="讀取匯率",
     *     operationId="exchangeRate",
     *     tags={"Currency"},
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
        $currency = Currency::findByCurrencyNo($baseCurrency);
        if (!$currency) {
            return response()->json([
                'status' => false,
                'message' => '無效的貨幣代號',
                'Currency' => null
            ], 400);
        }

        // 檢查 API KEY 和 URL 是否存在
        if (empty(env('EXCHANGE_RATE_API_KEY')) || empty(env('EXCHANGE_RATE_API_URL'))) {
            return response()->json([
                'status' => false,
                'message' => 'API 金鑰或 URL 未設定'
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
                'message' => '無法獲取匯率資訊'
            ], 500);
        }

        // 解析 API 回應
        $data = $response->json();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'base_currency' => $data['base_code'],
            'exchange_rates' => $data['conversion_rates'],
        ]);
    }
    /**
     * @OA\patch(
     *     path="/api/Currencys/{CurrencyNo}/disable",
     *     summary="刪除特定貨幣資訊",
     *     description="刪除特定貨幣資訊",
     *     operationId="DelteCurrency",
     *     tags={"Currency"},
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
     *         description="未找到部門"
     *     )
     * )
     */
    // 🔍 刪除特定部門
    public function disable($CurrencyNo)
    {
        $Currency = Currency::findByCurrencyNo($CurrencyNo);
        
        if (!$Currency) {
            return response()->json([
                'status' => false,
                'message' => '貨幣未找到',
                'Currency'    => null
            ], 404);
        }

        $Currency->IsValid = 0;
        $Currency->UpdateTime = now();
        $Currency->save();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'Currency'    => $Currency
        ], 200);
    }
}
