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
     *     tags={"Inventory"},
     *     @OA\Parameter(
     *         name="InventoryNO",
     *         in="query",
     *         required=true,
     *         description="庫別代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="InventoryNM",
     *         in="query",
     *         required=true,
     *         description="庫別名稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="InventoryQty",
     *         in="query",
     *         required=true,
     *         description="庫存數量",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="LotNum",
     *         in="query",
     *         required=true,
     *         description="批號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Safety_stock",
     *         in="query",
     *         required=true,
     *         description="安全庫存",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="LastStockReceiptDate",
     *         in="query",
     *         required=true,
     *         description="最近一次進貨日",
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
     *             @OA\Property(property="InventoryNO", type="string", example="INV001"),
     *             @OA\Property(property="InventoryNM", type="string", example="庫別1"),
     *             @OA\Property(property="InventoryQty", type="integer", example="1000"),
     *             @OA\Property(property="Safety_stock", type="integer", example="500"),
     *             @OA\Property(property="LotNum", type="string", example="LOT123"),
     *             @OA\Property(property="LastStockReceiptDate", type="string", example="2025-03-31"),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
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
             'InventoryNO'     => 'required|string|max:255|unique:inventory,InventoryNO',
             'InventoryNM'     => 'required|string|max:255',
             'InventoryQty'     => 'required|integer|max:10000000',
             'LotNum'     => 'nullable|string|max:255',
             'Safety_stock'     => 'required|integer|max:10000000',
             'LastStockReceiptDate'     => 'nullable|string',
             'IsValid'    => 'required|boolean',
             'Createuser' => 'required|string|max:255',
             'UpdateUser' => 'required|string|max:255',
         ]);

        // 建立庫別資料
        $Inventory = Inventory::create([
            'uuid'                    => Str::uuid(),  // 自動生成 UUID
            'InventoryNO'             => $validated['InventoryNO'],
            'InventoryNM'             => $validated['InventoryNM'],
            'InventoryQty'            => $validated['InventoryQty'],
            'LotNum'                  => $validated['LotNum'],
            'Safety_stock'            => $validated['Safety_stock'],
            'LastStockReceiptDate'    => $validated['LastStockReceiptDate'] ?? null,
            'IsValid'                 => $validated['IsValid'],
            'Createuser'              => $validated['Createuser'],
            'UpdateUser'              => $validated['UpdateUser'],
            'CreateTime'              => now(),  // 設定當前時間
            'UpdateTime'              => now(),
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
     *     tags={"Inventory"},
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
     *             @OA\Property(property="InventoryNO", type="string", example="INV001"),
     *             @OA\Property(property="InventoryNM", type="string", example="庫別1"),
     *             @OA\Property(property="InventoryQty", type="integer", example="1000"),
     *             @OA\Property(property="Safety_stock", type="integer", example="500"),
     *             @OA\Property(property="LotNum", type="string", example="LOT123"),
     *             @OA\Property(property="LastStockReceiptDate", type="string", example="2025-03-31"),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
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
     *     path="/api/Inventory/Valid",
     *     summary="查詢所有有效庫別資訊",
     *     description="查詢所有有效庫別資訊",
     *     operationId="GetAllInventory",
     *     tags={"Inventory"},
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="InventoryNO", type="string", example="INV001"),
     *             @OA\Property(property="InventoryNM", type="string", example="庫別1"),
     *             @OA\Property(property="InventoryQty", type="integer", example="1000"),
     *             @OA\Property(property="Safety_stock", type="integer", example="500"),
     *             @OA\Property(property="LotNum", type="string", example="LOT123"),
     *             @OA\Property(property="LastStockReceiptDate", type="string", example="2025-03-31"),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
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
        $Inventory = Inventory::where('IsValid', '1')->get();
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
     *     tags={"Inventory"},
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
     *             @OA\Property(property="InventoryNO", type="string", example="INV001"),
     *             @OA\Property(property="InventoryNM", type="string", example="庫別1"),
     *             @OA\Property(property="InventoryQty", type="integer", example="1000"),
     *             @OA\Property(property="Safety_stock", type="integer", example="500"),
     *             @OA\Property(property="LotNum", type="string", example="LOT123"),
     *             @OA\Property(property="LastStockReceiptDate", type="string", example="2025-03-31"),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
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
        $Inventory->UpdateTime = now();
        $Inventory->save();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'output'    => $Inventory
        ], 200);
    }
}
