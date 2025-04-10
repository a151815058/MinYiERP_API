<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoiceInfo;
use App\Models\SysCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InvoiceInfoController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/createInvoiceInfo",
     *     summary="新增發票資料",
     *     description="新增發票資料",
     *     operationId="createInvoiceInfo",
     *     tags={"Base_InvoiceInfo"},
     *     @OA\Parameter(
     *         name="period_start",
     *         in="query",
     *         required=true,
     *         description="期別_起",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="period_end",
     *         in="query",
     *         required=true,
     *         description="期別_迄",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="track_code",
     *         in="query",
     *         required=true,
     *         description="字軌代碼",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="start_number",
     *         in="query",
     *         required=true,
     *         description="發票起始號碼",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="end_number",
     *         in="query",
     *         required=true,
     *         description="發票截止號碼",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="effective_startdate",
     *         in="query",
     *         required=true,
     *         description="適用起始日期",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="effective_enddate",
     *         in="query",
     *         required=false,
     *         description="適用截止日期",
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
     *             @OA\Property(property="period_start", type="string", example="2025-01"),
     *             @OA\Property(property="period_end", type="string", example="2025-02"),
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
            $validator = Validator::make($request->all(),[
                'period_start' => 'required|string|max:7',
                'period_end' => 'required|string|max:7',
                'track_code' => 'required|string|max:2',
                'start_number' => 'required|string',
                'end_number' => 'required|string',
                'effective_startdate' => 'required|date',
                'effective_enddate' => 'required|date',
                'is_valid' => 'required|boolean',
            ]);
            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => '資料驗證失敗',
                    'errors' => $validator->errors()
                ], 200);
            }

            //發票期別起迄不能相同
            if ($request['period_start'] == $request['period_end']) {
                return response()->json([
                    'status' => false,
                    'message' => '發票期別起迄不能相同',
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
                    'status' => false,
                    'message' => '同一個期別，同一個發票號碼區間只能有一筆資料',
                    'output' => null
                ], 200);
            }

            //發票號碼需要8碼
            if (strlen($request['start_number']) != 8 || strlen($request['end_number']) != 8) {
                return response()->json([
                    'status' => false,
                    'message' => '發票號碼必須是8碼',
                    'output' => null
                ], 200);
            }

            //發票號碼區間需要大於0
            if (intval($request['end_number']) - intval($request['start_number']) <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => '發票號碼區間必須大於0',
                    'output' => null
                ], 200);
            }



            // 建立發票資料
            $InvoiceInfo = InvoiceInfo::create([
                'period_start'     => $request['period_start'],
                'period_end'     => $request['period_end'],
                'track_code'   => $request['track_code'],
                'start_number' => $request['start_number'],
                'end_number'   => $request['end_number'],
                'effective_startdate' => $request['effective_startdate'],
                'effective_enddate'   => $request['effective_enddate']?? null,
                'is_valid'    => $request['is_valid']
            ]);
    
            if (!$InvoiceInfo) {
                return response()->json([
                    'status' => false,
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
     * @OA\GET(
     *     path="/api/InvoiceInfo/{period}",
     *     summary="查詢特定發票資訊",
     *     description="查詢特定發票資訊",
     *     operationId="getInvoiceInfo",
     *     tags={"Base_InvoiceInfo"},
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
     *             @OA\Property(property="period_start", type="string", example="2025-01"),
     *             @OA\Property(property="period_end", type="string", example="2025-02"),
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
                    from invoiceinfo
                    where invoiceinfo.period_start = ? or invoiceinfo.period_end = ? and is_valid = '1'";

            $results = DB::select($sql, [$period, $period]);

            if (!$results) {
                return response()->json([
                    'status' => false,
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
     *     path="/api/InvoiceInfos/Valid",
     *     summary="查詢所有有效發票資訊",
     *     description="查詢所有有效發票資訊",
     *     operationId="GetAllInvoiceInfos",
     *     tags={"Base_InvoiceInfo"},
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="period_start", type="string", example="2025-01"),
     *             @OA\Property(property="period_end", type="string", example="2025-02"),
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
     *         description="未找到有效發票資訊"
     *     )
     * )
     */
    // 查詢所有有效發票資訊
    public function getVaildInvoiceInfo()
    {
        try {
            $InvoiceInfo = InvoiceInfo::getValidInvoiceInfo();
            if (!$InvoiceInfo) {
                return response()->json([
                    'status' => false,
                    'message' => '有效發票資訊未找到',
                    'output'    => null
                ], 404);
            }
            return response()->json([                
                'status' => true,
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
     *     path="/api/InvoiceInfo/{uuid}/disable",
     *     summary="刪除特定發票字軌資訊",
     *     description="刪除特定發票字軌資訊",
     *     operationId="DeleteInvoiceInfo",
     *     tags={"Base_InvoiceInfo"},
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
     *             @OA\Property(property="period_start", type="string", example="2025-01"),
     *             @OA\Property(property="period_end", type="string", example="2025-02"),
     *             @OA\Property(property="track_code", type="string", example="AQ"),
     *             @OA\Property(property="start_number", type="string", example="0000000001"),    
     *             @OA\Property(property="end_number", type="string", example="0000000050"),
     *             @OA\Property(property="effective_startdate", type="date", example="2025/01/01"),
     *             @OA\Property(property="effective_enddate", type="date", example="2025/02/28"),
     *             @OA\Property(property="is_valid", type="string", example="0"),
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
            $InvoiceInfo = InvoiceInfo::where('uuid', $uuid)->first();
            
            if (!$InvoiceInfo) {
                return response()->json([
                    'status' => false,
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
}
