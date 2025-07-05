<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SysCode;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Orderfile;
use App\Models\BillInfo;
use App\Models\Client;
use App\Models\Dept;
use App\Models\SysUser;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Productinventory;
use App\Models\PaymentTerm;
use Illuminate\Support\Str;
require_once base_path('app/Models/connect.php'); 
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use App\Helpers\ValidationHelper;
/**
 * @OA\POST(
 *     path="/api/createorder",
 *     summary="新增訂單資訊(主檔+明細)",
 *     description="新增訂單主檔與明細資料",
 *     operationId="createorder",
 *     tags={"base_order"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="order_type", type="string", example="SO"),
 *             @OA\Property(property="order_no", type="string", example="ORD20250514001"),
 *             @OA\Property(property="order_date", type="string", format="date", example="2025-05-14"),
 *             @OA\Property(property="customer_name", type="string", example="台灣大有限公司"),
 *             @OA\Property(property="contact_person", type="string", example="王小明"),
 *             @OA\Property(property="expected_completion_date", type="string", format="date", example="2025-06-01"),
 *             @OA\Property(property="responsible_dept", type="string", example="Sales"),
 *             @OA\Property(property="responsible_staff", type="string", example="E12345"),
 *             @OA\Property(property="terms_no", type="string", example="T001"),
 *             @OA\Property(property="currency_no", type="string", example="TWD"),
 *             @OA\Property(property="tax_type", type="string", example="1"),
 *             @OA\Property(property="is_deposit", type="string", example="1"),
 *             @OA\Property(property="create_deposit_type", type="string", example="現金"),
 *             @OA\Property(property="deposit", type="number", format="float", example=1000.00),
 *             @OA\Property(property="customer_address", type="string", example="台北市信義區123號"),
 *             @OA\Property(property="delivery_address", type="string", example="新北市板橋區456巷"),
 *             @OA\Property(property="status", type="string", example="draft"),
 *             @OA\Property(property="is_valid", type="string", example="1"),
 *             @OA\Property(
 *                 property="orderfile",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="ref_file_path", type="string", example="/path/to/file"),
 *                     @OA\Property(property="ref_file_name", type="string", example="水電圖"),
 *                     @OA\Property(property="ref_file_type", type="string", example="PDF"),
 *                     @OA\Property(property="is_valid", type="string", example="1")
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="order_details",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="line_no", type="integer", example=1),
 *                     @OA\Property(property="product_no", type="string", example="P0001"),
 *                     @OA\Property(property="product_nm", type="string", example="筆記型電腦"),
 *                     @OA\Property(property="specification", type="string", example="15吋 i7/16G/512G"),
 *                     @OA\Property(property="inventory_no", type="string", example="I2025051401"),
 *                     @OA\Property(property="qty", type="number", format="float", example=2),
 *                     @OA\Property(property="unit", type="string", example="台"),
 *                     @OA\Property(property="lot_num", type="string", example="L20250401"),
 *                     @OA\Property(property="unit_price", type="number", format="float", example=30000.00),
 *                     @OA\Property(property="amount", type="number", format="float", example=60000.00),
 *                     @OA\Property(property="customer_product_no", type="string", example="CUST001"),
 *                     @OA\Property(property="note", type="string", example="急件"),
 *                     @OA\Property(property="status", type="string", example="active"),
 *                     @OA\Property(property="is_valid", type="string", example="1")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="新增成功",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="order_id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
 *             @OA\Property(property="message", type="string", example="新增成功")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="資料格式錯誤"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="伺服器錯誤"
 *     )
 * )
 */
