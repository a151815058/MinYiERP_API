<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
use Illuminate\Support\Str;
use \Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    // 儲存部門資料
    public function store(Request $request)
    {
        // 驗證請求
        $validated = $request->validate([
            'CurrencyNo'     => 'required|string|max:255|unique:currencys,CurrencyNo',
            'CurrencyNM'     => 'required|string|max:255',
            'Note'       => 'nullable|string|max:255',
            'IsVaild'    => 'required|boolean',
            'Createuser' => 'required|string|max:255',
            'UpdateUser' => 'required|string|max:255',
        ]);

        // 建立幣別資料
        $currency = Currency::create([
            'uuid'       => Str::uuid(),  // 自動生成 UUID
            'CurrencyNo'     => $validated['CurrencyNo'],
            'CurrencyNM'     => $validated['CurrencyNM'],
            'Note'       => $validated['Note'] ?? null,
            'IsVaild'    => $validated['IsVaild'],
            'Createuser' => $validated['Createuser'],
            'UpdateUser' => $validated['UpdateUser'],
            'CreateTime' => now(),  // 設定當前時間
            'UpdateTime' => now(),
        ]);

        // 回應 JSON
        return response()->json([
            'message' => '幣別建立成功',
            'CurrencyNM'    => $currency
        ], 201);
    }

        // 🔍 查詢單一幣別
    public function show($CurrencyNo)
    {
        $Currency = Currency::findByCurrencyNo($CurrencyNo);
        
        if (!$Currency) {
            return response()->json(['message' => '幣別未找到'], 404);
        }

        return response()->json($Currency);
    }
    // 🔍 查詢所有有效幣別
    public function getValidCurrencys()
    {
        return response()->json(Currency::getValidCurrencys());
    }
    /**
     * 取得指定貨幣的匯率
     */
    public function getExchangeRate($baseCurrency = 'TWD')
    {
        // 從 .env 讀取 API KEY 和 URL
        $apiKey = env('EXCHANGE_RATE_API_KEY');
        $apiUrl = env('EXCHANGE_RATE_API_URL') . "$apiKey/latest/$baseCurrency";

        // 發送 HTTP GET 請求
        $response = Http::get($apiUrl);

        // 檢查是否請求成功
        if ($response->failed()) {
            return response()->json(['error' => '無法獲取匯率資訊'], 500);
        }

        // 解析 API 回應
        $data = $response->json();

        return response()->json([
            'base_currency' => $data['base_code'],
            'exchange_rates' => $data['conversion_rates'],
        ]);
    }
}
