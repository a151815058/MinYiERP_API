<?php

namespace App\Http\Controllers;

use App\Models\BillInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;

class BillInfoController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/createBillInfo",
     *     summary="新增單據資料",
     *     description="新增單據資料",
     *     operationId="createBillInfo",
     *     tags={"Base_BillInfo"},
     *     @OA\Parameter(
     *         name="BillNo",
     *         in="query",
     *         required=true,
     *         description="單據代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="BillNM",
     *         in="query",
     *         required=true,
     *         description="單據名稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="BillType",
     *         in="query",
     *         required=true,
     *         description="單據類型",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="BillEncode",
     *         in="query",
     *         required=true,
     *         description="單據編碼方式",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="BillCalc",
     *         in="query",
     *         required=true,
     *         description="單據計算方式",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="AutoReview",
     *         in="query",
     *         required=true,
     *         description="是否自動核准",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="GenOrder",
     *         in="query",
     *         required=true,
     *         description="自動產生銷貨單",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="OrderType",
     *         in="query",
     *         required=true,
     *         description="銷貨單別",
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
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="BillNo", type="string", example="T001"),
     *             @OA\Property(property="BillNM", type="string", example="客戶訂單"),
     *             @OA\Property(property="BillType", type="string", example="61"),
     *             @OA\Property(property="BillEncode", type="string", example="1"),
     *             @OA\Property(property="BillCalc", type="string", example="1"),
     *             @OA\Property(property="AutoReview", type="string", example="1"),
     *             @OA\Property(property="GenOrder", type="string", example="1"),
     *             @OA\Property(property="OrderType", type="string", example="1"),
     *             @OA\Property(property="Note", type="string", example=""),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
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
            $validated = $request->validate([
                'BillNo'     => 'required|string|max:255|unique:billinfo,BillNo',
                'BillNM'     => 'required|string|max:255',
                'BillType'   => 'required|string|max:10',
                'BillEncode' => 'required|string|max:10',
                'BillCalc'   => 'required|integer|max:10',
                'AutoReview' => 'required|integer|max:10',
                'GenOrder'   => 'required|string|max:10',
                'OrderType'  => 'required|integer|max:10',
                'Note'       => 'nullable|string|max:255',
                'IsValid'    => 'required|boolean'
            ]);
    
            // 建立單據資料
            $BillInfo = BillInfo::create([
                'BillNo'     => $validated['BillNo'],
                'BillNM'     => $validated['BillNM'],
                'BillType'   => $validated['BillType'],
                'BillEncode' => $validated['BillEncode'],
                'BillCalc'   => $validated['BillCalc'],
                'AutoReview' => $validated['AutoReview'],
                'GenOrder'   => $validated['GenOrder'],
                'OrderType'  => $validated['OrderType'],
                'Note'       => $validated['Note'] ?? null,
                'IsValid'    => $validated['IsValid']
            ]);
    
            if (!$BillInfo) {
                return response()->json([
                    'status' => false,
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
     *     path="/api/BillInfo/{BillNo}",
     *     summary="查詢特定單據資訊",
     *     description="查詢特定單據資訊",
     *     operationId="getBillInfo",
     *     tags={"Base_BillInfo"},
     *     @OA\Parameter(
     *         name="BillNo",
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
     *             @OA\Property(property="BillNo", type="string", example="T001"),
     *             @OA\Property(property="BillNM", type="string", example="客戶訂單"),
     *             @OA\Property(property="BillType", type="string", example="61"),
     *             @OA\Property(property="BillEncode", type="string", example="1"),
     *             @OA\Property(property="BillCalc", type="string", example="1"),
     *             @OA\Property(property="AutoReview", type="string", example="1"),
     *             @OA\Property(property="GenOrder", type="string", example="1"),
     *             @OA\Property(property="OrderType", type="string", example="1"),
     *             @OA\Property(property="Note", type="string", example=""),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
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
            $BillNo = BillInfo::findByBillNo($BillNo);
            
            if (!$BillNo) {
                return response()->json([
                    'status' => false,
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
     *     path="/api/BillInfos/valid",
     *     summary="查詢所有有效單據資訊",
     *     description="查詢所有有效單據資訊",
     *     operationId="GetAllBills",
     *     tags={"Base_BillInfo"},
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="BillNo", type="string", example="T001"),
     *             @OA\Property(property="BillNM", type="string", example="客戶訂單"),
     *             @OA\Property(property="BillType", type="string", example="61"),
     *             @OA\Property(property="BillEncode", type="string", example="1"),
     *             @OA\Property(property="BillCalc", type="string", example="1"),
     *             @OA\Property(property="AutoReview", type="string", example="1"),
     *             @OA\Property(property="GenOrder", type="string", example="1"),
     *             @OA\Property(property="OrderType", type="string", example="1"),
     *             @OA\Property(property="Note", type="string", example=""),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到有效單據資訊"
     *     )
     * )
     */
    // 🔍 查詢所有有效部門
    public function getValidBillNos()
    {
        try {
            $BillInfo = BillInfo::getValidBillNos();
            if (!$BillInfo) {
                return response()->json([
                    'status' => false,
                    'message' => '有效單據資訊未找到',
                    'output'    => null
                ], 404);
            }
            return response()->json([                
                'status' => true,
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
            Log::error('單據資料錯誤：' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        } 
    }
    /**
     * @OA\patch(
     *     path="/api/BillInfo/{BillNo}/disable",
     *     summary="刪除特定部門資訊",
     *     description="刪除特定部門資訊",
     *     operationId="DelteBill",
     *     tags={"Base_BillInfo"},
     *     @OA\Parameter(
     *         name="BillNo",
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
     *             @OA\Property(property="BillNo", type="string", example="T001"),
     *             @OA\Property(property="BillNM", type="string", example="客戶訂單"),
     *             @OA\Property(property="BillType", type="string", example="61"),
     *             @OA\Property(property="BillEncode", type="string", example="1"),
     *             @OA\Property(property="BillCalc", type="string", example="1"),
     *             @OA\Property(property="AutoReview", type="string", example="1"),
     *             @OA\Property(property="GenOrder", type="string", example="1"),
     *             @OA\Property(property="OrderType", type="string", example="1"),
     *             @OA\Property(property="Note", type="string", example=""),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
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
    public function disable($BillNo)
    {
        try {
            $BillNo = BillInfo::findByBillNo($BillNo);
            
            if (!$BillNo) {
                return response()->json([
                    'status' => false,
                    'message' => '單據未找到',
                    'output'    => null
                ], 404);
            }

            $BillNo->IsValid = 0;
            $BillNo->UpdateUser = 'admin';
            $BillNo->UpdateTime = now();
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
}