class OrderController extends Controller
{
    // 儲存訂單資料
    public function store(Request $request)
    {
        try{
            $errors1 = [];

            //////訂單主檔//////

            /*訂單單別為必填*/
            if (!$request->filled('order_type')) {
                $errors1[] = '訂單單別為必填';
            }
            /*訂單單別必須存在單據管理*/
            if(!BillInfo::where('bill_no', $request->input('order_type'))->exists()){
                $errors1['order_type_err'] = '訂單單別不存在，請選擇正確的訂單單別';
            }

            /*訂單單號為必填*/
            if (!$request->filled('order_no')) {
                $errors1[] = '訂單單號為必填';
            }

            /*訂單日期為必填*/
            if (!$request->filled('order_date')) {
                $errors1[] = '訂單日期為必填';
            } elseif (!ValidationHelper::isValidDate($request->input('order_date'))) {
                $errors1[] = '訂單日期格式錯誤，請使用 YYYY/MM/DD 格式';
            }

            /*客戶名稱為必填 */
            if (!$request->filled('customer_name')) {
                $errors1[] = '客戶名稱為必填';
            }

            /*聯絡人為必填*/
            if (!$request->filled('contact_person')) {
                $errors1[] = '聯絡人為必填';
            }

            /*負責部門為必填*/
            if (!$request->filled('responsible_dept')) {
                $errors1[] = '負責部門為必填';
            } elseif (!Dept::where('dept_no', $request->input('responsible_dept'))->exists()) {
                $errors1[] = '負責部門不存在，請選擇正確的負責部門';
            }
            /*負責業務為必填*/  
            if (!$request->filled('responsible_staff')) {
                $errors1[] = '負責業務為必填';
            } elseif (!SysUser::where('user_no', $request->input('responsible_staff'))->exists()) {
                $errors1[] = '負責業務不存在，請選擇正確的負責業務';
            }   

            /*付款條件為必填*/
            if (!$request->filled('terms_no')) {
                $errors1[] = '付款條件為必填';
            } elseif (!PaymentTerm::where('terms_no', $request->input('terms_no'))->exists()) {
                $errors1[] = '付款條件不存在，請選擇正確的付款條件';
            }

            /*幣別為必填*/  
            if (!$request->filled('currency_no')) {
                $errors1[] = '幣別為必填';
            } elseif (!Currency::where('currency_no', $request->input('currency_no'))->exists()) {
                $errors1[] = '幣別不存在，請選擇正確的幣別';
            }

            /*課稅別為必填*/
            if (!$request->filled('tax_type')) {
                $errors1[] = '課稅別為必填';
            } elseif (!SysCode::where('param_sn', '04')->where('code', $request->input('tax_type'))->exists()) {
                $errors1[] = '課稅別不存在，請選擇正確的課稅別';
            }

            /*勾選是否開立訂金，則開立方式為必填*/
            if ($request->input('is_deposit')) {
                if (!$request->filled('create_deposit_type')) {
                    $errors1[] = '開立方式為必填';
                }
            }

            //////訂單明細檔//////
            // 驗證訂單明細的每一筆資料
            foreach ($request->input('order_details') as $detail) {

                /*訂單品號為必填*/
                if (!$detail['product_no']) {
                    $errors1[] = '訂單品號為必填';
                }
                /*品號必須存在產品主檔*/
                if (!Product::where('product_no', $detail['product_no'])->exists()) {
                    $errors1[] = '品號不存在，請選擇正確的品號';
                }

                /*品名出庫庫別為必填*/
                if (!$detail['inventory_no']) {
                    $errors1[] = '品名出庫庫別為必填';
                }

                /*品名數量為必填*/
                if (!$detail['qty']) {
                    $errors1[] = '品名數量為必填';
                } elseif (!is_numeric($detail['qty']) || $detail['qty'] <= 0) {
                    $errors1[] = '品名數量必須為正數';
                }

                /*品名批號為必填*/
                if (!$detail['lot_num']) {
                    $errors1[] = '品名批號為必填';
                }

                /*品名數量必須小於等於批號數量*/
                if (isset($detail['lot_num']) && isset($detail['qty'])) {
                    $inventory = Productinventory::where('product_no', $detail['product_no'])
                        ->where('lot_num', $detail['lot_num'])
                        ->first();
                    if ($inventory && $detail['qty'] > $inventory->qty) {
                        $errors1[] = '品名數量必須小於等於批號數量';
                    }
                }

                /*單價為必填*/
                if (!$detail['unit_price']) {
                    $errors1[] = '單價為必填';
                } elseif (!is_numeric($detail['unit_price']) || $detail['unit_price'] <= 0) {
                    $errors1[] = '單價必須為正數';
                } 
            }

            // 驗證訂單圖片 
            foreach ($request->input('orderfile') as $detail) {
                $validator = Validator::make($detail, [
                    'ref_file_path' => 'required|string|max:255',
                    'ref_file_name' => 'required|string|max:100',
                    'ref_file_type' => 'required|string|max:20',
                    'is_valid' => 'required|boolean'
                ])->validate();
            }

            // 開始事務
            DB::beginTransaction();

            // 儲存訂單主檔
            $order = new Order();
            $order->uuid = Str::uuid();
            $order->fill($request->all());
            $order->create_user =  'admin'; 
            $order->create_time = now();
            $order->save();

            // 儲存訂單明細
            foreach ($request->input('order_details') as $detail) {
                $orderItem = new OrderItem();
                $orderItem->uuid = Str::uuid();
                $orderItem->order_id = $order->uuid; // 關聯到主檔
                $orderItem->fill($detail);
                $orderItem->create_user = 'admin'; 
                $orderItem->create_time = now();
                $orderItem->save();
            }

            // 儲存訂單圖片
            foreach ($request->input('orderfile') as $detail) {
                $orderfile = new Orderfile();
                $orderfile->uuid = Str::uuid();
                $orderfile->order_id = $order->uuid; // 關聯到主檔
                $orderfile->fill($detail);
                $orderfile->create_user = 'admin'; 
                $orderfile->create_time = now();
                $orderfile->save();
            }
            // 提交事務
            DB::commit();
            // 回傳成功訊息
            return response()->json([
                'status' => true,
                'message' => '新增成功',
                'order_id' => $order->uuid
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // 捕捉資料庫錯誤
            DB::rollBack();
            Log::error('資料庫錯誤：' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => '資料庫錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        } catch (\Illuminate\Http\Exceptions\PostTooLargeException $e) {
            // 捕捉請求過大錯誤
            return response()->json([
                'status' => false,
                'message' => '請求過大，請檢查上傳的檔案大小'
            ], 413);
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
     *     path="/api/orderInfo/{order_no}",
     *     summary="查詢特定訂單資訊",
     *     description="查詢特定訂單資訊",
     *     operationId="getorderInfo",
     *     tags={"base_order"},
     *     @OA\Parameter(
     *         name="order_no",
     *         in="path",
     *         required=true,
     *         description="訂單單號",
     *         @OA\Schema(type="string")
     *     ),
    *     @OA\Response(
    *         response=200,
    *         description="新增成功",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="order_id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
    *             @OA\Property(property="message", type="string", example="新增成功")
    *         )
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="資料格式錯誤"
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="伺服器錯誤"
    *     )
     * )
     */
    // 查詢特定訂單資訊(以訂單號碼查詢)
    public function showno($order_no)
    {
        try {
            $validator = Validator::make(['order_no' => $order_no], [
                'order_no' => 'required|string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => '資料驗證失敗',
                    'errors' => $validator->errors()
                ], 200);
            }
            // 查詢特定訂單資訊(以訂單號碼查詢)
            $sql = "select  *
                    from `order`
                    where `order`.order_no = ? and is_valid = '1'";
            $orderinfo = DB::select($sql, [$order_no]);
            // 查詢特定訂單明細資訊(以order_id查詢)
            $sql2 = "select  *
                    from `orderitem`
                    where `orderitem`.order_id = ? and is_valid = '1'";
            $orderinfo[1] = DB::select($sql2, [$orderinfo[0]->uuid]);
            // 查詢特定訂單圖片資訊(以order_id查詢)
            $sql3 = "select  *
                    from `orderfile`
                    where `orderfile`.order_id = ? and is_valid = '1'";        
            $orderinfo[2] = DB::select($sql3, [$orderinfo[0]->uuid]);

            

            if (!$orderinfo) {
                return response()->json([
                    'status' => true,
                    'message' => '查無資料',
                    'output' => null
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'success',
                'output' => $orderinfo
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
     *     path="/api/orderInfo1/valid",
     *     summary="查詢所有有效訂單資訊(訂單編號、訂單日期、負責部門、負責業務、客戶名稱、客戶地址)",
     *     description="查詢所有有效訂單資訊(訂單編號、訂單日期、負責部門、負責業務、客戶名稱、客戶地址)",
     *     operationId="getallorderinfos",
     *     tags={"base_order"},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         required=false,
     *         description="關鍵字查詢",
     *         @OA\Schema(type="string")
     *     ),
    *      @OA\Response(
    *           response=200,
    *           description="成功取得分頁訂單清單",
    *      @OA\JsonContent(
    *         type="object",
    *         @OA\Property(property="atPage", type="integer", example=1),
    *         @OA\Property(property="total", type="integer", example=10),
    *         @OA\Property(property="totalPages", type="integer", example=1),
    *         @OA\Property(
    *             property="data",
    *             type="array",
    *         @OA\Items(
    *             type="object",
    *             @OA\Property(property="order_type", type="string", example="SO"),
    *             @OA\Property(property="order_no", type="string", example="ORD20250514001"),
    *             @OA\Property(property="order_date", type="string", format="date", example="2025-05-14"),
    *             @OA\Property(property="customer_name", type="string", example="台灣大有限公司"),
    *             @OA\Property(property="contact_person", type="string", example="王小明"),
    *             @OA\Property(property="expected_completion_date", type="string", format="date", example="2025-06-01"),
    *             @OA\Property(property="responsible_dept", type="string", example="Sales"),
    *             @OA\Property(property="responsible_staff", type="string", example="E12345"),
    *             @OA\Property(property="terms_no", type="string", example="T001"),
    *             @OA\Property(property="currency_no", type="string", example="TWD"),
    *             @OA\Property(property="tax_type", type="string", example="1"),
    *             @OA\Property(property="is_deposit", type="string", example="1"),
    *             @OA\Property(property="create_deposit_type", type="string", example="現金"),
    *             @OA\Property(property="deposit", type="number", format="float", example=1000.00),
    *             @OA\Property(property="customer_address", type="string", example="台北市信義區123號"),
    *             @OA\Property(property="delivery_address", type="string", example="新北市板橋區456巷"),
    *             @OA\Property(property="status", type="string", example="draft"),
    *             @OA\Property(property="is_valid", type="string", example="1"),
    *             @OA\Property(
    *                 property="orderfile",
    *                 type="array",
    *                 @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="ref_file_path", type="string", example="/path/to/file"),
    *                     @OA\Property(property="ref_file_name", type="string", example="水電圖"),
    *                     @OA\Property(property="ref_file_type", type="string", example="PDF"),
    *                     @OA\Property(property="is_valid", type="string", example="1")
    *                 )
    *             ),
    *             @OA\Property(
    *                 property="order_details",
    *                 type="array",
    *                 @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="line_no", type="integer", example=1),
    *                     @OA\Property(property="product_no", type="string", example="P0001"),
    *                     @OA\Property(property="product_nm", type="string", example="筆記型電腦"),
    *                     @OA\Property(property="specification", type="string", example="15吋 i7/16G/512G"),
    *                     @OA\Property(property="inventory_no", type="string", example="I2025051401"),
    *                     @OA\Property(property="qty", type="number", format="float", example=2),
    *                     @OA\Property(property="unit", type="string", example="台"),
    *                     @OA\Property(property="lot_num", type="string", example="L20250401"),
    *                     @OA\Property(property="unit_price", type="number", format="float", example=30000.00),
    *                     @OA\Property(property="amount", type="number", format="float", example=60000.00),
    *                     @OA\Property(property="customer_product_no", type="string", example="CUST001"),
    *                     @OA\Property(property="note", type="string", example="急件"),
    *                     @OA\Property(property="status", type="string", example="active"),
    *                     @OA\Property(property="is_valid", type="string", example="1")
    *                 )
    *             )
    *         )
    *         )
    *     )
    * ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到有效發票資訊"
     *     )
     * )
     */
    // 查詢所有有效訂單資訊(訂單編號、訂單日期、負責部門、負責業務、客戶名稱、客戶地址)
    public function getvaildorderinfo(Request $request)
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

            $query = Order::with(['orderitems', 'orderfiles'])
                ->where('is_valid', '1')
                ->where(function ($q) use ($keyword) {
                    $q->where('order_no', 'like', "%$keyword%")
                    ->orWhere('order_date', "%$keyword%")
                    ->orWhere('responsible_dept', 'like', "%$keyword%")
                    ->orWhere('responsible_staff', 'like', "%$keyword%")
                    ->orWhere('customer_name', 'like', "%$keyword%")
                    ->orWhere('customer_address', 'like', "%$keyword%");
                })
                ->orderBy('update_time')
                ->orderBy('create_time')
                ->paginate($pageSize, ['*'], 'page', $page);

            //取得總筆數與總頁數   
            $sql_count = "
                    SELECT COUNT(*) as total
                    from `order`
                    where is_valid = '1'
                      and (`order`.order_no LIKE ?
                           or `order`.order_date LIKE ?
                           or `order`.responsible_dept LIKE ?
                           or `order`.responsible_staff LIKE ?
                           or `order`.customer_name LIKE ?
                           or `order`.customer_address LIKE ?)
                    order by `order`.update_time, `order`.create_time asc
                ";
            $likeKeyword = '%' . $keyword . '%';
            $stmt = $pdo->prepare($sql_count);
            $stmt->execute([$likeKeyword, $likeKeyword,$likeKeyword, $likeKeyword, $likeKeyword,$likeKeyword]);
            $total = $stmt->fetchColumn();
            $totalPages = ceil($total / $pageSize); // 計算總頁數    

            


            if ($query->isEmpty()) {
                // 如果查詢結果為空，回傳 404 錯誤
                return response()->json([
                    'status' => true,
                    'atPage' => $page,
                    'total' => $total,
                    'totalPages' => $totalPages,                
                    'message' => '有效訂單資訊未找到',
                    'output'    => null
                ], 404);
            }
            return response()->json([                
                'status' => true,
                'atPage' => $page,
                'total' => $total,
                'totalPages' => $totalPages,                
                'message' => 'success',
                'output'    => $query->items()
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
     * @OA\get(
     *     path="/api/orderInfo2/showconst",
     *     summary="列出所有訂單需要的常用(下拉、彈窗)",
     *     description="列出所有訂單需要的常用(下拉、彈窗)",
     *     operationId="show_order_aLL_const",
     *     tags={"base_order"},
     *     @OA\Response(
     *         response=200,
     *         description="成功"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="訂單資料需要的常用未找到"
     *     )
     * )
     */
    // 列出所有訂單需要的常用(下拉、彈窗)
    public function showconst($constant='all'){
        // 查詢 '訂單單別' 的資料
        $BillInfo = BillInfo::where('is_valid','1')->get();
        // 查詢 '客戶資料' 的資料
        $Client = Client::where('is_valid','1')->get();
        // 查詢 '負責部門' 的資料
        $dept = Dept::where('is_valid','1')->get();
        // 查詢 '付款條件' 的資料
        $PaymentTerm = PaymentTerm::where('is_valid','1')->get();
        // 查詢 '幣別' 的資料
        $Currency = Currency::where('is_valid','1')->get();
        // 查詢 '課稅別' 的資料
        $sysCode = SysCode::where('is_valid','1')->where('param_sn','04')->get();
        
        try {
            
            // 返回查詢結果
            return response()->json([
                'status' => true,
                'message' => 'success',
                'BillInfoOption' => $BillInfo,
                'clientOption' => $Client,
                'deptOption' => $dept,
                //'SysUser' => $user,
                'PaymentTermOption' => $PaymentTerm,
                'CurrencyOption' => $Currency,
                'taxtypeOption' => $sysCode
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
