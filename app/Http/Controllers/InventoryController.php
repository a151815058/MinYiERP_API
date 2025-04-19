<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/createinventory",
     *     summary="新增庫別資訊",
     *     description="新增庫別資訊",
     *     operationId="createinventory",
     *     tags={"base_inventory"},
     *     @OA\Parameter(
     *         name="inventory_no",
     *         in="query",
     *         required=true,
     *         description="庫別代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="inventory_nm",
     *         in="query",
     *         required=true,
     *         description="庫別名稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="inventory_qty",
     *         in="query",
     *         required=true,
     *         description="庫存數量",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="lot_num",
     *         in="query",
     *         required=false,
     *         description="批號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="safety_stock",
     *         in="query",
     *         required=true,
     *         description="安全庫存",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="lastStock_receiptdate",
     *         in="query",
     *         required=true,
     *         description="最近一次進貨日",
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
     *             @OA\Property(property="inventory_no", type="string", example="INV001"),
     *             @OA\Property(property="inventory_nm", type="string", example="庫別1"),
     *             @OA\Property(property="inventory_qty", type="integer", example="1000"),
     *             @OA\Property(property="lot_num", type="string", example="LOT123"),
     *             @OA\Property(property="safety_stock", type="integer", example="500"),
     *             @OA\Property(property="lastStock_receiptdate", type="string", example="2025-03-31"),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="建立庫別失敗",
     *     )
     * )
     */
    // 儲存庫別資料
    public function store(Request $request)
    {
        try {
            // 驗證請求
            $validator = Validator::make($request->all(),[
                'inventory_no'     => 'required|string|max:255|unique:inventory,inventory_no',
                'inventory_nm'     => 'required|string|max:255',
                'inventory_qty'     => 'required|integer|max:10000000',
                'lot_num'     => 'nullable|string|max:255',
                'safety_stock'     => 'required|integer|max:10000000',
                'lastStock_receiptdate'     => 'nullable|string',
                'is_valid'    => 'required|boolean'
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => true,
                    'message' => '資料驗證失敗',
                    'errors' => $validator->errors()
                ], 200);
            }

            // 建立庫別資料
            $Inventory = Inventory::create([
                'uuid'                    => Str::uuid(),  // 自動生成 UUID
                'inventory_no'             => $request['inventory_no'],
                'inventory_nm'             => $request['inventory_nm'],
                'inventory_qty'            => $request['inventory_qty'],
                'lot_num'                  => $request['lot_num']?? null,
                'safety_stock'            => $request['safety_stock'],
                'lastStock_receiptdate'    => $request['lastStock_receiptdate'] ?? null,
                'is_valid'                 => $request['is_valid']
            ]);

            // 回應 JSON
            if (!$Inventory) {
                return response()->json([
                    'status' => true,
                    'message' => '庫別建立失敗',
                    'output'    => null
                ], status: 404);
            }else {
                // 回應 JSON
                return response()->json([
                    'status' => true,
                    'message' => 'success',
                    'output'    => $Inventory
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
     *     path="/api/inventory/{inventoryno}",
     *     summary="查詢特定庫別資訊",
     *     description="查詢特定庫別資訊",
     *     operationId="getinventory",
     *     tags={"base_inventory"},
     *     @OA\Parameter(
     *         name="inventoryno",
     *         in="path",
     *         required=true,
     *         description="庫別代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="inventory_no", type="string", example="INV001"),
     *             @OA\Property(property="inventory_nm", type="string", example="庫別1"),
     *             @OA\Property(property="inventory_qty", type="integer", example="1000"),
     *             @OA\Property(property="lot_num", type="string", example="LOT123"),
     *             @OA\Property(property="safety_stock", type="integer", example="500"),
     *             @OA\Property(property="lastStock_receiptdate", type="string", example="2025-03-31"),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到庫別"
     *     )
     * )
     */
    // 🔍 查詢單一庫別
    public function showno($InventoryNO)
    {
        try{
            $Inventory = Inventory::findByInventoryNO($InventoryNO)->where('is_valid', '1')->first();
        
            if (!$Inventory) {
                 return response()->json([
                     'status' => false,
                     'message' => '庫別未找到',
                     'output'    => null
                 ], 404);
             }
     
             return response()->json([                
                 'status' => true,
                 'message' => 'success',
                 'output'    => $Inventory
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
     *     path="/api/inventorys/valid",
     *     summary="查詢所有有效庫別資訊(含關鍵字查詢)",
     *     description="查詢所有有效庫別資訊(含關鍵字查詢)",
     *     operationId="getallinventory",
     *     tags={"base_inventory"},
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
     *             @OA\Property(property="inventory_no", type="string", example="INV001"),
     *             @OA\Property(property="inventory_nm", type="string", example="庫別1"),
     *             @OA\Property(property="inventory_qty", type="integer", example="1000"),
     *             @OA\Property(property="lot_num", type="string", example="LOT123"),
     *             @OA\Property(property="safety_stock", type="integer", example="500"),
     *             @OA\Property(property="lastStock_receiptdate", type="string", example="2025-03-31"),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到有效庫別"
     *     )
     * )
     */
    // 🔍 查詢所有有效庫別
    public function getvaildinventory(Request $request)
    {
        try{
            $keyword = $request->query('keyword'); // 可為 null

            // 使用 DB::select 進行關鍵字查詢
            if($keyword != null) {
                //庫別代號、庫別名稱、批號
                // 這裡使用了 SQL 的 LIKE 語法來進行模糊查詢
                $likeKeyword = '%' . $keyword . '%';
                $sql = "select  *
                        from inventory
                        where inventory.is_valid = '1'  
                        and ( inventory.inventory_no LIKE ? 
                           OR inventory.inventory_nm LIKE ?
                           OR inventory.lot_num LIKE ? )
                        order by update_time,create_time asc;";

                $Inventory = DB::select($sql, [$likeKeyword, $likeKeyword, $likeKeyword]);

            } else {
                $Inventory = Inventory::where('is_valid', '1')->get();
            }

            if (!$Inventory) {
                return response()->json([
                    'status' => true,
                    'message' => '未找到有效庫別',
                    'output'    => $Inventory
                ], 404);
            }
            return response()->json([                
                'status' => true,
                'message' => 'success',
                'output'    => $Inventory
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
     *     path="/api/inventory/{inventoryno}/disable",
     *     summary="刪除特定庫別資訊",
     *     description="刪除特定庫別資訊",
     *     operationId="deleteinventory",
     *     tags={"base_inventory"},
     *     @OA\Parameter(
     *         name="inventoryno",
     *         in="path",
     *         required=true,
     *         description="庫別代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="inventory_no", type="string", example="INV001"),
     *             @OA\Property(property="inventory_nm", type="string", example="庫別1"),
     *             @OA\Property(property="inventory_qty", type="integer", example="1000"),
     *             @OA\Property(property="lot_num", type="string", example="LOT123"),
     *             @OA\Property(property="safety_stock", type="integer", example="500"),
     *             @OA\Property(property="lastStock_receiptdate", type="string", example="2025-03-31"),
     *             @OA\Property(property="is_valid", type="string", example="0"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )   
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到庫別"
     *     )
     * )
     */
    // 🔍 刪除特定庫別
    public function disable($InventoryNO)
    {
        try{
            $Inventory = Inventory::findByInventoryNO($InventoryNO)->where('is_valid', '1')->first();

            if (!$Inventory) {
                return response()->json([
                    'status' => true,
                    'message' => '庫別未找到',
                    'output'    => null
                ], 404);
            }
    
            $Inventory->is_valid = 0;
            $Inventory->update_user = 'admin';
            $Inventory->update_time = now();
            $Inventory->save();
    
            return response()->json([
                'status' => true,
                'message' => 'success',
                'output'    => $Inventory
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
}
