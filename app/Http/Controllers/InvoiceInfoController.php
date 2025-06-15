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
use App\Helpers\ValidationHelper;

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
     *     @OA\Parameter(name="series",in="query",required=true,description="序號",@OA\Schema(type="string", example=001)),
     *     @OA\Parameter(name="invoice_type",in="query",required=true,description="發票類型", @OA\Schema(type="string", example=1)),
     *     @OA\Parameter(name="track_code",in="query",required=true,description="字軌代碼",@OA\Schema(type="string", example="AQ")),
     *     @OA\Parameter(name="start_number",in="query",required=true,description="發票起始號碼", @OA\Schema(type="string", example=0000000000)),
     *     @OA\Parameter(name="end_number",in="query",required=true, description="發票截止號碼",@OA\Schema(type="string", example=0000000049)),
     *     @OA\Parameter(name="effective_startdate",in="query",required=true,description="適用起始日期",@OA\Schema(type="string")),
     *     @OA\Parameter(name="effective_enddate",in="query",required=true,description="適用截止日期",@OA\Schema(type="string")),
     *     @OA\Parameter(name="is_valid",in="query",required=true,description="是否有效", @OA\Schema(type="string", example=1)),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="period_start", type="string", example="'114/01'"),
     *             @OA\Property(property="period_end", type="string", example="'114/02'"),
     *             @OA\Property(property="series", type="string", example="001"),
     *             @OA\Property(property="invoice_type", type="string", example="1"),
     *             @OA\Property(property="track_code", type="string", example="AQ"),
     *             @OA\Property(property="start_number", type="string", example="0000000001"),    
     *             @OA\Property(property="end_number", type="string", example="0000000050"),
     *             @OA\Property(property="effective_startdate", type="date", example="'2025/01/01'"),
     *             @OA\Property(property="effective_enddate", type="date", example="'2025/02/28'"),
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
        $errors1 = [];
        try{
            //開立年月起為必填欄位
            if (!$request->has(['period_start'])) {
                $errors1['period_start_err'] = '開立年月起為必填';
            }
            // 開立年月起不為空字串
            if (!ValidationHelper::isValidText($request->input('period_start'))) {
                $errors1['period_start_err'] = '開立年月起不得為空字串或*';
            }
            //開立年月起須為民國年月例如：114/01
            if (!preg_match('/^[1-9]\d{2}\/(0[1-9]|1[0-2])$/', $request->input('period_start'))) {
                $errors1['period_start_err'] = '開立年月須為民國年月格式(例如：114/01)';
            }

            //開立年月迄為必填欄位
            if (!$request->has(['period_end'])) {
                $errors1['period_end_err'] = '開立年月迄為必填';
            }
            // 開立年月迄不為空字串
            if (!ValidationHelper::isValidText($request->input('period_end'))) {
                $errors1['period_end_err'] = '開立年月迄不得為空字串或*';
            }
            //開立年月迄須為民國年月
            if (!preg_match('/^[1-9]\d{2}\/(0[1-9]|1[0-2])$/', $request->input('period_end'))) {
                $errors1['period_end_err'] = '開立年月須為民國年月格式(例如：114/02)';
            }

            //發票期別起迄不能相同
            if ($request->input('period_start') == $request->input('period_end')) {
                $errors1['period_startend_err'] = '發票期別起迄不能相同';
            }


            //發票類型為必填欄位且須發票類型需存在在參數檔中
            if (!$request->has(['invoice_type']) || !SysCode::where('param_sn', '05')->where('uuid', $request['invoice_type'])->exists()) {
                $errors1['invoice_type_err'] = '發票類型為必填且須存在於參數檔中';
            }

            //序號為必填欄位且不得超過3碼
            if (!$request->has(['series']) || strlen($request->input('series')) > 3) {
                $errors1['series_err'] = '序號為必填且不得超過3碼';
            }
            // 開立年月迄不為空字串
            if(empty($request->input('series')) || str_contains($request->input('series') , '*')  ){
                $errors1['series_err'] = '開立年月迄不得為空字串或*';
            } 
            //字軌代碼為必填欄位且為2碼
            if (!$request->has(['track_code']) || strlen($request->input('track_code')) != 2) {
                $errors1['track_code_err'] = '字軌代碼為必填且為2碼';
            }
            // 字軌代碼不為空字串
            if (!ValidationHelper::isValidText($request->input('track_code'))) {
                $errors1['track_code_err'] = ' 字軌代碼不得為空字串或*';
            }
            //發票起始號碼為必填欄位且須為8碼
            if (!$request->has(['start_number']) || strlen($request->input('start_number')) != 8) {
                $errors1['start_number_err'] = '發票起始號碼為必填且須為8碼';
            }
            // /發票起始號碼不為空字串
            if (!ValidationHelper::isValidText($request->input('start_number'))) {
                $errors1['start_number_err'] = ' 發票起始號碼不得為空字串或*';
            }
            //發票起始號碼尾數需要為0
            if (substr($request->input('start_number'), -1) != '0') {
                $errors1['start_number_err'] = '發票起始號碼尾數需要為0';
            }else {
                $request['start_number'] = $request['track_code'].$request['start_number'];
            }

            //發票截止號碼為必填欄位且須為8碼
            if (!$request->has(['end_number']) || strlen($request->input('end_number')) != 8) {
                $errors1['end_number_err'] = '發票截止號碼為必填且須為8碼';
            }
            //發票截止號碼不為空字串
            if (!ValidationHelper::isValidText($request->input('end_number'))) {
                $errors1['end_number_err'] = ' 發票截止號碼不得為空字串或*';
            }
            //發票截止號碼尾數需要為9
            if (substr($request->input('end_number'), -1) != '9') {
                $errors1['end_number_err'] = '發票截止號碼尾數需要為9';
            }else {
                $request['end_number'] = $request['track_code'].$request['end_number'];
            }

            //通用日期起為必填欄位且須為西元年年月日格式
            if (!$request->has(['effective_startdate']) || !preg_match('/^\d{4}\/(0?[1-9]|1[0-2])\/(0?[1-9]|[12][0-9]|3[01])$/', $request->input('effective_startdate'))) {
                $errors1['effective_startdate_err'] = '通用日期起為必填且須為西元年年月日格式(例如：2025/01/01)';
            }

            //通用日期迄為選填欄位且須為西元年年月日格式
            if ($request->has(['effective_enddate']) && !preg_match('/^\d{4}\/(0?[1-9]|1[0-2])\/(0?[1-9]|[12][0-9]|3[01])$/', $request->input('effective_startdate'))) {
                $errors1['effective_enddate_err'] = '通用日期迄須為西元年年月日格式(例如：2025/01/01)';
            }

            //是否有效不為空字串
            if (!ValidationHelper::isValidText($request->input('is_valid'))) {
                $errors1['is_valid_err'] = ' 是否有效不得為空字串或*';
            }


            // 如果有錯誤，回傳統一格式
            if (!empty($errors1)) {
                return response()->json([
                    'status' => false,
                    'message' => '缺少必填的欄位及欄位格式錯誤',
                    'errors' => $errors1
                ], 400);
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
     *     summary="更新發票資料(UUID)",
     *     description="更新發票資料(UUID)",
     *     operationId="updateinvoiceinfo",
     *     tags={"base_invoiceinfo"},
     *     @OA\Parameter(name="period_start",in="query",required=true,description="期別_起", @OA\Schema(type="string")),
     *     @OA\Parameter(name="period_end",in="query",required=true,description="期別_迄",@OA\Schema(type="string")),
     *     @OA\Parameter(name="series",in="query",required=true,description="序號",@OA\Schema(type="string", example=001)),
     *     @OA\Parameter(name="invoice_type",in="query",required=true,description="發票類型", @OA\Schema(type="string", example=1)),
     *     @OA\Parameter(name="track_code",in="query",required=true,description="字軌代碼",@OA\Schema(type="string", example="AQ")),
     *     @OA\Parameter(name="start_number",in="query",required=true,description="發票起始號碼", @OA\Schema(type="string", example=0000000000)),
     *     @OA\Parameter(name="end_number",in="query",required=true, description="發票截止號碼",@OA\Schema(type="string", example=0000000049)),
     *     @OA\Parameter(name="effective_startdate",in="query",required=true,description="適用起始日期",@OA\Schema(type="string")),
     *     @OA\Parameter(name="effective_enddate",in="query",required=true,description="適用截止日期",@OA\Schema(type="string")),
     *     @OA\Parameter(name="is_valid",in="query",required=true,description="是否有效", @OA\Schema(type="string", example=1)),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
    *              @OA\Property(property="period_start", type="string", example="'114/01'"),
    *              @OA\Property(property="period_end", type="string", example="'114/02'"),
     *             @OA\Property(property="series", type="string", example="001"),
     *             @OA\Property(property="invoice_type", type="string", example="1"),
     *             @OA\Property(property="track_code", type="string", example="AQ"),
     *             @OA\Property(property="start_number", type="string", example="0000000001"),    
     *             @OA\Property(property="end_number", type="string", example="0000000050"),
     *             @OA\Property(property="effective_startdate", type="date", example="'2025/01/01'"),
     *             @OA\Property(property="effective_enddate", type="date", example="'2025/02/28'"),
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
         $errors1 = [];
        try{
            //開立年月起為必填欄位
            if (!$request->has(['period_start'])) {
                $errors1['period_start_err'] = '開立年月起為必填';
            }
            // 開立年月起不為空字串
            if (!ValidationHelper::isValidText($request->input('period_start'))) {
                $errors1['period_start_err'] = '開立年月起不得為空字串或*';
            }
            //開立年月起須為民國年月例如：114/01
            if (!preg_match('/^[1-9]\d{2}\/(0[1-9]|1[0-2])$/', $request->input('period_start'))) {
                $errors1['period_start_err'] = '開立年月須為民國年月格式(例如：114/01)';
            }

            //開立年月迄為必填欄位
            if (!$request->has(['period_end'])) {
                $errors1['period_end_err'] = '開立年月迄為必填';
            }
            // 開立年月迄不為空字串
            if (!ValidationHelper::isValidText($request->input('period_end'))) {
                $errors1['period_end_err'] = '開立年月迄不得為空字串或*';
            }
            //開立年月迄須為民國年月
            if (!preg_match('/^[1-9]\d{2}\/(0[1-9]|1[0-2])$/', $request->input('period_end'))) {
                $errors1['period_end_err'] = '開立年月須為民國年月格式(例如：114/02)';
            }

            //發票期別起迄不能相同
            if ($request->input('period_start') == $request->input('period_end')) {
                $errors1['period_startend_err'] = '發票期別起迄不能相同';
            }


            //發票類型為必填欄位且須發票類型需存在在參數檔中
            if (!$request->has(['invoice_type']) || !SysCode::where('param_sn', '05')->where('uuid', $request['invoice_type'])->exists()) {
                $errors1['invoice_type_err'] = '發票類型為必填且須存在於參數檔中';
            }

            //序號為必填欄位且不得超過3碼
            if (!$request->has(['series']) || strlen($request->input('series')) > 3) {
                $errors1['series_err'] = '序號為必填且不得超過3碼';
            }
            // 開立年月迄不為空字串
            if(empty($request->input('series')) || str_contains($request->input('series') , '*')  ){
                $errors1['series_err'] = '開立年月迄不得為空字串或*';
            } 
            //字軌代碼為必填欄位且為2碼
            if (!$request->has(['track_code']) || strlen($request->input('track_code')) != 2) {
                $errors1['track_code_err'] = '字軌代碼為必填且為2碼';
            }
            // 字軌代碼不為空字串
            if (!ValidationHelper::isValidText($request->input('track_code'))) {
                $errors1['track_code_err'] = ' 字軌代碼不得為空字串或*';
            }
            //發票起始號碼為必填欄位且須為8碼
            if (!$request->has(['start_number']) || strlen($request->input('start_number')) != 8) {
                $errors1['start_number_err'] = '發票起始號碼為必填且須為8碼';
            }
            // /發票起始號碼不為空字串
            if (!ValidationHelper::isValidText($request->input('start_number'))) {
                $errors1['start_number_err'] = ' 發票起始號碼不得為空字串或*';
            }
            //發票起始號碼尾數需要為0
            if (substr($request->input('start_number'), -1) != '0') {
                $errors1['start_number_err'] = '發票起始號碼尾數需要為0';
            }else {
                $request['start_number'] = $request['track_code'].$request['start_number'];
            }

            //發票截止號碼為必填欄位且須為8碼
            if (!$request->has(['end_number']) || strlen($request->input('end_number')) != 8) {
                $errors1['end_number_err'] = '發票截止號碼為必填且須為8碼';
            }
            //發票截止號碼不為空字串
            if (!ValidationHelper::isValidText($request->input('end_number'))) {
                $errors1['end_number_err'] = ' 發票截止號碼不得為空字串或*';
            }
            //發票截止號碼尾數需要為9
            if (substr($request->input('end_number'), -1) != '9') {
                $errors1['end_number_err'] = '發票截止號碼尾數需要為9';
            }else {
                $request['end_number'] = $request['track_code'].$request['end_number'];
            }

            //通用日期起為必填欄位且須為西元年年月日格式
            if (!$request->has(['effective_startdate']) || !preg_match('/^\d{4}\/(0?[1-9]|1[0-2])\/(0?[1-9]|[12][0-9]|3[01])$/', $request->input('effective_startdate'))) {
                $errors1['effective_startdate_err'] = '通用日期起為必填且須為西元年年月日格式(例如：2025/01/01)';
            }

            //通用日期迄為選填欄位且須為西元年年月日格式
            if ($request->has(['effective_enddate']) && !preg_match('/^\d{4}\/(0?[1-9]|1[0-2])\/(0?[1-9]|[12][0-9]|3[01])$/', $request->input('effective_startdate'))) {
                $errors1['effective_enddate_err'] = '通用日期迄須為西元年年月日格式(例如：2025/01/01)';
            }

            //是否有效不為空字串
            if (!ValidationHelper::isValidText($request->input('is_valid'))) {
                $errors1['is_valid_err'] = ' 是否有效不得為空字串或*';
            }


            // 如果有錯誤，回傳統一格式
            if (!empty($errors1)) {
                return response()->json([
                    'status' => false,
                    'message' => '缺少必填的欄位及欄位格式錯誤',
                    'errors' => $errors1
                ], 400);
            }

            // 驗證 UUID 是否存在
            $InvoiceInfo = InvoiceInfo::where('uuid', $request['uuid'])->where('is_valid','1')->first();
            if (!$InvoiceInfo) {
                return response()->json([
                    'status' => true,
                    'message' => '發票未找到',
                    'output'    => null
                ], 404);
            }
            // 更新發票資料
            $InvoiceInfo->period_start = $request['period_start'];
            $InvoiceInfo->period_end = $request['period_end'];
            $InvoiceInfo->invoice_type = $request['invoice_type'];
            $InvoiceInfo->track_code = $request['track_code'];
            $InvoiceInfo->start_number = $request['start_number'];
            $InvoiceInfo->end_number = $request['end_number'];
            $InvoiceInfo->effective_startdate = $request['effective_startdate'];
            $InvoiceInfo->effective_enddate = $request['effective_enddate'] ?? null;
            $InvoiceInfo->is_valid = $request['is_valid'];
            $InvoiceInfo->update_user = 'admin'; // 假設更新人為 admin
            $InvoiceInfo->update_time = now(); // 更新時間為當前時間
            $InvoiceInfo->save();
            return response()->json([
                'status' => true,
                'message' => 'success',
                'output' => $InvoiceInfo
            ], 200);
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
     *     path="/api/invoiceInfo2/{uuid}",
     *     summary="查詢特定發票資訊",
     *     description="查詢特定發票資訊",
     *     operationId="getinvoiceinfo",
     *     tags={"base_invoiceinfo"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
    *                 @OA\Property(property="period_start", type="string", example="'114/01'"),
    *                 @OA\Property(property="period_end", type="string", example="'114/02'"),
     *             @OA\Property(property="series", type="string", example="001"),
     *             @OA\Property(property="invoice_type", type="string", example="1"),
     *             @OA\Property(property="track_code", type="string", example="AQ"),
     *             @OA\Property(property="start_number", type="string", example="0000000001"),    
     *             @OA\Property(property="end_number", type="string", example="0000000050"),
     *             @OA\Property(property="effective_startdate", type="date", example="'2025/01/01'"),
     *             @OA\Property(property="effective_enddate", type="date", example="'2025/02/28'"),
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
    public function show($UUID)
    {
        $error1=[];
        try {
            //驗證發票UUID是否存在
            if (!$UUID->has(['UUID']) || !InvoiceInfo::where('uuid', $UUID)->where('is_valid','1')->exists()) {
               $error1['uuid_err']='發票UUID為必填且須存在於資料庫中';
            }

            // 如果有錯誤，回傳統一格式
            if (!empty($errors1)) {
                return response()->json([
                    'status' => false,
                    'message' => '查詢條件不存在',
                    'errors' => $errors1
                ], 400);
            }

            // 查詢特定發票資訊(以期別查詢，只要起迄其中符合即可)
            $sql = "select  *
                    from invoice_info
                    where invoice_info.uuid =? and is_valid = '1'";

            $results = DB::select($sql, [$UUID]);

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
     *     summary="查詢所有有效發票資訊(含關鍵字查詢，開立起始日期、開立迄止日期、發票類型)",
     *     description="查詢所有有效發票資訊(含關鍵字查詢，開立起始日期、開立迄止日期、發票類型)",
     *     operationId="getallinvoiceinfos",
     *     tags={"base_invoiceinfo"},
     *     @OA\Parameter(
     *         name="period_start",
     *         in="query",
     *         required=false,
     *         description="開立年月起",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="period_end",
     *         in="query",
     *         required=false,
     *         description="開立年月迄",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="invoice_type",
     *         in="query",
     *         required=false,
     *         description="發票類型",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         required=false,
     *         description="關鍵字",
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
    *                 @OA\Property(property="period_start", type="string", example="'114/01'"),
    *                 @OA\Property(property="period_end", type="string", example="'114/01'"),
    *                 @OA\Property(property="series", type="string", example="001"),
    *                 @OA\Property(property="invoice_type", type="string", example="1"),
    *                 @OA\Property(property="track_code", type="string", example="AQ"),
    *                 @OA\Property(property="start_number", type="string", example="0000000001"),    
    *                 @OA\Property(property="end_number", type="string", example="0000000050"),
    *                 @OA\Property(property="effective_startdate", type="date", example="'2025/01/01'"),
    *                 @OA\Property(property="effective_enddate", type="date", example="'2025/02/28'"),
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
        $errors1 = [];
        try {
            $pdo = getPDOConnection();
            $period_start = $request->query('period_start'); // 可為 null
            $period_end = $request->query('period_end'); // 可為 null
            $invoice_type = $request->query('invoice_type'); // 可為 null
            $keyword = $request->query('keyword'); // 關鍵字查詢
            // 開立年月起須為民國年月格式(例如：114-01)
            //開立年月起須為民國年月例如：114/01
            if (!preg_match('/^[1-9]\d{2}\/(0[1-9]|1[0-2])$/', $request['period_start'])) {
                $errors1['period_start_err'] = '開立年月須為民國年月格式(例如：114/01)';
            }

            // 開立年月迄須為民國年月格式(例如：114-02)
            //開立年月起須為民國年月例如：114/01
            if (!preg_match('/^[1-9]\d{2}\/(0[1-9]|1[0-2])$/', $request['period_start'])) {
                $errors1['period_end_err'] = '開立年月須為民國年月格式(例如：114/02)';
            }

            // 發票類型須存在於參數檔中
            if ($invoice_type && !SysCode::where('param_sn', '05')->where('uuid', $invoice_type)->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => '發票類型須存在於參數檔中'
                ], 400);
            }
            // 關鍵字查詢
            if ($keyword && !is_string($keyword)) {
                return response()->json([
                    'status' => false,
                    'message' => '關鍵字查詢必須為字串'
                ], 400);
            }

            // 如果有錯誤，回傳統一格式
            if (!empty($errors1)) {
                return response()->json([
                    'status' => false,
                    'message' => '查詢條件錯誤',
                    'errors' => $errors1
                ], 400);
            }


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
            $InvoiceInfo = DB::select($sql_data, [$period_start, $period_end,$invoice_type, $likeKeyword, $likeKeyword, $pageSize, $offset]);

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
            $stmt->execute([$period_start, $period_end,$invoice_type, $likeKeyword, $likeKeyword]);
            $total = $stmt->fetchColumn();
            $totalPages = ceil($total / $pageSize); // 計算總頁數    

            


            if (!$InvoiceInfo) {
                return response()->json([
                    'status' => true,
                    'atPage' => $page,
                    'total' => $total,
                    'totalPages' => $totalPages,                
                    'message' => '有效發票資訊未找到',
                    'output'    => []
                ], 200);
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
        $SysCode = SysCode::where('param_sn', '05')->where('is_valid','1')->get();
        
        try {
            // 檢查是否有結果
            if ($SysCode->isEmpty() ) {
                return response()->json([
                    'status' => false,
                    'message' => '常用資料未找到',
                    'InvoiceOption' => []
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
