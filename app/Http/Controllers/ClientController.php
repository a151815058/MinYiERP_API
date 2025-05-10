<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\SysCode;
use App\Models\PaymentTerm;
use App\Models\Currency;
use App\Models\SysUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class ClientController extends Controller
{
/**
 * @OA\POST(
 *     path="/api/createclient",
 *     summary="新增客戶資料",
 *     description="新增客戶資料",
 *     operationId="createclient",
 *     tags={"base_client"},
 *     @OA\Parameter(name="client_no", in="query", required=true, description="客戶編號", @OA\Schema(type="string")),
 *     @OA\Parameter(name="client_shortnm", in="query", required=true, description="客戶簡稱", @OA\Schema(type="string")),
 *     @OA\Parameter(name="client_type", in="query", required=true, description="客戶型態", @OA\Schema(type="string")),
 *     @OA\Parameter(name="client_fullnm", in="query", required=true, description="客戶全名", @OA\Schema(type="string")),
 *     @OA\Parameter(name="zip_code1", in="query", required=true, description="郵遞區號 1", @OA\Schema(type="string")),
 *     @OA\Parameter(name="address1", in="query", required=true, description="公司地址 1", @OA\Schema(type="string")),
 *     @OA\Parameter(name="zip_code2", in="query", required=false, description="郵遞區號 2 (選填)", @OA\Schema(type="string")),
 *     @OA\Parameter(name="address2", in="query", required=false, description="公司地址 2 (選填)", @OA\Schema(type="string")),
 *     @OA\Parameter(name="responsible_person", in="query", required=true, description="負責人", @OA\Schema(type="string")),
 *     @OA\Parameter(name="contact_person", in="query", required=true, description="聯絡人", @OA\Schema(type="string")),
 *     @OA\Parameter(name="contact_phone", in="query", required=true, description="聯絡人電話", @OA\Schema(type="string")),
 *     @OA\Parameter(name="phone", in="query", required=true, description="公司電話", @OA\Schema(type="string")),
 *     @OA\Parameter(name="fax", in="query", required=false, description="公司傳真 (選填)", @OA\Schema(type="string")),
 *     @OA\Parameter(name="established_date", in="query", required=true, description="成立時間", @OA\Schema(type="string")),
 *     @OA\Parameter(name="mobile_phone", in="query", required=true, description="聯絡人行動電話", @OA\Schema(type="string")),
 *     @OA\Parameter(name="contact_email", in="query", required=true, description="聯絡人信箱", @OA\Schema(type="string")),
 *     @OA\Parameter(name="user_id", in="query", required=true, description="負責採購人員id", @OA\Schema(type="string")),
 *     @OA\Parameter(name="currency_id", in="query", required=true, description="幣別id", @OA\Schema(type="string")),
 *     @OA\Parameter(name="paymentterm_id", in="query", required=true, description="付款條件id", @OA\Schema(type="string")),
 *     @OA\Parameter(name="account_category", in="query", required=true, description="科目別", @OA\Schema(type="string")),
 *     @OA\Parameter(name="invoice_title", in="query", required=true, description="發票抬頭", @OA\Schema(type="string")),
 *     @OA\Parameter(name="taxtype", in="query", required=true, description="稅別(抓參數資料param_sn=10)", @OA\Schema(type="string")),
 *     @OA\Parameter(name="taxid", in="query", required=true, description="統一編號 (台灣: 8 碼)", @OA\Schema(type="string")),
 *     @OA\Parameter(name="delivery_method", in="query", required=true, description="送貨方式", @OA\Schema(type="string")),
 *     @OA\Parameter(name="recipient_name", in="query", required=true, description="發票收件人", @OA\Schema(type="string")),
 *     @OA\Parameter(name="recipient_phone", in="query", required=true, description="發票收件人電話", @OA\Schema(type="string")),
 *     @OA\Parameter(name="recipient_email", in="query", required=true, description="發票收件人信箱", @OA\Schema(type="string")),
 *     @OA\Parameter(name="invoice_address", in="query", required=true, description="發票地址", @OA\Schema(type="string")),
 *     @OA\Parameter(name="note", in="query", required=false, description="備註", @OA\Schema(type="string")),
 *     @OA\Parameter(name="is_valid", in="query", required=true, description="是否有效", @OA\Schema(type="string", example=1)),
 *     @OA\Response(
 *         response=200,
 *         description="成功",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="client_no", type="string", example="S003"),
 *             @OA\Property(property="client_shortnm", type="string", example="測試客戶1"),
 *             @OA\Property(property="client_type", type="string", example="個人"),
 *             @OA\Property(property="client_fullnm", type="string", example="測試客戶1"),
 *             @OA\Property(property="zip_code1", type="string", example="12345"),
 *             @OA\Property(property="address1", type="string", example="台北市信義區"),
 *             @OA\Property(property="zip_code2", type="string", example="54321"),
 *             @OA\Property(property="address2", type="string", example="台北市大安區"),
 *             @OA\Property(property="responsible_person", type="string", example="王小明"),
 *             @OA\Property(property="contact_person", type="string", example="李小華"),
 *             @OA\Property(property="contact_phone", type="string", example="0912345678"),
 *             @OA\Property(property="phone", type="string", example="02-12345678"),
 *             @OA\Property(property="fax", type="string", example="02-87654321"),
 *             @OA\Property(property="established_date", type="string", example="2025-03-31"),
 *             @OA\Property(property="mobile_phone", type="string", example="0987654321"),
 *             @OA\Property(property="contact_email", type="string", example="a151815058@gmail.com"),
 *             @OA\Property(property="user_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
 *             @OA\Property(property="currency_id", type="string", example="TWD"),
 *             @OA\Property(property="paymentterm_id", type="string", example="NET30"),
 *             @OA\Property(property="account_category", type="string", example="AC001"),
 *             @OA\Property(property="invoice_title", type="string", example="宏達電股份有限公司"),
 *             @OA\Property(property="taxtype", type="string", example="T001"),
 *             @OA\Property(property="taxid", type="string", example="12345678"),
 *             @OA\Property(property="delivery_method", type="string", example="宅配"),
 *             @OA\Property(property="recipient_name", type="string", example="王小姐"),
 *             @OA\Property(property="recipient_phone", type="string", example="02-22334455"),
 *             @OA\Property(property="recipient_email", type="string", example="invoice@htc.com"),
 *             @OA\Property(property="invoice_address", type="string", example="新北市板橋區縣民大道二段100號"),
 *             @OA\Property(property="note", type="string", example=""),
 *             @OA\Property(property="is_valid", type="string", example="1"),
 *             @OA\Property(property="create_user", type="string", example="admin"),
 *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
 *             @OA\Property(property="update_user", type="string", example="admin"),
 *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="客戶建立失敗"
 *     )
 * )
     */
    // 儲存客戶
    public function store(Request $request)
    {
        try {
            // 驗證請求
            $validator = Validator::make($request->all(),[
                'client_no'           => 'required|string|max:255|unique:clients,client_no',//客戶編號
                'client_shortnm'      => 'required|string|max:255',//客戶簡稱
                'client_type'         =>  'required|string|max:255',//客戶型態
                'client_fullnm'       => 'required|string|max:255',//客戶全名
                'zip_code1'           => 'nullable|string|max:20',//郵遞區域一
                'address1'            => 'nullable|string|max:255',//公司地址
                'zip_code2'           => 'required|string|max:20',//郵遞區域二
                'address2'            => 'required|string|max:255',//送貨地址
                'responsible_person'  => 'required|string|max:255', //負責人           
                'contact_person'      => 'required|string|max:255',  //聯絡人 
                'contact_phone'       => 'required|string|max:255',   //聯絡電話 
                'phone'               => 'required|string|max:20',  //公司電話
                'fax'                 => 'nullable|string|max:10',  //傳真
                'established_date'    => 'required|string|max:20', //成立時間 
                'mobile_phone'        => 'required|string|max:255', //行動電話 
                'contact_email'       => 'required|string|max:255', //聯絡人信箱 
                'user_id'             => 'required|string|max:255', //負責採購人員id
                'currency_id'         => 'required|string|max:255', //幣別id 
                'paymentterm_id'      => 'required|string|max:255', //付款條件id
                'account_category'    => 'required|string|max:255', //科目別 
                'invoice_title'       => 'required|string|max:255', //發票抬頭
                'taxtype'             => 'required|string|max:255', //課稅別 
                'taxid'               => 'required|string|max:255', //統一編號  
                'delivery_method'     => 'required|string|max:255', //送貨方式 
                'recipient_name'      => 'required|string|max:255', //發票收件人
                'recipient_phone'     => 'required|string|max:255', //發票收件人電話
                'recipient_email'     => 'required|string|max:255', //發票收件人信箱
                'invoice_address'     => 'required|string|max:255', //發票地址
                'note'                => 'nullable|string|max:255',//備註
                'is_valid'            => 'required|string'
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => '資料驗證失敗',
                    'errors' => $validator->errors()
                ], 200);
            }
            
        
            // 建立客戶資料
            $Client = Client::create([
                'client_no'           => $request['client_no'],            
                'client_shortnm'      => $request['client_shortnm'],       
                'client_type'         => $request['client_type'],           
                'client_fullnm'       => $request['client_fullnm'],    
                'zip_code1'           => $request['zip_code1'],        
                'address1'            => $request['address1'] ?? null,          
                'zip_code2'           => $request['zip_code2'] ?? null,              
                'address2'            => $request['address2'],          
                'responsible_person'  => $request['responsible_person'],
                'contact_person'      => $request['contact_person'],   
                'contact_phone'       => $request['contact_phone'],     
                'phone'               => $request['phone'],             
                'fax'                 => $request['fax'] ?? null,                  
                'established_date'    => $request['established_date'], 
                'mobile_phone'        => $request['mobile_phone'],      
                'contact_email'       => $request['contact_email'],     
                'user_id'             => $request['user_id'],          
                'currency_id'         => $request['currency_id'],      
                'paymentterm_id'      => $request['paymentterm_id'],   
                'account_category'    => $request['account_category'],  
                'invoice_title'       => $request['invoice_title'],     
                'taxtype'             => $request['taxtype'],           
                'taxid'               => $request['taxid'],            
                'delivery_method'     => $request['delivery_method'],   
                'recipient_name'      => $request['recipient_name'],    
                'recipient_phone'     => $request['recipient_phone'],   
                'recipient_email'     => $request['recipient_email'],  
                'invoice_address'     => $request['invoice_address'],  
                'note'                => $request['note'] ?? null,                 
                'is_valid'            => $request['is_valid'],          
            ]);

            // 回應 JSON
            if (!$Client) {
                return response()->json([
                    'status' => true,
                    'message' => '客戶資料建失敗',
                    'output'    => null
                ], status: 404);
            }else {
                // 回應 JSON
                return response()->json([
                    'status' => true,
                    'message' => 'success',
                    'output'    => $Client
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
            Log::error('建立客戶資料錯誤：' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        }

    }
    /**
     * @OA\GET(
     *     path="/api/cient1/{clientno}",
     *     summary="查詢特定客戶資料",
     *     description="查詢特定客戶資料",
     *     operationId="getclient",
     *     tags={"base_client"},
     *     @OA\Parameter(
     *         name="clientno",
     *         in="path",
     *         required=true,
     *         description="客戶代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
 *             @OA\Property(property="client_no", type="string", example="S003"),
 *             @OA\Property(property="client_shortnm", type="string", example="測試客戶1"),
 *             @OA\Property(property="client_type", type="string", example="一般"),
 *             @OA\Property(property="client_fullnm", type="string", example="測試客戶1"),
 *             @OA\Property(property="zip_code1", type="string", example="12345"),
 *             @OA\Property(property="address1", type="string", example="台北市信義區"),
 *             @OA\Property(property="zip_code2", type="string", example="54321"),
 *             @OA\Property(property="address2", type="string", example="台北市大安區"),
 *             @OA\Property(property="responsible_person", type="string", example="王小明"),
 *             @OA\Property(property="contact_person", type="string", example="李小華"),
 *             @OA\Property(property="contact_phone", type="string", example="0912345678"),
 *             @OA\Property(property="phone", type="string", example="02-12345678"),
 *             @OA\Property(property="fax", type="string", example="02-87654321"),
 *             @OA\Property(property="established_date", type="string", example="2025-03-31"),
 *             @OA\Property(property="mobile_phone", type="string", example="0987654321"),
 *             @OA\Property(property="contact_email", type="string", example="a151815058@gmail.com"),
 *             @OA\Property(property="user_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
 *             @OA\Property(property="currency_id", type="string", example="TWD"),
 *             @OA\Property(property="paymentterm_id", type="string", example="NET30"),
 *             @OA\Property(property="account_category", type="string", example="AC001"),
 *             @OA\Property(property="invoice_title", type="string", example="宏達電股份有限公司"),
 *             @OA\Property(property="taxtype", type="string", example="T001"),
 *             @OA\Property(property="taxid", type="string", example="12345678"),
 *             @OA\Property(property="delivery_method", type="string", example="宅配"),
 *             @OA\Property(property="recipient_name", type="string", example="王小姐"),
 *             @OA\Property(property="recipient_phone", type="string", example="02-22334455"),
 *             @OA\Property(property="recipient_email", type="string", example="invoice@htc.com"),
 *             @OA\Property(property="invoice_address", type="string", example="新北市板橋區縣民大道二段100號"),
 *             @OA\Property(property="note", type="string", example=""),
 *             @OA\Property(property="is_valid", type="string", example="1"),
 *             @OA\Property(property="create_user", type="string", example="admin"),
 *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
 *             @OA\Property(property="update_user", type="string", example="admin"),
 *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到客戶資料"
     *     )
     * )
     */
    // 🔍 查詢客戶
    public function show($clientNo)
    {
        try {
            $Client = Client::findByclientNo($clientNo)->where('is_valid','1')->first();
            
            if (!$Client) {
                return response()->json([
                    'status' => true,
                    'message' => '客戶未找到',
                    'output'    => null
                ], 404);
            }

            return response()->json([                
                'status' => true,
                'message' => 'success',
                'output'    => $Client
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
            Log::error('客戶資料錯誤：' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/clients/valid",
     *     summary="查詢所有有效客戶(含關鍵字查詢)",
     *     description="查詢所有有效客戶(含關鍵字查詢)",
     *     operationId="getallclient",
     *     tags={"base_client"},
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
 *             @OA\Property(property="client_no", type="string", example="S003"),
 *             @OA\Property(property="client_shortnm", type="string", example="測試客戶1"),
 *             @OA\Property(property="client_type", type="string", example="一般"),
 *             @OA\Property(property="client_fullnm", type="string", example="測試客戶1"),
 *             @OA\Property(property="zip_code1", type="string", example="12345"),
 *             @OA\Property(property="address1", type="string", example="台北市信義區"),
 *             @OA\Property(property="zip_code2", type="string", example="54321"),
 *             @OA\Property(property="address2", type="string", example="台北市大安區"),
 *             @OA\Property(property="responsible_person", type="string", example="王小明"),
 *             @OA\Property(property="contact_person", type="string", example="李小華"),
 *             @OA\Property(property="contact_phone", type="string", example="0912345678"),
 *             @OA\Property(property="phone", type="string", example="02-12345678"),
 *             @OA\Property(property="fax", type="string", example="02-87654321"),
 *             @OA\Property(property="established_date", type="string", example="2025-03-31"),
 *             @OA\Property(property="mobile_phone", type="string", example="0987654321"),
 *             @OA\Property(property="contact_email", type="string", example="a151815058@gmail.com"),
 *             @OA\Property(property="user_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
 *             @OA\Property(property="currency_id", type="string", example="TWD"),
 *             @OA\Property(property="paymentterm_id", type="string", example="NET30"),
 *             @OA\Property(property="account_category", type="string", example="AC001"),
 *             @OA\Property(property="invoice_title", type="string", example="宏達電股份有限公司"),
 *             @OA\Property(property="taxtype", type="string", example="T001"),
 *             @OA\Property(property="taxid", type="string", example="12345678"),
 *             @OA\Property(property="delivery_method", type="string", example="宅配"),
 *             @OA\Property(property="recipient_name", type="string", example="王小姐"),
 *             @OA\Property(property="recipient_phone", type="string", example="02-22334455"),
 *             @OA\Property(property="recipient_email", type="string", example="invoice@htc.com"),
 *             @OA\Property(property="invoice_address", type="string", example="新北市板橋區縣民大道二段100號"),
 *             @OA\Property(property="note", type="string", example=""),
 *             @OA\Property(property="is_valid", type="string", example="1"),
 *             @OA\Property(property="create_user", type="string", example="admin"),
 *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
 *             @OA\Property(property="update_user", type="string", example="admin"),
 *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到有效客戶"
     *     )
     * )
     */
    // 🔍 查詢所有有效客戶
    public function getValidclients(Request $request)
    {
        try {

            $keyword = $request->query('keyword'); // 可為 null

            // 使用 DB::select 進行關鍵字查詢
            if($keyword != null) {
                $likeKeyword = '%' . $keyword . '%';
                $sql = "select  *
                        from clients
                        where clients.is_valid = '1'  
                        and ( clients.client_no LIKE ? 
                           OR clients.client_shortnm LIKE ?
                           OR clients.client_fullnm LIKE ?
                           OR clients.address1 LIKE ?
                           OR clients.address2 LIKE ?)
                        order by update_time,create_time asc;";

                $Client = DB::select($sql, [$likeKeyword, $likeKeyword,$likeKeyword, $likeKeyword, $likeKeyword]);

            } else {
                $Client = Client::where('is_valid', '1')->get();
            }
            if (!$Client) {
                return response()->json([
                    'status' => true,
                    'message' => '未找到有效客戶',
                    'output'    => $Client
                ], 404);
            }
            return response()->json([                
                'status' => true,
                'message' => 'success',
                'output'    => $Client
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
            Log::error('客戶資料錯誤：' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        } 
    }
    /**
     * @OA\patch(
     *     path="/api/client/{clientno}/disable",
     *     summary="刪除特定客戶",
     *     description="刪除特定客戶",
     *     operationId="deleteclient",
     *     tags={"base_client"},
     *     @OA\Parameter(
     *         name="clientno",
     *         in="path",
     *         required=true,
     *         description="客戶代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
 *             @OA\Property(property="client_no", type="string", example="S003"),
 *             @OA\Property(property="client_shortnm", type="string", example="測試客戶1"),
 *             @OA\Property(property="client_type", type="string", example="一般"),
 *             @OA\Property(property="client_fullnm", type="string", example="測試客戶1"),
 *             @OA\Property(property="zip_code1", type="string", example="12345"),
 *             @OA\Property(property="address1", type="string", example="台北市信義區"),
 *             @OA\Property(property="zip_code2", type="string", example="54321"),
 *             @OA\Property(property="address2", type="string", example="台北市大安區"),
 *             @OA\Property(property="responsible_person", type="string", example="王小明"),
 *             @OA\Property(property="contact_person", type="string", example="李小華"),
 *             @OA\Property(property="contact_phone", type="string", example="0912345678"),
 *             @OA\Property(property="phone", type="string", example="02-12345678"),
 *             @OA\Property(property="fax", type="string", example="02-87654321"),
 *             @OA\Property(property="established_date", type="string", example="2025-03-31"),
 *             @OA\Property(property="mobile_phone", type="string", example="0987654321"),
 *             @OA\Property(property="contact_email", type="string", example="a151815058@gmail.com"),
 *             @OA\Property(property="user_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
 *             @OA\Property(property="currency_id", type="string", example="TWD"),
 *             @OA\Property(property="paymentterm_id", type="string", example="NET30"),
 *             @OA\Property(property="account_category", type="string", example="AC001"),
 *             @OA\Property(property="invoice_title", type="string", example="宏達電股份有限公司"),
 *             @OA\Property(property="taxtype", type="string", example="T001"),
 *             @OA\Property(property="taxid", type="string", example="12345678"),
 *             @OA\Property(property="delivery_method", type="string", example="宅配"),
 *             @OA\Property(property="recipient_name", type="string", example="王小姐"),
 *             @OA\Property(property="recipient_phone", type="string", example="02-22334455"),
 *             @OA\Property(property="recipient_email", type="string", example="invoice@htc.com"),
 *             @OA\Property(property="invoice_address", type="string", example="新北市板橋區縣民大道二段100號"),
 *             @OA\Property(property="note", type="string", example=""),
 *             @OA\Property(property="is_valid", type="string", example="1"),
 *             @OA\Property(property="create_user", type="string", example="admin"),
 *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
 *             @OA\Property(property="update_user", type="string", example="admin"),
 *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )   
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到客戶"
     *     )
     * )
     */
    // 🔍 刪除特定客戶
    public function disable($clientNo)
    {
        try {
            $Client = Client::findByclientNo($clientNo)->where('is_valid','1')->get();
            
            if (!$Client) {
                return response()->json([
                    'status' => true,
                    'message' => '客戶未找到',
                    'output'    => null
                ], 404);
            }

            $Client->is_valid = 0;
            $Client->update_user = 'admin';
            $Client->update_time = now();
            $Client->save();

            return response()->json([
                'status' => true,
                'message' => 'success',
                'output'    => $Client
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
            Log::error('客戶資料錯誤：' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        } 
    }
    /**
     * @OA\get(
     *     path="/api/clients/showconst",
     *     summary="列出所有客戶需要的常用(下拉、彈窗)",
     *     description="列出所有客戶需要的常用(下拉、彈窗)",
     *     operationId="show_client_aLL_const",
     *     tags={"base_client"},
     *     @OA\Response(
     *         response=200,
     *         description="成功"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="客戶需要的常用未找到"
     *     )
     * )
     */
    // 列出所有客戶需要的常用(下拉、彈窗)
    public function showconst($constant='all'){
        // 查詢 '所有有效幣別資料' 的資料
        $SysCode = Currency::where('is_valid', '1')->where('is_valid','1')->get();
        // 查詢 '所有稅別資料' 的資料
        $SysCode1 = SysCode::where('param_sn', '04')->where('is_valid','1')->get();
        // 查詢 '所有有效付款條件' 的資料
        $SysCode2 = PaymentTerm::where('is_valid', '1')->where('is_valid','1')->get();
        // 付款條件(當月、次月的常數資料)
        $SysCode4 = PaymentTerm::where('is_valid', '1')->where('is_valid','1')->get();
        // 查詢 '所有有效人員' 的資料
        $SysCode3 = SysUser::with('depts')->where('is_valid', '1')->get();
        // 付款條件(當月、次月的常數資料)
        $SysCode4 = PaymentTerm::where('is_valid', '1')->where('is_valid','1')->get();
        // 發票寄送方式
        $SysCode5 = SysCode::where('param_sn', '10')->where('is_valid','1')->get();
        
        try {
            // 檢查是否有結果
            if ($SysCode->isEmpty() ) {
                return response()->json([
                    'status' => true,
                    'message' => '常用資料未找到',
                    'currencyOption' => null,
                    'taxtypeOption' => null,
                    'paymenttermOption' => null,
                    'sysuserOption' => null,
                    'paymentterm2Option' => null,
                    'deliverymethodOption' => null
                ], 404);
            }
    
            // 返回查詢結果
            return response()->json([
                'status' => true,
                'message' => 'success',
                'currencyOption' => $SysCode,
                'taxtypeOption' => $SysCode1,
                'paymenttermOption' => $SysCode2,
                'sysuserOption' => $SysCode3,
                'paymentterm2Option' => $SysCode4,
                'deliverymethodOption' => $SysCode5
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
