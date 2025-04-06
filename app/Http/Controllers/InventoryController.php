<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;

class InventoryController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/createInventory",
     *     summary="新增庫別資訊",
     *     description="新增庫別資訊",
     *     operationId="createInventory",
     *     tags={"Base_Inventory"},
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
        // 驗證請求
         $validated = $request->validate([
             'inventory_no'     => 'required|string|max:255|unique:inventory,inventory_no',
             'inventory_nm'     => 'required|string|max:255',
             'inventory_qty'     => 'required|integer|max:10000000',
             'lot_num'     => 'nullable|string|max:255',
             'safety_stock'     => 'required|integer|max:10000000',
             'lastStock_receiptdate'     => 'nullable|string',
             'is_valid'    => 'required|boolean'
         ]);

        // 建立庫別資料
        $Inventory = Inventory::create([
            'uuid'                    => Str::uuid(),  // 自動生成 UUID
            'inventory_no'             => $validated['inventory_no'],
            'inventory_nm'             => $validated['inventory_nm'],
            'inventory_qty'            => $validated['inventory_qty'],
            'lot_num'                  => $validated['lot_num']?? null,
            'safety_stock'            => $validated['safety_stock'],
            'lastStock_receiptdate'    => $validated['lastStock_receiptdate'] ?? null,
            'is_valid'                 => $validated['is_valid']
        ]);

        // 回應 JSON
        if (!$Inventory) {
            return response()->json([
                'status' => false,
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
    }
    /**
     * @OA\GET(
     *     path="/api/Inventory/{InventoryNO}",
     *     summary="查詢特定庫別資訊",
     *     description="查詢特定庫別資訊",
     *     operationId="getInventory",
     *     tags={"Base_Inventory"},
     *     @OA\Parameter(
     *         name="InventoryNO",
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
    public function show($InventoryNO)
    {
        $Inventory = Inventory::findByInventoryNO($InventoryNO);
        
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
    }
    /**
     * @OA\GET(
     *     path="/api/Inventorys/Valid",
     *     summary="查詢所有有效庫別資訊",
     *     description="查詢所有有效庫別資訊",
     *     operationId="GetAllInventory",
     *     tags={"Base_Inventory"},
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
    public function getVaildInventory()
    {
        $Inventory = Inventory::where('is_valid', '1')->get();
        //$Inventory = Inventory::getValidInventory();
        if ($Inventory->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => '未找到有效庫別',
                'output'    => null
            ], 404);
        }
        return response()->json([                
            'status' => true,
            'message' => 'success',
            'output'    => $Inventory
        ],200);        
    }
    /**
     * @OA\patch(
     *     path="/api/Inventory/{InventoryNO}/disable",
     *     summary="刪除特定庫別資訊",
     *     description="刪除特定庫別資訊",
     *     operationId="DeleteInventory",
     *     tags={"Base_Inventory"},
     *     @OA\Parameter(
     *         name="InventoryNO",
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
        $Inventory = Inventory::findByInventoryNO($InventoryNO);
        
        if (!$Inventory) {
            return response()->json([
                'status' => false,
                'message' => '庫別未找到',
                'output'    => null
            ], 404);
        }

        $Inventory->IsValid = 0;
        $Inventory->UpdateUser = 'admin';
        $Inventory->UpdateTime = now();
        $Inventory->save();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'output'    => $Inventory
        ], 200);
    }
}
