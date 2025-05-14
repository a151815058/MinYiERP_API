<?php

namespace App\Http\Controllers;

use App\Models\BillInfo;
use App\Models\SysCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
require_once base_path('app/Models/connect.php'); 
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class BillInfoController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/createbillinfo",
     *     summary="新增單據資料",
     *     description="新增單據資料",
     *     operationId="createbillinfo",
     *     tags={"base_billinfo"},
     *     @OA\Parameter(
     *         name="bill_no",
     *         in="query",
     *         required=true,
     *         description="單據代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="bill_nm",
     *         in="query",
     *         required=true,
     *         description="單據名稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="bill_type",
     *         in="query",
     *         required=true,
     *         description="單據類型",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="bill_encode",
     *         in="query",
     *         required=true,
     *         description="單據編碼方式(1:年月日+3碼流水碼,2:手動編碼)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="bill_calc",
     *         in="query",
     *         required=true,
     *         description="單據計算方式(1:單身單筆,2:整張計算)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="auto_review",
     *         in="query",
     *         required=true,
     *         description="是否自動核准(1:是,2:否)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="gen_order",
     *         in="query",
     *         required=false,
     *         description="自動產生單據(1:自動,2:手動)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="gen_bill_type",
     *         in="query",
     *         required=false,
     *         description="產生單據類型",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="order_type",
     *         in="query",
     *         required=false,
     *         description="依照gen_bill_type動態產生欄位名稱",
     *         @OA\Schema(type="string")
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
     *             @OA\Property(property="bill_no", type="string", example="T001"),
     *             @OA\Property(property="bill_nm", type="string", example="客戶訂單"),
     *             @OA\Property(property="bill_type", type="string", example="61"),
     *             @OA\Property(property="bill_encode", type="string", example="1"),    
     *             @OA\Property(property="bill_calc", type="string", example="1"),
     *             @OA\Property(property="auto_review", type="string", example="1"),
     *             @OA\Property(property="gen_order", type="string", example="1"),
     *             @OA\Property(property="gen_bill_type", type="string", example="1"),
     *             @OA\Property(property="order_type", type="string", example="1"),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="單據建立失敗"
     *     )
     * )
     */
    // 儲存付款條件
    public function store(Request $request)
    {
        try {
            // 驗證請求
            $validator = Validator::make($request->all(),[
                'bill_no'     => 'required|string|max:255|unique:billinfo,bill_no',
                'bill_nm'     => 'required|string|max:255',
                'bill_type'   => 'required|string|max:10',
                'bill_encode' => 'required|string|max:10',
                'bill_calc'   => 'required|integer|max:10',
                'auto_review' => 'required|integer|max:10',
                'gen_order'   => 'nullable|string|max:10',
                'gen_bill_type'   => 'nullable|string|max:10',
                //'order_type'  => 'nullable|integer|max:10',
                'note'       => 'nullable|string|max:255',
                'is_valid'    => 'required|string'
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => '資料驗證失敗',
                    'errors' => $validator->errors()
                ], 200);
            }

            //判斷order_type
            //單據類別=客戶訂單=>自動產生"銷貨單"，所以gen_bill_type需存"71"
            //單據類別=銷貨單=>自動產生"結帳單"，所以gen_bill_type需存"81"
            //單據類別=採購單=>自動產生"進貨單"，所以gen_bill_type需存"51" 
            if ($request['bill_type'] == '61') {
                $request['gen_bill_type'] = '71';
                $request['order_type'] = '銷貨單';
            } elseif ($request['bill_type'] == '71') {
                $request['gen_bill_type'] = '81';
                $request['order_type'] = '結帳單';
            } elseif ($request['bill_type'] == '採購單') {
                $request['gen_bill_type'] = '51';
                $request['order_type'] = '進貨單';
            }
    
            // 建立單據資料
            $BillInfo = BillInfo::create([
                'bill_no'     => $request['bill_no'],
                'bill_nm'     => $request['bill_nm'],
                'bill_type'   => $request['bill_type'],
                'bill_encode' => $request['bill_encode'],
                'bill_calc'   => $request['bill_calc'],
                'auto_review' => $request['auto_review'],
                'gen_order'   => $request['gen_order']?? null,
                'gen_bill_type'   => $request['gen_bill_type']?? null,
                'order_type'  => $request['order_type']?? null,
                'note'       => $request['note'] ?? null,
                'is_valid'    => $request['is_valid']
            ]);
    
            if (!$BillInfo) {
                return response()->json([
                    'status' => true,
                    'message' => '單據資料建立失敗',
                    'output' => null
                ], 404);
            }
    
            return response()->json([
                'status' => true,
                'message' => 'success',
                'output' => $BillInfo
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
     *     path="/api/billinfo/{billno}",
     *     summary="查詢特定單據資訊",
     *     description="查詢特定單據資訊",
     *     operationId="getbillinfo",
     *     tags={"base_billinfo"},
     *     @OA\Parameter(
     *         name="billno",
     *         in="path",
     *         required=true,
     *         description="單據代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="bill_no", type="string", example="T001"),
     *             @OA\Property(property="bill_nm", type="string", example="客戶訂單"),
     *             @OA\Property(property="bill_type", type="string", example="61"),
     *             @OA\Property(property="bill_encode", type="string", example="1"),    
     *             @OA\Property(property="bill_calc", type="string", example="1"),
     *             @OA\Property(property="auto_review", type="string", example="1"),
     *             @OA\Property(property="gen_order", type="string", example="1"),
     *             @OA\Property(property="gen_bill_type", type="string", example="1"),
     *             @OA\Property(property="order_type", type="string", example="1"),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到單據資訊"
     *     )
     * )
     */
    // 🔍 查詢單一付款條件
    public function show($BillNo)
    {
        try {
            $BillNo = BillInfo::findByBillNo($BillNo)->where('is_valid','1' )->first();
            
            if (!$BillNo) {
                return response()->json([
                    'status' => true,
                    'message' => '單據未找到',
                    'output'    => null
                ], 404);
            }

            return response()->json([                
                'status' => true,
                'message' => 'success',
                'output'    => $BillNo
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
            Log::error('單據資料錯誤：' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        }
    }
    /**
     * @OA\GET(
     *     path="/api/billinfo1/valid",
     *     summary="查詢所有有效單據資訊(含關鍵字查詢，單據代碼、單據名稱)",
     *     description="查詢所有有效單據資訊(含關鍵字查詢，單據代碼、單據名稱)",
     *     operationId="getallbills",
     *     tags={"base_billinfo"},
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
    *             		@OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
    *             		@OA\Property(property="bill_no", type="string", example="T001"),
    *             		@OA\Property(property="bill_nm", type="string", example="客戶訂單"),
    *             		@OA\Property(property="bill_type", type="string", example="61"),
    *             		@OA\Property(property="bill_encode", type="string", example="1"),    
    *             		@OA\Property(property="bill_calc", type="string", example="1"),
    *             		@OA\Property(property="auto_review", type="string", example="1"),
    *             		@OA\Property(property="gen_order", type="string", example="1"),
    *             		@OA\Property(property="gen_bill_type", type="string", example="1"),
    *             		@OA\Property(property="order_type", type="string", example="1"),
    *             		@OA\Property(property="note", type="string", example=""),
    *             		@OA\Property(property="is_valid", type="string", example="1"),
    *             		@OA\Property(property="create_user", type="string", example="admin"),
    *             		@OA\Property(property="update_user", type="string", example="admin"),
    *             		@OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
    *             		@OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
    *             )
    *         )
    *     )
    * ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到有效單據資訊"
     *     )
     * )
     */
    // 🔍 查詢所有有效單據(含關鍵字查詢)
    public function getvalidbillnos(Request $request)
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

            $sql = "select  *
                        from billinfo
                        where billinfo.is_valid = '1'  
                        and ( billinfo.bill_no LIKE ? OR billinfo.bill_nm LIKE ?)
                        order by update_time,create_time asc
                        LIMIT ? OFFSET ?;";
            $likeKeyword = '%' . $keyword . '%';

            $BillInfo = DB::select($sql, [$likeKeyword, $likeKeyword, $pageSize, $offset]);


            //取得總筆數與總頁數   
            $sql_count = "
                SELECT COUNT(*) as total
                from billinfo
                where billinfo.is_valid = '1'  
                  and ( billinfo.bill_no LIKE ? OR billinfo.bill_nm LIKE ?)
                 order by update_time,create_time asc;
                ";
            $stmt = $pdo->prepare($sql_count);
            $stmt->execute([$likeKeyword, $likeKeyword]);
            $total = $stmt->fetchColumn();
            $totalPages = ceil($total / $pageSize); // 計算總頁數 

            if (!$BillInfo) {
                return response()->json([
                    'status' => true,
                    'atPage' => $page,
                    'total' => $total,
                    'totalPages' => $totalPages, 
                    'message' => '有效單據資訊未找到',
                    'output'    => $BillInfo
                ], 404);
            }
            return response()->json([                
                'status' => true,
                'atPage' => $page,
                'total' => $total,
                'totalPages' => $totalPages, 
                'message' => 'success',
                'output'    => $BillInfo
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
     *     path="/api/billinfo/{billno}/disable",
     *     summary="刪除特定部門資訊",
     *     description="刪除特定部門資訊",
     *     operationId="deletebillinfo",
     *     tags={"base_billinfo"},
     *     @OA\Parameter(
     *         name="billno",
     *         in="path",
     *         required=true,
     *         description="單據代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="bill_no", type="string", example="T001"),
     *             @OA\Property(property="bill_nm", type="string", example="客戶訂單"),
     *             @OA\Property(property="bill_type", type="string", example="61"),
     *             @OA\Property(property="bill_encode", type="string", example="1"),    
     *             @OA\Property(property="bill_calc", type="string", example="1"),
     *             @OA\Property(property="auto_review", type="string", example="1"),
     *             @OA\Property(property="gen_order", type="string", example="1"),
     *             @OA\Property(property="gen_bill_type", type="string", example="1"),
     *             @OA\Property(property="order_type", type="string", example="1"),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )   
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到部門"
     *     )
     * )
     */
        // 🔍 刪除特定部門
    public function disable($BillNo)
    {
        try {
            $BillNo = BillInfo::findByBillNo($BillNo)->where('is_valid','1')->first();
            
            if (!$BillNo) {
                return response()->json([
                    'status' => true,
                    'message' => '單據未找到',
                    'output'    => $BillNo
                ], 404);
            }

            $BillNo->is_valid = 0;
            $BillNo->update_user = 'admin';
            $BillNo->update_time = now();
            $BillNo->save();

            return response()->json([
                'status' => true,
                'message' => 'success',
                'output'    => $BillNo
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
            Log::error('單據資料錯誤：' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        } 
    }
    /**
     * @OA\get(
     *     path="/api/billinfo3/showconst",
     *     summary="列出所有單據需要的常用(下拉、彈窗)",
     *     description="列出所有單據需要的常用(下拉、彈窗)",
     *     operationId="Show_bill_aLL_const",
     *     tags={"base_billinfo"},
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
    // 列出所有單據需要的常用(下拉、彈窗)
    public function showconst($constant='all'){
        // 查詢 '單據管理' 的資料
        $SysCode = SysCode::where('note', '單據管理')->where('is_valid','1')->get();
        // 查詢 '單據類型=81' 的單據資料
        $BillType81 = BillInfo::where('bill_type', '81')->where('is_valid','1')->get();
        // 查詢 '單據類型=71' 的單據資料
        $BillType71 = BillInfo::where('bill_type', '71')->where('is_valid','1')->get();
        // 查詢 '單據類型=51' 的單據資料
        $BillType51 = BillInfo::where('bill_type', '51')->where('is_valid','1')->get();
        try {
            // 檢查是否有結果
            if ($SysCode->isEmpty() && $BillType81->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => '常用資料未找到',
                    'bill_typeoption' => null,
                    'bill_type81option' => null,
                    'bill_type71option' => null,
                    'bill_type51option' => null
                ], 404);
            }
    
            // 返回查詢結果
            return response()->json([
                'status' => true,
                'message' => 'success',
                'bill_typeoption' => $SysCode,
                'bill_type81option' => $BillType81,
                'bill_type71option' => $BillType71,
                'bill_type51option' => $BillType51
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
