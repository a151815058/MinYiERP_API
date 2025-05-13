<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\SysCode;
use Illuminate\Support\Str;
require_once base_path('app/Models/connect.php'); 
use \Illuminate\Support\Facades\Http;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CurrencyController extends Controller
{
        /**
     * @OA\POST(
     *     path="/api/createcurrency",
     *     summary="新增貨幣資訊",
     *     description="新增貨幣資訊",
     *     operationId="createcurrency",
     *     tags={"base_currency"},
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
    // 儲存貨幣資訊
    public function store(Request $request)
    {
        // 驗證請求
        $validator = Validator::make($request->all(),[
            'currency_no'     => 'required|string|max:255|unique:currencys,currency_no',
            'currency_nm'     => 'required|string|max:255',
            'currency_rate'     => 'nullable|string|max:255',
            'note'       => 'nullable|string|max:255',
            'is_valid'    => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => true,
                'message' => '資料驗證失敗',
                'errors' => $validator->errors()
            ], 200);
        }

        // 取得建立當下匯率存入資料表(以台幣為基準)
        //$curr_Rate = CurrencyController::getExchangeRate('TWD');
        //foreach ($curr_Rate['conversion_rates'] as $currency => $rate) {
        //    if ($currency == $validated['currency_no']) {
        //        $validated['currency_rate'] = $rate;
        //        break;
        //    }
        //}


        // 建立幣別資料
        $currency = Currency::create([
            'uuid'            => Str::uuid(),  // 自動生成 UUID
            'currency_no'     => $request['currency_no'],
            'currency_nm'     => $request['currency_nm'],
            'currency_rate'   => $request['currency_rate']?? null,
            'note'            => $request['note'] ?? null,
            'is_valid'        => $request['is_valid']
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
     *     path="/api/currency/{currencyno}",
     *     summary="查詢特定貨幣資訊",
     *     description="查詢特定貨幣資訊",
     *     operationId="getcurrency",
     *     tags={"base_currency"},
     *     @OA\Parameter(
     *         name="currencyno",
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
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
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
        $Currency = Currency::findByCurrencyNo($CurrencyNo)->where('is_valid','1')->first();
        
        if (!$Currency) {
            return response()->json([
                'status' => true,
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
     *     path="/api/currencys/valid",
     *     summary="查詢所有有效貨幣資訊(含關鍵字查詢，貨幣代號、貨幣名稱)",
     *     description="查詢所有有效貨幣資訊(含關鍵字查詢，貨幣代號、貨幣名稱)",
     *     operationId="getallcurrency",
     *     tags={"base_currency"},
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
     *             @OA\Property(property="CurrencyNo", type="string", example="C001"),
     *             @OA\Property(property="CurrencyNM", type="string", example="台幣"),
     *             @OA\Property(property="Note", type="string", example="測試測試"),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到有效貨幣資訊"
     *     )
     * )
     */
    // 🔍 查詢所有有效幣別(含關鍵字查詢)
    public function getvalidcurrencys(Request $request)
    {
            $pdo = getPDOConnection();
            $keyword = $request->query('keyword'); // 可為 null
            $page = $request->query('page'); // 當前頁碼
            $pageSize = $request->query('pageSize'); // 一頁顯示幾筆數值
            $page = $page ? (int)$page : 1; // 預設為第 1 頁
            $pageSize = $pageSize ? (int)$pageSize : 30; // 預設每頁顯示 30 筆資料

            $likeKeyword = '%' . $keyword . '%';

        // 使用 DB::select 進行關鍵字查詢
        if($keyword != null) {
            //查詢目前頁數的資料
            $offset = ($page - 1) * $pageSize;
            //LIMIT 30：每次最多回傳 30 筆資料
            //OFFSET 0：從第 0 筆開始取，也就是第一頁的第 1 筆
            //LIMIT 30 OFFSET 0  -- 取第 1~30 筆
            //LIMIT 30 OFFSET 30 -- 取第 31~60 筆
            //LIMIT 30 OFFSET 60 -- 取第 61~90 筆
            $sql = "select  *
                    from currencys
                    where currencys.is_valid = '1'  
                    and ( currencys.currency_no LIKE ? OR currencys.currency_nm LIKE ?)
                    order by update_time,create_time asc
                    LIMIT ? OFFSET ?;";

            $currencys = DB::select($sql, [$likeKeyword, $likeKeyword, $pageSize, $offset]);

        } else {
            $currencys = Currency::where('is_valid', '1')->get();
        }
        //取得總筆數與總頁數   
        $sql_count = "
                SELECT COUNT(*) as total
                from currencys
                    where currencys.is_valid = '1'  
                    and ( currencys.currency_no LIKE ? OR currencys.currency_nm LIKE ?)
                    order by update_time,create_time asc;
                ";
            $stmt = $pdo->prepare($sql_count);
            $stmt->execute([$likeKeyword, $likeKeyword]);
            $total = $stmt->fetchColumn();
            $totalPages = ceil($total / $pageSize); // 計算總頁數   

        if (!$currencys) {
            return response()->json([
                'status' => true,
                'atPage' => $page,
                'total' => $total,
                'totalPages' => $totalPages,
                'message' => '未找到有效貨幣資訊',
                'output' => $currencys
            ], 404);
        }
        return response()->json([                
            'status' => true,
            'atPage' => $page,
            'total' => $total,
            'totalPages' => $totalPages,
            'message' => 'success',
            'output'    => Currency::getValidCurrencys()
        ],200);
    }
    /**
     * 取得指定貨幣的匯率
     */
    /**
     * @OA\GET(
     *     path="/api/exchange-rate/{currencyno}",
     *     summary="讀取匯率",
     *     description="讀取匯率(不對外)",
     *     operationId="exchangeRate",
     *     tags={"base_currency"},
     *     @OA\Parameter(
     *         name="currencyno",
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
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到貨幣資訊"
     *     )
     * )
     */
    public function getexchangerate($baseCurrency)
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
     *     path="/api/currencys/{currencyno}/disable",
     *     summary="刪除特定貨幣資訊",
     *     description="刪除特定貨幣資訊",
     *     operationId="deletecurrency",
     *     tags={"base_currency"},
     *     @OA\Parameter(
     *         name="currencyno",
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
     *             @OA\Property(property="is_valid", type="boolean", example=false),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
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
        $Currency = Currency::findByCurrencyNo($CurrencyNo)->where('is_valid','1')->first();
        
        if (!$Currency) {
            return response()->json([
                'status' => true,
                'message' => '貨幣未找到',
                'output'    => null
            ], 404);
        }

        $Currency->is_valid = 0;
        $Currency->update_user = 'admin'; // 這裡可以根據實際情況設置更新者
        $Currency->update_time = now();
        $Currency->save();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'output'    => $Currency
        ], 200);
    }
    /**
     * @OA\get(
     *     path="/api/currencys/showconst",
     *     summary="列出所有幣別需要的常用(下拉、彈窗)",
     *     description="列出所有幣別需要的常用(下拉、彈窗)",
     *     operationId="Show_currency_aLL_const",
     *     tags={"base_currency"},
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
    public function showconst($constant='all'){
        // 查詢 '所有幣別資料' 的資料
        $SysCode = SysCode::where('note', '幣別資料')->where('is_valid','1')->get();
        try {
            // 檢查是否有結果
            if ($SysCode->isEmpty() ) {
                return response()->json([
                    'status' => true,
                    'message' => '常用資料未找到',
                    'currencyoption' => null
                ], 404);
            }
    
            // 返回查詢結果
            return response()->json([
                'status' => true,
                'message' => 'success',
                'currencyoption' => $SysCode
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
