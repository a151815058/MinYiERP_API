<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoiceInfo;
use App\Models\SysCode;
use Illuminate\Support\Str;
require_once base_path('app/Models/connect.php'); 
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InvoiceInfoController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/createinvoiceinfo",
     *     summary="新增發票資料",
     *     description="新增發票資料",
     *     operationId="createinvoiceinfo",
     *     tags={"base_invoiceinfo"},
     *     @OA\Parameter(name="period_start",in="query",required=true,description="期別_起", @OA\Schema(type="string")),
     *     @OA\Parameter(name="period_end",in="query",required=true,description="期別_迄",@OA\Schema(type="string")),
     *     @OA\Parameter(name="series",in="query",required=true,description="序號",@OA\Schema(type="string")),
     *     @OA\Parameter(name="invoice_type",in="query",required=true,description="發票類型", @OA\Schema(type="string")),
     *     @OA\Parameter(name="track_code",in="query",required=true,description="字軌代碼",@OA\Schema(type="string")),
     *     @OA\Parameter(name="start_number",in="query",required=true,description="發票起始號碼", @OA\Schema(type="string")),
     *     @OA\Parameter(name="end_number",in="query",required=true, description="發票截止號碼",@OA\Schema(type="string")),
     *     @OA\Parameter(name="effective_startdate",in="query",required=true,description="適用起始日期",@OA\Schema(type="string")),
     *     @OA\Parameter(name="effective_enddate",in="query",required=false,description="適用截止日期",@OA\Schema(type="string")),
     *     @OA\Parameter(name="is_valid",in="query",required=true,description="是否有效", @OA\Schema(type="string", example=1)),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="period_start", type="string", example="2025-01"),
     *             @OA\Property(property="period_end", type="string", example="2025-02"),
     *             @OA\Property(property="series", type="string", example="001"),
     *             @OA\Property(property="invoice_type", type="string", example="1"),
     *             @OA\Property(property="track_code", type="string", example="AQ"),
     *             @OA\Property(property="start_number", type="string", example="0000000001"),    
     *             @OA\Property(property="end_number", type="string", example="0000000050"),
     *             @OA\Property(property="effective_startdate", type="date", example="2025/01/01"),
     *             @OA\Property(property="effective_enddate", type="date", example="2025/02/28"),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="單據發票失敗"
     *     )
     * )
     */
    // 儲存發票資料
    public function store(Request $request)
    {
        try{
            //必填欄位未填寫
            if (!$request->has(['uuid', 'period_start', 'period_end', 'series', 'invoice_type', 'track_code', 'start_number', 'end_number', 'effective_startdate', 'is_valid'])) || empty($request['uuid'])) {
                return response()->json([
                    'status' => true,
                    'message' => '必填欄位未填寫',
                    'output' => null
                ], 200);
            }

            //發票期別起迄不能相同
            if ($request['period_start'] == $request['period_end']) {
                return response()->json([
                    'status' => true,
                    'message' => '發票期別起迄不能相同',
                    'output' => null
                ], 200);
            }

            //期別起需要是民國年月的格式
            if (!preg_match('/^0?\d{2,3}[\/](0?[1-9]|1[0-2])$/', $request['period_start']) || !preg_match('/^0?\d{2,3}[\/](0?[1-9]|1[0-2])$/', $request['period_end'])) {
                return response()->json([
                    'status' => true,
                    'message' => '期別格式錯誤，請使用民國年月的格式(例如：113/01)',
                    'output' => null
                ], 200);
            }

            //發票號碼需要8碼
            if (strlen($request['start_number']) != 8 || strlen($request['end_number']) != 8) {
                return response()->json([
                    'status' => true,
                    'message' => '發票號碼必須是8碼',
                    'output' => null
                ], 200);
            }

            //發票號碼數值相減需要等於50
            //if ((intval(substr($request['end_number'],-2)) - intval(substr($request['start_number'],-2)))+1 != 50) {
            //    return response()->json([
            //        'status' => true,
            //        'message' => '發票號碼區間必須等於50',
            //        'output' => null
            //    ], 200);
            //}

            //發票號碼起需要0結尾
            if (substr($request['start_number'], -1) != '0') {
                return response()->json([
                    'status' => true,
                    'message' => '發票號碼起需要0結尾',
                    'output' => null
                ], 200);
            }

            //發票號碼迄需要9結尾
            if (substr($request['end_number'], -1) != '9') {
                return response()->json([
                    'status' => true,
                    'message' => '發票號碼迄需要0結尾',
                    'output' => null
                ], 200);
            }

            //同一個期別起迄，同一個發票號碼區間只能有一筆資料
            $existingInvoiceInfo = InvoiceInfo::where('period_start', $request['period_start'])
                ->where('period_end', $request['period_end'])
                ->where('start_number', $request['start_number'])
                ->where('end_number', $request['end_number'])
                ->first();
            if ($existingInvoiceInfo) {
                return response()->json([
                    'status' => true,
                    'message' => '同一個期別，同一個發票號碼區間只能有一筆資料',
                    'output' => null
                ], 200);
            }

            // 建立發票資料
            $InvoiceInfo = InvoiceInfo::create([
                'period_start'        => $request['period_start'],
                'period_end'          => $request['period_end'],
                'invoice_type'        => $request['invoice_type'],
                'track_code'          => $request['track_code'],
                'start_number'        => $request['start_number'],
                'end_number'          => $request['end_number'],
                'effective_startdate' => $request['effective_startdate'],
                'effective_enddate'   => $request['effective_enddate']?? null,
                'is_valid'            => $request['is_valid']
            ]);
    
            if (!$InvoiceInfo) {
                return response()->json([
                    'status' => true,
                    'message' => '資料建立失敗',
                    'output' => null
                ], 404);
            }
    
            return response()->json([
                'status' => true,
                'message' => 'success',
                'output' => $InvoiceInfo
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
     * @OA\POST(
     *     path="/api/updateinvoiceinfo",
     *     summary="更新發票資料",
     *     description="更新發票資料",
     *     operationId="updateinvoiceinfo",
     *     tags={"base_invoiceinfo"},
     *     @OA\Parameter(name="period_start",in="query",required=true,description="期別_起", @OA\Schema(type="string")),
     *     @OA\Parameter(name="period_end",in="query",required=true,description="期別_迄",@OA\Schema(type="string")),
     *     @OA\Parameter(name="series",in="query",required=true,description="序號",@OA\Schema(type="string")),
     *     @OA\Parameter(name="invoice_type",in="query",required=true,description="發票類型", @OA\Schema(type="string")),
     *     @OA\Parameter(name="track_code",in="query",required=true,description="字軌代碼",@OA\Schema(type="string")),
     *     @OA\Parameter(name="start_number",in="query",required=true,description="發票起始號碼", @OA\Schema(type="string")),
     *     @OA\Parameter(name="end_number",in="query",required=true, description="發票截止號碼",@OA\Schema(type="string")),
     *     @OA\Parameter(name="effective_startdate",in="query",required=true,description="適用起始日期",@OA\Schema(type="string")),
     *     @OA\Parameter(name="effective_enddate",in="query",required=false,description="適用截止日期",@OA\Schema(type="string")),
     *     @OA\Parameter(name="is_valid",in="query",required=true,description="是否有效", @OA\Schema(type="string", example=1)),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="period_start", type="string", example="2025-01"),
     *             @OA\Property(property="period_end", type="string", example="2025-02"),
     *             @OA\Property(property="series", type="string", example="001"),
     *             @OA\Property(property="invoice_type", type="string", example="1"),
     *             @OA\Property(property="track_code", type="string", example="AQ"),
     *             @OA\Property(property="start_number", type="string", example="0000000001"),    
     *             @OA\Property(property="end_number", type="string", example="0000000050"),
     *             @OA\Property(property="effective_startdate", type="date", example="2025/01/01"),
     *             @OA\Property(property="effective_enddate", type="date", example="2025/02/28"),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="單據發票失敗"
     *     )
     * )
     */
    // 更新發票資料
    public function update(Request $request)
    {
        try {
            //必填欄位未填寫
            if (!$request->has(['uuid', 'period_start', 'period_end', 'series', 'invoice_type', 'track_code', 'start_number', 'end_number', 'effective_startdate', 'is_valid'])) || empty($request['uuid'])) {
                return response()->json([
                    'status' => true,
                    'message' => '必填欄位未填寫',
                    'output' => null
                ], 200);
            }

            //發票期別起迄不能相同
            if ($request['period_start'] == $request['period_end']) {
                return response()->json([
                    'status' => true,
                    'message' => '發票期別起迄不能相同',
                    'output' => null
                ], 200);
            }

            //期別起需要是民國年月的格式
            if (!preg_match('/^0?\d{2,3}[\/](0?[1-9]|1[0-2])$/', $request['period_start']) || !preg_match('/^0?\d{2,3}[\/](0?[1-9]|1[0-2])$/', $request['period_end'])) {
                return response()->json([
                    'status' => true,
                    'message' => '期別格式錯誤，請使用民國年月的格式(例如：113/01)',
                    'output' => null
                ], 200);
            }

            //發票號碼需要8碼
            if (strlen($request['start_number']) != 8 || strlen($request['end_number']) != 8) {
                return response()->json([
                    'status' => true,
                    'message' => '發票號碼必須是8碼',
                    'output' => null
                ], 200);
            }

            //發票號碼數值相減需要等於50    
            //if ((intval(substr($request['end_number'],-2)) - intval(substr($request['start_number'],-2)))+1 != 50) {
            //    return response()->json([
            //        'status' => true,
            //        'message' => '發票號碼區間必須等於50',
            //        'output' => null
            //    ], 200);
            //}
            //發票號碼起需要0結尾
            if (substr($request['start_number'], -1) != '0') {
                return response()->json([
                    'status' => true,
                    'message' => '發票號碼起需要0結尾',
                    'output' => null
                ], 200);
            }
            //發票號碼迄需要9結尾
            if (substr($request['end_number'], -1) != '9') {
                return response()->json([
                    'status' => true,
                    'message' => '發票號碼迄需要9結尾',
                    'output' => null
                ], 200);
            }
            //同一個期別起迄，同一個發票號碼區間只能有一筆資料
            $existingInvoiceInfo = InvoiceInfo::where('period_start', $request['period_start'])
                ->where('period_end', $request['period_end'])
                ->where('start_number', $request['start_number'])
                ->where('end_number', $request['end_number'])
                ->where('uuid', '!=', $request['uuid']) // 排除當前更新的資料
                ->first();
            if ($existingInvoiceInfo) {
                return response()->json([
                    'status' => true,
                    'message' => '同一個期別，同一個發票號碼區間只能有一筆資料',
                    'output' => null
                ], 200);
            }
            // 更新發票資料
            $InvoiceInfo = InvoiceInfo::where('uuid', $request['uuid'])->first();
            if (!$InvoiceInfo) {
                return response()->json([
                    'status' => true,
                    'message' => '資料不存在',
                    'output' => null
                ], 404);
            }
            $InvoiceInfo->period_start = $request['period_start'];
            $InvoiceInfo->period_end = $request['period_end'];
            $InvoiceInfo->series = $request['series'];
            $InvoiceInfo->invoice_type = $request['invoice_type'];
            $InvoiceInfo->track_code = $request['track_code'];
            $InvoiceInfo->start_number = $request['start_number'];
            $InvoiceInfo->end_number = $request['end_number'];
            $InvoiceInfo->effective_startdate = $request['effective_startdate'];
            $InvoiceInfo->effective_enddate = $request['effective_enddate'] ?? null;
            $InvoiceInfo->is_valid = $request['is_valid'];
            $InvoiceInfo->update_user = 'admin'; // 假設更新者為 admin
            $InvoiceInfo->update_time = now(); // 更新時間為當前時間
            $InvoiceInfo->save();
        }catch (\Illuminate\Validation\ValidationException $e) {
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
     *     path="/api/invoiceInfo2/{period}",
     *     summary="查詢特定發票資訊",
     *     description="查詢特定發票資訊",
     *     operationId="getinvoiceinfo",
     *     tags={"base_invoiceinfo"},
     *     @OA\Parameter(
     *         name="period",
     *         in="path",
     *         required=true,
     *         description="期別",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="period_start", type="string", example="025-01"),
     *             @OA\Property(property="period_end", type="string", example="2025-02"),
     *             @OA\Property(property="series", type="string", example="001"),
     *             @OA\Property(property="invoice_type", type="string", example="1"),
     *             @OA\Property(property="track_code", type="string", example="AQ"),
     *             @OA\Property(property="start_number", type="string", example="0000000001"),    
     *             @OA\Property(property="end_number", type="string", example="0000000050"),
     *             @OA\Property(property="effective_startdate", type="date", example="2025/01/01"),
     *             @OA\Property(property="effective_enddate", type="date", example="2025/02/28"),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到發票資訊"
     *     )
     * )
     */
    // 查詢特定發票資訊(以期別查詢)
    public function show($period)
    {
        try {
            $validator = Validator::make(['period_start' => $period], [
                'period_start' => 'required|string|max:7',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => '資料驗證失敗',
                    'errors' => $validator->errors()
                ], 200);
            }
            // 查詢特定發票資訊(以期別查詢，只要起迄其中符合即可)
            $sql = "select  *
                    from invoice_info
                    where invoice_info.period_start = ? or invoice_info.period_end = ? and is_valid = '1'";

            $results = DB::select($sql, [$period, $period]);

            if (!$results) {
                return response()->json([
                    'status' => true,
                    'message' => '查無資料',
                    'output' => null
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'success',
                'output' => $results
            ], 200);
        } catch (\Exception $e) {
            Log::error('查詢資料錯誤：' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        }

    }
    /**
     * @OA\GET(
     *     path="/api/invoiceInfo1/valid",
     *     summary="查詢所有有效發票資訊(含關鍵字查詢，開立起始日期、開立迄止日期、發票類型、發票號碼)",
     *     description="查詢所有有效發票資訊(含關鍵字查詢，開立起始日期、開立迄止日期、發票類型、發票號碼)",
     *     operationId="getallinvoiceinfos",
     *     tags={"base_invoiceinfo"},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         required=false,
     *         description="關鍵字查詢",
     *         @OA\Schema(type="string")
     *     ),
    * @OA\Response(
    *     response=200,
    *     description="成功取得分頁供應商清單",
    *     @OA\JsonContent(
    *         type="object",
    *         @OA\Property(property="atPage", type="integer", example=1),
    *         @OA\Property(property="total", type="integer", example=10),
    *         @OA\Property(property="totalPages", type="integer", example=1),
    *         @OA\Property(
    *             property="data",
    *             type="array",
    *             @OA\Items(
    *                 type="object",
    *                 @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
    *                 @OA\Property(property="period_start", type="string", example="025-01"),
    *                 @OA\Property(property="period_end", type="string", example="2025-02"),
    *                 @OA\Property(property="series", type="string", example="001"),
    *                 @OA\Property(property="invoice_type", type="string", example="1"),
    *                 @OA\Property(property="track_code", type="string", example="AQ"),
    *                 @OA\Property(property="start_number", type="string", example="0000000001"),    
    *                 @OA\Property(property="end_number", type="string", example="0000000050"),
    *                 @OA\Property(property="effective_startdate", type="date", example="2025/01/01"),
    *                 @OA\Property(property="effective_enddate", type="date", example="2025/02/28"),
    *                 @OA\Property(property="is_valid", type="string", example="1"),
    *                 @OA\Property(property="create_user", type="string", example="admin"),
    *                 @OA\Property(property="update_user", type="string", example="admin"),
    *                 @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
    *                 @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
    *             )
    *         )
    *     )
    * ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到有效發票資訊"
     *     )
     * )
     */
    // 查詢所有有效發票資訊
    public function getvaildinvoiceinfo(Request $request)
    {    
        try {
            $pdo = getPDOConnection();
            $keyword = $request->query('keyword'); // 可為 null
            $page = $request->query('page'); // 當前頁碼
            $pageSize = $request->query('pageSize'); // 一頁顯示幾筆數值
            $page = $page ? (int)$page : 1; // 預設為第 1 頁
            $pageSize = $pageSize ? (int)$pageSize : 30; // 預設每頁顯示 30 筆資料    
            //查詢目前頁數的資料
             $offset = ($page - 1) * $pageSize;
            //LIMIT 30：每次最多回傳 30 筆資料
            //OFFSET 0：從第 0 筆開始取，也就是第一頁的第 1 筆
            //LIMIT 30 OFFSET 0  -- 取第 1~30 筆
            //LIMIT 30 OFFSET 30 -- 取第 31~60 筆
            //LIMIT 30 OFFSET 60 -- 取第 61~90 筆
            $sql_data = "select  *
                        from invoice_info
                        where invoice_info.is_valid = '1'  
                        and (  invoice_info.invoice_type LIKE ?
                             OR invoice_info.start_number LIKE ?
                             OR invoice_info.end_number LIKE ?
                             OR invoice_info.period_start LIKE ?
                             OR invoice_info.period_end LIKE ?)
                        order by invoice_info.update_time, invoice_info.create_time asc
                        LIMIT ? OFFSET ?;";
            $likeKeyword = '%' . $keyword . '%';
            $InvoiceInfo = DB::select($sql_data, [$likeKeyword, $likeKeyword,$likeKeyword, $likeKeyword, $likeKeyword, $pageSize, $offset]);

            //取得總筆數與總頁數   
            $sql_count = "
                    SELECT COUNT(*) as total
                    from invoice_info
                        where invoice_info.is_valid = '1'  
                        and (   invoice_info.invoice_type LIKE ?
                             OR invoice_info.start_number LIKE ?
                             OR invoice_info.end_number LIKE ?
                             OR invoice_info.period_start LIKE ?
                             OR invoice_info.period_end LIKE ?)
                        order by invoice_info.update_time, invoice_info.create_time asc
                ";
            $stmt = $pdo->prepare($sql_count);
            $stmt->execute([$likeKeyword, $likeKeyword,$likeKeyword, $likeKeyword, $likeKeyword]);
            $total = $stmt->fetchColumn();
            $totalPages = ceil($total / $pageSize); // 計算總頁數    

            


            if (!$InvoiceInfo) {
                return response()->json([
                    'status' => true,
                    'atPage' => $page,
                    'total' => $total,
                    'totalPages' => $totalPages,                
                    'message' => '有效發票資訊未找到',
                    'output'    => null
                ], 404);
            }
            return response()->json([                
                'status' => true,
                'atPage' => $page,
                'total' => $total,
                'totalPages' => $totalPages,                
                'message' => 'success',
                'output'    => $InvoiceInfo
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
            Log::error('資料錯誤：' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        } 
    }
    /**
     * @OA\patch(
     *     path="/api/invoiceinfo/{uuid}/disable",
     *     summary="刪除特定發票字軌資訊",
     *     description="刪除特定發票字軌資訊",
     *     operationId="deleteinvoiceinfo",
     *     tags={"base_invoiceinfo"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="發票字軌uuid",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="period_start", type="string", example="025-01"),
     *             @OA\Property(property="period_end", type="string", example="2025-02"),
     *             @OA\Property(property="series", type="string", example="001"),
     *             @OA\Property(property="invoice_type", type="string", example="1"),
     *             @OA\Property(property="track_code", type="string", example="AQ"),
     *             @OA\Property(property="start_number", type="string", example="0000000001"),    
     *             @OA\Property(property="end_number", type="string", example="0000000050"),
     *             @OA\Property(property="effective_startdate", type="date", example="2025/01/01"),
     *             @OA\Property(property="effective_enddate", type="date", example="2025/02/28"),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )   
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到發票"
     *     )
     * )
     */
        // 🔍 刪除特定發票，以uuid，以uuid
    public function disable($uuid)
    {
        try {
            $InvoiceInfo = InvoiceInfo::where('uuid', $uuid)->where('is_valid','1')->first();
            
            if (!$InvoiceInfo) {
                return response()->json([
                    'status' => true,
                    'message' => '發票未找到',
                    'output'    => null
                ], 404);
            }

            $InvoiceInfo->is_valid = 0;
            $InvoiceInfo->update_user = 'admin';
            $InvoiceInfo->update_time = now();
            $InvoiceInfo->save();

            return response()->json([
                'status' => true,
                'message' => 'success',
                'output'    => $InvoiceInfo
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
            Log::error('資料錯誤：' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        } 
    }
    /**
     * @OA\get(
     *     path="/api/invoiceinfo/showconst",
     *     summary="列出所有發票字軌需要的常用(下拉、彈窗)",
     *     description="列出所有發票字軌需要的常用(下拉、彈窗)",
     *     operationId="show_invoiceinfo_aLL_const",
     *     tags={"base_invoiceinfo"},
     *     @OA\Response(
     *         response=200,
     *         description="成功"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="發票字軌需要的常用未找到"
     *     )
     * )
     */
    // 列出所有發票字軌需要的常用(下拉、彈窗)
    public function showconst($constant='all'){
        // 查詢 '發票類型' 的資料
        $SysCode = SysCode::where('param_sn', '08')->where('is_valid','1')->get();
        
        try {
            // 檢查是否有結果
            if ($SysCode->isEmpty() ) {
                return response()->json([
                    'status' => false,
                    'message' => '常用資料未找到',
                    'InvoiceOption' => null
                ], 404);
            }
    
            // 返回查詢結果
            return response()->json([
                'status' => true,
                'message' => 'success',
                'InvoiceOption' => $SysCode
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
