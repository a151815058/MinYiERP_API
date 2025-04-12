<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SysCode;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
     *         name="product_no",
     *         in="query",
     *         required=true,
     *         description="品號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="product_nm",
     *         in="query",
     *         required=true,
     *         description="品名",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="specification",
     *         in="query",
     *         required=true,
     *         description="規格",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="price_1",
     *         in="query",
     *         required=true,
     *         description="售價一",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="price_2",
     *         in="query",
     *         required=false,
     *         description="售價二",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="price_3",
     *         in="query",
     *         required=false,
     *         description="售價三",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="cost_1",
     *         in="query",
     *         required=true,
     *         description="進價一",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="cost_2",
     *         in="query",
     *         required=false,
     *         description="進價二",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="cost_3",
     *         in="query",
     *         required=false,
     *         description="進價三",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="batch_control",
     *         in="query",
     *         required=true,
     *         description="批號管理(param_sn=03)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="valid_days",
     *         in="query",
     *         required=true,
     *         description="有效天數",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="effective_date",
     *         in="query",
     *         required=true,
     *         description="生效日期",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="stock_control",
     *         in="query",
     *         required=true,
     *         description="是否庫存管理(1=是,0=否)",
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
     *         name="expiry_date",
     *         in="query",
     *         required=true,
     *         description="失效日期",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         required=false,
     *         description="商品描述",
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
     *             @OA\Property(property="product_no", type="string", example="P001"),
     *             @OA\Property(property="product_nm", type="string", example="螺絲起子"),
     *             @OA\Property(property="specification", type="string", example="SP001"),
     *             @OA\Property(property="price_1", type="integer", example=100),
     *             @OA\Property(property="price_2", type="integer", example=0),
     *             @OA\Property(property="price_3", type="integer", example=0),
     *             @OA\Property(property="cost_1", type="decimal", example=60),
     *             @OA\Property(property="cost_2", type="integer", example=0),
     *             @OA\Property(property="cost_3", type="integer", example=0),
     *             @OA\Property(property="batch_control", type="string", example=01),
     *             @OA\Property(property="valid_days", type="integer", example=0),
     *             @OA\Property(property="effective_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="stock_control", type="integer", example=true),
     *             @OA\Property(property="safety_stock", type="integer", example=0),
     *             @OA\Property(property="expiry_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="description", type="string", example=""),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
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
        try {
            // 驗證請求
            $validator = Validator::make($request->all(),[
                'product_no'         => 'required|string|max:255|unique:product,product_no',
                'product_nm'         => 'required|string|max:255',
                'specification'     => 'required|string|max:255',
                'price_1'            => 'required|integer|max:10000',
                'price_2'            => 'nullable|integer|max:10000',
                'price_3'            => 'nullable|integer|max:10000',
                'cost_1'            => 'required|integer|max:10000',
                'cost_2'            => 'nullable|integer|max:10000',
                'cost_3'            => 'nullable|integer|max:10000',
                'batch_control'     => 'required|string|max:255',
                'valid_days'        => 'required|integer|max:10000',
                'effective_date'    => 'required|date',
                'stock_control'     => 'required|boolean',
                'safety_stock'      => 'required|integer|max:10000',
                'expiry_date'       => 'required|date',
                'description'       => 'nullable|string|max:255',
                'is_valid'            => 'required|boolean'
            ]);
            
            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => '資料驗證失敗',
                    'errors' => $validator->errors()
                ], 200);
            }
        
    
        // 建立品號資料
        $Product = Product::create([
            'product_no'     => $request['product_no'],
            'product_nm'     => $request['product_nm'],
            'specification'   => $request['specification'],
            'price_1' => $request['price_1'],
            'price_2'   => $request['price_2']?? null,
            'price_3' => $request['price_3']?? null,
            'cost_1'   => $request['cost_1'],
            'cost_2'  => $request['cost_2']?? null,
            'cost_3'   => $request['cost_3']?? null,
            'batch_control' => $request['batch_control'],
            'valid_days'   => $request['valid_days'],
            'effective_date'  => $request['effective_date'],
            'stock_control'   => $request['stock_control'],
            'safety_stock' => $request['safety_stock'],
            'expiry_date'   => $request['expiry_date'],
            'description'  => $request['description']?? null,
            'is_valid'    => $request['is_valid']
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
     *             @OA\Property(property="product_no", type="string", example="P001"),
     *             @OA\Property(property="product_nm", type="string", example="螺絲起子"),
     *             @OA\Property(property="specification", type="string", example="SP001"),
     *             @OA\Property(property="price_1", type="integer", example=100),
     *             @OA\Property(property="price_2", type="integer", example=0),
     *             @OA\Property(property="price_3", type="integer", example=0),
     *             @OA\Property(property="cost_1", type="decimal", example=60),
     *             @OA\Property(property="cost_2", type="integer", example=0),
     *             @OA\Property(property="cost_3", type="integer", example=0),
     *             @OA\Property(property="batch_control", type="integer", example=true),
     *             @OA\Property(property="valid_days", type="integer", example=0),
     *             @OA\Property(property="effective_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="stock_control", type="integer", example=true),
     *             @OA\Property(property="safety_stock", type="integer", example=0),
     *             @OA\Property(property="expiry_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="description", type="string", example=""),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
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
        try{
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
     *     path="/api/product2/{keyword}",
     *     summary="查詢關鍵字",
     *     description="查詢關鍵字",
     *     operationId="getproductkeyword",
     *     tags={"Base_Product"},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="path",
     *         required=true,
     *         description="關鍵字(請輸入品名、規格、商品描述)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="product_no", type="string", example="P001"),
     *             @OA\Property(property="product_nm", type="string", example="螺絲起子"),
     *             @OA\Property(property="specification", type="string", example="SP001"),
     *             @OA\Property(property="price_1", type="integer", example=100),
     *             @OA\Property(property="price_2", type="integer", example=0),
     *             @OA\Property(property="price_3", type="integer", example=0),
     *             @OA\Property(property="cost_1", type="decimal", example=60),
     *             @OA\Property(property="cost_2", type="integer", example=0),
     *             @OA\Property(property="cost_3", type="integer", example=0),
     *             @OA\Property(property="batch_control", type="integer", example=true),
     *             @OA\Property(property="valid_days", type="integer", example=0),
     *             @OA\Property(property="effective_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="stock_control", type="integer", example=true),
     *             @OA\Property(property="safety_stock", type="integer", example=0),
     *             @OA\Property(property="expiry_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="description", type="string", example=""),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到品號"
     *     )
     * )
     */
    // 🔍 查詢關鍵字
    public function showNM($keyword)
    {
        try{
            // 使用關鍵字查詢品號
            $Product = Product::where('product_no', 'like', '%' . $keyword . '%')
                ->orWhere('product_nm', 'like', '%' . $keyword . '%')
                ->orWhere('specification', 'like', '%' . $keyword . '%')
                ->get();
        
            // 判斷品號是否存在
            if ($Product->isEmpty()) {
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
     *             @OA\Property(property="product_no", type="string", example="P001"),
     *             @OA\Property(property="product_nm", type="string", example="螺絲起子"),
     *             @OA\Property(property="specification", type="string", example="SP001"),
     *             @OA\Property(property="price_1", type="integer", example=100),
     *             @OA\Property(property="price_2", type="integer", example=0),
     *             @OA\Property(property="price_3", type="integer", example=0),
     *             @OA\Property(property="cost_1", type="decimal", example=60),
     *             @OA\Property(property="cost_2", type="integer", example=0),
     *             @OA\Property(property="cost_3", type="integer", example=0),
     *             @OA\Property(property="batch_control", type="integer", example=true),
     *             @OA\Property(property="valid_days", type="integer", example=0),
     *             @OA\Property(property="effective_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="stock_control", type="integer", example=true),
     *             @OA\Property(property="safety_stock", type="integer", example=0),
     *             @OA\Property(property="expiry_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="description", type="string", example=""),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
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
        $Product = Product::where('is_valid', '1')->get();
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
     *             @OA\Property(property="product_no", type="string", example="P001"),
     *             @OA\Property(property="product_nm", type="string", example="螺絲起子"),
     *             @OA\Property(property="specification", type="string", example="SP001"),
     *             @OA\Property(property="price_1", type="integer", example=100),
     *             @OA\Property(property="price_2", type="integer", example=0),
     *             @OA\Property(property="price_3", type="integer", example=0),
     *             @OA\Property(property="cost_1", type="decimal", example=60),
     *             @OA\Property(property="cost_2", type="integer", example=0),
     *             @OA\Property(property="cost_3", type="integer", example=0),
     *             @OA\Property(property="batch_control", type="integer", example=true),
     *             @OA\Property(property="valid_days", type="integer", example=0),
     *             @OA\Property(property="effective_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="stock_control", type="integer", example=true),
     *             @OA\Property(property="safety_stock", type="integer", example=0),
     *             @OA\Property(property="expiry_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="description", type="string", example=""),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
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

        $Product->is_valid = 0;
        $Product->update_user = 'admin';
        $Product->update_time = now();
        $Product->save();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'output'    => $Product
        ], 200);
    }
    /**
     * @OA\get(
     *     path="/api/products/showConst",
     *     summary="列出所有品號需要的常用(下拉、彈窗)",
     *     description="列出所有品號需要的常用(下拉、彈窗)",
     *     operationId="Show_Product_ALL_Const",
     *     tags={"Base_Product"},
     *     @OA\Response(
     *         response=200,
     *         description="成功"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="有效品號類型未找到"
     *     )
     * )
     */
    // 列出所有品號需要的常用(下拉、彈窗)
    public function showConst($constant='all'){
        // 查詢 '批號管理' 的資料
        $SysCode = SysCode::where('param_sn', '03')->get();
        try {
            // 檢查是否有結果
            if ($SysCode->isEmpty() ) {
                return response()->json([
                    'status' => false,
                    'message' => '常用資料未找到',
                    'batch_controloption' => null,
                ], 404);
            }
    
            // 返回查詢結果
            return response()->json([
                'status' => true,
                'message' => 'success',
                'batch_controloption' => $SysCode
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
