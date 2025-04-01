<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
    /**
     * @OA\Get(
     *     path="/Inventory/{InventoryNO}",
     *     summary="取得單一部門資訊",
     *     description="根據部門編號查詢部門資訊",
     *     operationId="getInventoryNO",
     *     tags={"InventoryNO"},
     *     @OA\Parameter(
     *         name="deptNo",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="DeptNo", type="string", example="D001"),
     *             @OA\Property(property="DeptNM", type="string", example="資訊部"),
     *             @OA\Property(property="IsVaild", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到部門"
     *     )
     * )
     */
class InventoryController extends Controller
{
    // 儲存部門資料
    public function store(Request $request)
    {
        // 驗證請求
        // $validated = $request->validate([
        //     'InventoryNO'     => 'required|string|max:255|unique:inventory,InventoryNO',
        //     'InventoryNM'     => 'required|string|max:255',
        //     'InventoryQty'     => 'required|integer|max:10000',
        //     'LotNum'     => 'nullable|string|max:255',
        //     'Safety_stock'     => 'required|integer|max:10000',
        //     'LastStockReceiptDate'     => 'nullable|date',
        //     'IsVaild'    => 'required|boolean',
        //     'Createuser' => 'required|string|max:255',
        //     'UpdateUser' => 'required|string|max:255',
        // ]);

        // 建立庫別資料
        $Inventory = Inventory::create([
            'uuid'                    => Str::uuid(),  // 自動生成 UUID
            'InventoryNO'             => $request['InventoryNO'],
            'InventoryNM'             => $request['InventoryNM'],
            'InventoryQty'            => $request['InventoryQty'],
            'LotNum'                  => $request['LotNum'],
            'Safety_stock'            => $request['Safety_stock'],
            'LastStockReceiptDate'    => $request['LastStockReceiptDate'] ?? null,
            'IsVaild'                 => $request['IsVaild'],
            'Createuser'              => $request['Createuser'],
            'UpdateUser'              => $request['UpdateUser'],
            'CreateTime'              => now(),  // 設定當前時間
            'UpdateTime'              => now(),
        ]);

        // 回應 JSON
        return response()->json([
            'message' => '庫別建立成功',
            'Dept'    => $Inventory
        ], 201);
    }

    // 🔍 查詢單一庫別
    public function show($InventoryNO)
    {
        $Inventory = Inventory::findByInventoryNO($InventoryNO);
        
        if (!$Inventory) {
            return response()->json(['message' => '庫別未找到'], 404);
        }

        return response()->json($Inventory);
    }

    // 🔍 查詢所有有效庫別
    public function getValidInventory()
    {
        return response()->json(Inventory::getValidInventory());
    }
}
