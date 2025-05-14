<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
require_once base_path('app/Models/connect.php'); 
use App\Models\Supplier;
use App\Models\Account;
use App\Models\SysCode;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/createproduct",
     *     summary="新增品號資訊",
     *     description="新增品號資訊",
     *     operationId="createproduct",
     *     tags={"base_product"},
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
     *         description="批號管理(開窗選擇)",
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
     *         name="product_path",
     *         in="query",
     *         required=false,
     *         description="圖片路徑",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="main_supplier",
     *         in="query",
     *         required=false,
     *         description="主要供應商uuid(開窗選擇)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Accounting",
     *         in="query",
     *         required=false,
     *         description="認列科目uuid(開窗選擇)",
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
     *             @OA\Property(property="main_supplier", type="string", example="840ec743-f3d2-4760-b3a2-5e960207a61e"),
     *             @OA\Property(property="Accounting", type="string", example="04f32f4a-9ac1-43f8-b22c-1c1539de7005"),
     *             @OA\Property(property="description", type="string", example=""),
     *             @OA\Property(property="product_path", type="string", example=""),
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
                'cost_1'            => 'required|integer|max:10000',
                'batch_control'     => 'required|string|max:255',
                'valid_days'        => 'required|integer|max:10000',
                'effective_date'    => 'required|date',
                'stock_control'     => 'required|boolean',
                'safety_stock'      => 'required|integer|max:10000',
                'expiry_date'       => 'required|date',
                'description'       => 'nullable|string|max:255',
                'product_path'      => 'nullable|string|max:255',
                'main_supplier'     => 'nullable|string|max:255',
                'Accounting'        => 'nullable|string|max:255',
                'unit'              => 'nullable|string|max:255',
                'is_valid'            => 'required|boolean'
            ]);
            
            if($validator->fails()){
                return response()->json([
                    'status' => true,
                    'message' => '資料驗證失敗',
                    'errors' => $validator->errors()
                ], 200);
            }
        
    
        // 建立品號資料
        $Product = Product::create([
            'product_no'     => $request['product_no'],
            'product_nm'     => $request['product_nm'],
            'specification'  => $request['specification'],
            'price_1'        => $request['price_1'],
            'cost_1'         => $request['cost_1'],
            'batch_control'  => $request['batch_control'],
            'valid_days'     => $request['valid_days'],
            'effective_date' => $request['effective_date'],
            'stock_control'  => $request['stock_control'],
            'safety_stock'   => $request['safety_stock'],
            'expiry_date'    => $request['expiry_date'],
            'description'    => $request['description']?? null,
            'product_path'   => $request['product_path']?? null,
            'main_supplier'  => $request['main_supplier']?? null,
            'Accounting'     => $request['Accounting']?? null,
            'unit'           => $request['unit']?? null,
            'is_valid'       => $request['is_valid']
        ]);

        // 回應 JSON
        if (!$Product) {
            return response()->json([
                'status' => true,
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
     *     path="/api/product/{productno}",
     *     summary="查詢特定品號",
     *     description="查詢特定品號",
     *     operationId="getproduct",
     *     tags={"base_product"},
     *     @OA\Parameter(
     *         name="productno",
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
     *             @OA\Property(property="batch_control", type="string", example=01),
     *             @OA\Property(property="valid_days", type="integer", example=0),
     *             @OA\Property(property="effective_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="stock_control", type="integer", example=true),
     *             @OA\Property(property="safety_stock", type="integer", example=0),
     *             @OA\Property(property="expiry_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="main_supplier", type="string", example="840ec743-f3d2-4760-b3a2-5e960207a61e"),
     *             @OA\Property(property="Accounting", type="string", example="04f32f4a-9ac1-43f8-b22c-1c1539de7005"),
     *             @OA\Property(property="description", type="string", example=""),
     *             @OA\Property(property="product_path", type="string", example=""),
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
            $Product = Product::findByProductNO($ProductNO)->where('is_valid', '1')->first();
            // 判斷品號是否存在
            if (!$Product) {
                return response()->json([
                    'status' => true,
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
     *     path="/api/product3/valid",
     *     summary="查詢所有有效品號(含關鍵字查詢，品名、品號、規格、商品描述)",
     *     description="查詢所有有效品號(含關鍵字查詢，品名、品號、規格、商品描述)",
     *     operationId="getallproduct",
     *     tags={"base_product"},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         required=false,
     *         description="關鍵字查詢",
     *         @OA\Schema(type="string")
     *     ),
    * @OA\Response(
    *     response=200,
    *     description="成功取得分頁品號資料",
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
    *                 @OA\Property(property="UsrNo", type="string", example="U001"),
    *                 @OA\Property(property="UsrNM", type="string", example="姚佩彤"),
    *                 @OA\Property(property="Note", type="string", example=""),
    *                 @OA\Property(property="is_valid", type="boolean", example=true),
    *                 @OA\Property(property="Createuser", type="string", example="admin"),
    *                 @OA\Property(property="UpdateUser", type="string", example="admin"),
    *                 @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
    *                 @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
    *             )
    *         )
    *     )
    * ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到有效品號"
     *     )
     * )
     */
    // 🔍 查詢所有有效品號
    public function getvalidproduct(Request $request)
    {
        try{
            $pdo = getPDOConnection();
            $keyword = $request->query('keyword'); // 可為 null
            $page = $request->query('page'); // 當前頁碼
            $pageSize = $request->query('pageSize'); // 一頁顯示幾筆數值
            $page = $page ? (int)$page : 1; // 預設為第 1 頁
            $pageSize = $pageSize ? (int)$pageSize : 30; // 預設每頁顯示 30 筆資料
            // 使用 DB::select 進行關鍵字查詢
            // 關鍵字 品號 品名 規格

            $likeKeyword = '%' . $keyword . '%';

            //查詢目前頁數的資料
                $offset = ($page - 1) * $pageSize;
                //LIMIT 30：每次最多回傳 30 筆資料
                //OFFSET 0：從第 0 筆開始取，也就是第一頁的第 1 筆
                //LIMIT 30 OFFSET 0  -- 取第 1~30 筆
                //LIMIT 30 OFFSET 30 -- 取第 31~60 筆
                //LIMIT 30 OFFSET 60 -- 取第 61~90 筆
            $sql_data = "select  *
                        from product
                        where product.is_valid = '1'  
                        and ( product.product_no LIKE ? 
                           OR product.product_nm LIKE ?
                           OR product.specification LIKE ? )
                        order by update_time,create_time asc
                        LIMIT ? OFFSET ?
                        ;";

            $Product = DB::select($sql_data, [$likeKeyword, $likeKeyword, $likeKeyword,$pageSize, $offset]);


            //取得總筆數與總頁數   
            $sql_count = "
                    SELECT COUNT(*) as total
                    FROM product
                    WHERE product.is_valid = '1';
                ";
            $stmt = $pdo->prepare($sql_count);
            $stmt->execute();
            $total = $stmt->fetchColumn();
            $totalPages = ceil($total / $pageSize); // 計算總頁數  

 

            if (!$Product) {
                return response()->json([
                    'status' => true,
                    'atPage' => $page,
                    'total' => $total,
                    'totalPages' => $totalPages,
                    'message' => '未找到有效品號',
                    'output'    => $Product
                ], 404);
            }
            return response()->json([                
                'status' => true,
                'atPage' => $page,
                'total' => $total,
                'totalPages' => $totalPages,
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
     * @OA\patch(
     *     path="/api/product/{productno}/disable",
     *     summary="刪除特定品號",
     *     description="刪除特定品號",
     *     operationId="deleteproduct",
     *     tags={"base_product"},
     *     @OA\Parameter(
     *         name="productno",
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
     *             @OA\Property(property="batch_control", type="string", example=01),
     *             @OA\Property(property="valid_days", type="integer", example=0),
     *             @OA\Property(property="effective_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="stock_control", type="integer", example=true),
     *             @OA\Property(property="safety_stock", type="integer", example=0),
     *             @OA\Property(property="expiry_date", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="main_supplier", type="string", example="840ec743-f3d2-4760-b3a2-5e960207a61e"),
     *             @OA\Property(property="Accounting", type="string", example="04f32f4a-9ac1-43f8-b22c-1c1539de7005"),
     *             @OA\Property(property="description", type="string", example=""),
     *             @OA\Property(property="product_path", type="string", example=""),
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
        try{
            $Product = Product::findByProductNO($ProductNO)->where('is_valid', '1')->first();
            // 判斷品號是否存在
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
     * @OA\get(
     *     path="/api/product1/showconst",
     *     summary="列出所有品號需要的常用(下拉、彈窗)",
     *     description="列出所有品號需要的常用(下拉、彈窗)",
     *     operationId="show_product_all_const",
     *     tags={"base_product"},
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
    public function showconst($constant='all'){
        // 查詢 '批號管理' 的資料
        $SysCode = SysCode::where('param_sn', '03')->where('is_valid','1')->get();
        // 查詢 '供應商' 的資料
        $SysCode1 = Supplier::where('is_valid','1')->get();
        // 查詢 '認列會計科目' 的資料
        $SysCode2 = Account::where('is_valid','1')->get();
        try {
            // 檢查是否有結果
            if (!$SysCode ) {
                return response()->json([
                    'status' => true,
                    'message' => '常用資料未找到',
                    'batch_controloption' => null,
                    'Supplier' => null,
                    'Account' => null
                ], 404);
            }
    
            // 返回查詢結果
            return response()->json([
                'status' => true,
                'message' => 'success',
                'batch_controloption' => $SysCode,
                'Supplier' => $SysCode1,
                'Account' => $SysCode2
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
