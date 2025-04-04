<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;

class ProductController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/createproduct",
     *     summary="新增品號資訊",
     *     description="新增品號資訊",
     *     operationId="createProduct",
     *     tags={"Base_Product"},
     *     @OA\Parameter(
     *         name="ProductNO",
     *         in="query",
     *         required=true,
     *         description="品號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="ProductNM",
     *         in="query",
     *         required=true,
     *         description="品名",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Specification",
     *         in="query",
     *         required=true,
     *         description="規格",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Barcode",
     *         in="query",
     *         required=false,
     *         description="條碼",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Price_1",
     *         in="query",
     *         required=true,
     *         description="售價一",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="Price_2",
     *         in="query",
     *         required=false,
     *         description="售價二",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="Price_3",
     *         in="query",
     *         required=false,
     *         description="售價三",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="Cost_1",
     *         in="query",
     *         required=true,
     *         description="進價一",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="Cost_2",
     *         in="query",
     *         required=false,
     *         description="進價二",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="Cost_3",
     *         in="query",
     *         required=false,
     *         description="進價三",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="Batch_control",
     *         in="query",
     *         required=true,
     *         description="批號管理",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Valid_days",
     *         in="query",
     *         required=true,
     *         description="有效天數",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="Effective_date",
     *         in="query",
     *         required=true,
     *         description="生效日期",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Stock_control",
     *         in="query",
     *         required=true,
     *         description="是否庫存管理",
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
     *         name="Expiry_date",
     *         in="query",
     *         required=true,
     *         description="失效日期",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Description",
     *         in="query",
     *         required=false,
     *         description="商品描述",
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
     *             @OA\Property(property="ProductNO", type="string", example="P001"),
     *             @OA\Property(property="ProductNM", type="string", example="螺絲起子"),
     *             @OA\Property(property="Specification", type="string", example="SP001"),
     *             @OA\Property(property="Barcode", type="string", example=""),
     *             @OA\Property(property="Price_1", type="integer", example=100),
     *             @OA\Property(property="Price_2", type="integer", example=0),
     *             @OA\Property(property="Price_3", type="integer", example=0),
     *             @OA\Property(property="Cost_1", type="decimal", example=60),
     *             @OA\Property(property="Cost_2", type="integer", example=0),
     *             @OA\Property(property="Cost_3", type="integer", example=0),
     *             @OA\Property(property="Batch_control", type="integer", example=true),
     *             @OA\Property(property="Valid_days", type="integer", example=0),
     *             @OA\Property(property="Effective_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="Stock_control", type="integer", example=true),
     *             @OA\Property(property="Safety_stock", type="integer", example=0),
     *             @OA\Property(property="Expiry_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="Description", type="string", example=""),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="建立失敗",
     *     )
     * )
     */
    // 儲存品號
    public function store(Request $request)
    {
        // 驗證請求
         $validated = $request->validate([
             'ProductNO'         => 'required|string|max:255|unique:product,ProductNO',
             'ProductNM'         => 'required|string|max:255',
             'Specification'     => 'required|string|max:255',
             'Barcode'            => 'nullable|string|max:255',
             'Price_1'            => 'required|integer|max:10000',
             'Price_2'            => 'nullable|integer|max:10000',
             'Price_3'            => 'nullable|integer|max:10000',
             'Cost_1'            => 'required|integer|max:10000',
             'Cost_2'            => 'nullable|integer|max:10000',
             'Cost_3'            => 'nullable|integer|max:10000',
             'Batch_control'     => 'required|boolean',
             'Valid_days'        => 'required|integer|max:10000',
             'Effective_date'    => 'required|date',
             'Stock_control'     => 'required|boolean',
             'Safety_stock'      => 'required|integer|max:10000',
             'Expiry_date'       => 'required|date',
             'Description'       => 'nullable|string|max:255',
             'IsValid'            => 'required|boolean'
         ]);
        
    
        // 建立品號資料
        $Product = Product::create([
            'ProductNO'     => $validated['ProductNO'],
            'ProductNM'     => $validated['ProductNM'],
            'Specification'   => $validated['Specification'],
            'Barcode'   => $validated['Barcode']?? null,
            'Price_1' => $validated['Price_1'],
            'Price_2'   => $validated['Price_2']?? null,
            'Price_3' => $validated['Price_3']?? null,
            'Cost_1'   => $validated['Cost_1'],
            'Cost_2'  => $validated['Cost_2']?? null,
            'Cost_3'   => $validated['Cost_3']?? null,
            'Batch_control' => $validated['Batch_control'],
            'Valid_days'   => $validated['Valid_days'],
            'Effective_date'  => $validated['Effective_date'],
            'Stock_control'   => $validated['Stock_control'],
            'Safety_stock' => $validated['Safety_stock'],
            'Expiry_date'   => $validated['Expiry_date'],
            'Description'  => $validated['Description']?? null,
            'IsValid'    => $validated['IsValid']
        ]);

        // 回應 JSON
        if (!$Product) {
            return response()->json([
                'status' => false,
                'message' => '品號建立失敗',
                'output'    => null
            ], status: 404);
        }else {
            // 回應 JSON
            return response()->json([
                'status' => true,
                'message' => 'success',
                'output'    => $Product
            ], 200);
        }

    }
    /**
     * @OA\GET(
     *     path="/api/product/{ProductNO}",
     *     summary="查詢特定品號",
     *     description="查詢特定品號",
     *     operationId="getproduct",
     *     tags={"Base_Product"},
     *     @OA\Parameter(
     *         name="ProductNO",
     *         in="path",
     *         required=true,
     *         description="品號代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="ProductNO", type="string", example="P001"),
     *             @OA\Property(property="ProductNM", type="string", example="螺絲起子"),
     *             @OA\Property(property="Specification", type="string", example="SP001"),
     *             @OA\Property(property="Barcode", type="string", example=""),
     *             @OA\Property(property="Price_1", type="integer", example=100),
     *             @OA\Property(property="Price_2", type="integer", example=0),
     *             @OA\Property(property="Price_3", type="integer", example=0),
     *             @OA\Property(property="Cost_1", type="decimal", example=60),
     *             @OA\Property(property="Cost_2", type="integer", example=0),
     *             @OA\Property(property="Cost_3", type="integer", example=0),
     *             @OA\Property(property="Batch_control", type="integer", example=true),
     *             @OA\Property(property="Valid_days", type="integer", example=0),
     *             @OA\Property(property="Effective_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="Stock_control", type="integer", example=true),
     *             @OA\Property(property="Safety_stock", type="integer", example=0),
     *             @OA\Property(property="Expiry_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="Description", type="string", example=""),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到品號"
     *     )
     * )
     */
    // 🔍 查詢單一品號
    public function show($ProductNO)
    {
        $Product = Product::findByProductNO($ProductNO);
        // 判斷品號是否存在
        if (!$Product) {
            return response()->json([
                'status' => false,
                'message' => '品號未找到',
                'output'    => null
            ], 404);
        }

        return response()->json([                
            'status' => true,
            'message' => 'success',
            'output'    => $Product
        ],200);
    }
    /**
     * @OA\GET(
     *     path="/api/product/valid",
     *     summary="查詢所有有效品號",
     *     description="查詢所有有效品號",
     *     operationId="GetAllProduct",
     *     tags={"Base_Product"},
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="ProductNO", type="string", example="P001"),
     *             @OA\Property(property="ProductNM", type="string", example="螺絲起子"),
     *             @OA\Property(property="Specification", type="string", example="SP001"),
     *             @OA\Property(property="Barcode", type="string", example=""),
     *             @OA\Property(property="Price_1", type="integer", example=100),
     *             @OA\Property(property="Price_2", type="integer", example=0),
     *             @OA\Property(property="Price_3", type="integer", example=0),
     *             @OA\Property(property="Cost_1", type="decimal", example=60),
     *             @OA\Property(property="Cost_2", type="integer", example=0),
     *             @OA\Property(property="Cost_3", type="integer", example=0),
     *             @OA\Property(property="Batch_control", type="integer", example=true),
     *             @OA\Property(property="Valid_days", type="integer", example=0),
     *             @OA\Property(property="Effective_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="Stock_control", type="integer", example=true),
     *             @OA\Property(property="Safety_stock", type="integer", example=0),
     *             @OA\Property(property="Expiry_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="Description", type="string", example=""),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到有效品號"
     *     )
     * )
     */
    // 🔍 查詢所有有效品號
    public function getValidProduct()
    {
        $Product = Product::where('IsValid', '1')->get();
        if ($Product->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => '未找到有效品號',
                'output'    => null
            ], 404);
        }
        return response()->json([                
            'status' => true,
            'message' => 'success',
            'output'    => $Product
        ],200);
    }
    /**
     * @OA\patch(
     *     path="/api/product/{ProductNO}/disable",
     *     summary="刪除特定品號",
     *     description="刪除特定品號",
     *     operationId="DeleteProduct",
     *     tags={"Base_Product"},
     *     @OA\Parameter(
     *         name="ProductNO",
     *         in="path",
     *         required=true,
     *         description="品號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="ProductNO", type="string", example="P001"),
     *             @OA\Property(property="ProductNM", type="string", example="螺絲起子"),
     *             @OA\Property(property="Specification", type="string", example="SP001"),
     *             @OA\Property(property="Barcode", type="string", example=""),
     *             @OA\Property(property="Price_1", type="integer", example=100),
     *             @OA\Property(property="Price_2", type="integer", example=0),
     *             @OA\Property(property="Price_3", type="integer", example=0),
     *             @OA\Property(property="Cost_1", type="decimal", example=60),
     *             @OA\Property(property="Cost_2", type="integer", example=0),
     *             @OA\Property(property="Cost_3", type="integer", example=0),
     *             @OA\Property(property="Batch_control", type="integer", example=true),
     *             @OA\Property(property="Valid_days", type="integer", example=0),
     *             @OA\Property(property="Effective_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="Stock_control", type="integer", example=true),
     *             @OA\Property(property="Safety_stock", type="integer", example=0),
     *             @OA\Property(property="Expiry_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="Description", type="string", example=""),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )   
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到品號",
     *     )
     * )
     */
    // 🔍 刪除特定品號
    public function disable($ProductNO)
    {
        $Product = Product::findByProductNO($ProductNO);
        
        if (!$Product) {
            return response()->json([
                'status' => false,
                'message' => '品號未找到',
                'output'    => null
            ], 404);
        }

        $Product->IsValid = 0;
        $Product->UpdateUser = 'admin';
        $Product->UpdateTime = now();
        $Product->save();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'output'    => $Product
        ], 200);
    }
}
