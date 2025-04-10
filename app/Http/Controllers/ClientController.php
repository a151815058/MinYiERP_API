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
     *     operationId="createClient",
     *     tags={"Base_Client"},
     *     @OA\Parameter(
     *         name="client_no",
     *         in="query",
     *         required=true,
     *         description="客戶編號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="client_shortnm",
     *         in="query",
     *         required=true,
     *         description="客戶簡稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="client_fullnm",
     *         in="query",
     *         required=true,
     *         description="客戶全名",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="zip_code1",
     *         in="query",
     *         required=true,
     *         description="郵遞區號 1",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="address1",
     *         in="query",
     *         required=true,
     *         description="公司地址 1",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="zip_code2",
     *         in="query",
     *         required=false,
     *         description="郵遞區號 2 (選填)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="address2",
     *         in="query",
     *         required=false,
     *         description="公司地址 2 (選填)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="taxid",
     *         in="query",
     *         required=true,
     *         description="統一編號 (台灣: 8 碼)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="responsible_person",
     *         in="query",
     *         required=true,
     *         description="負責人",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="established_date",
     *         in="query",
     *         required=true,
     *         description="成立時間",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         required=true,
     *         description="公司電話",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="fax",
     *         in="query",
     *         required=false,
     *         description="公司傳真 (選填)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="contact_person",
     *         in="query",
     *         required=true,
     *         description="聯絡人",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="contact_phone",
     *         in="query",
     *         required=true,
     *         description="聯絡人電話",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="mobile_phone",
     *         in="query",
     *         required=true,
     *         description="聯絡人行動電話",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="contact_email",
     *         in="query",
     *         required=true,
     *         description="聯絡人信箱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="currency_id",
     *         in="query",
     *         required=true,
     *         description="幣別id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="taxtype",
     *         in="query",
     *         required=true,
     *         description="稅別(抓參數資料)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="paymentterm_id",
     *         in="query",
     *         required=true,
     *         description="付款條件id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=true,
     *         description="負責採購人員id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="note",
     *         in="query",
     *         required=false,
     *         description="備註",
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
     *             @OA\Property(property="client_no", type="string", example="S003"),
     *             @OA\Property(property="client_shortnm", type="string", example="測試客戶1"),
     *             @OA\Property(property="client_fullnm", type="string", example="測試客戶1"),
     *             @OA\Property(property="zip_code1", type="string", example="12345"),
     *             @OA\Property(property="address1", type="string", example="台北市信義區"),
     *             @OA\Property(property="zip_code2", type="string", example="54321"),
     *             @OA\Property(property="address2", type="string", example="台北市大安區"),
     *             @OA\Property(property="taxid", type="string", example="12345678"),
     *             @OA\Property(property="responsible_person", type="string", example="王小明"),
     *             @OA\Property(property="established_date", type="string", example="2025-03-31"),
     *             @OA\Property(property="phone", type="string", example="02-12345678"),
     *             @OA\Property(property="fax", type="string", example="02-87654321"),
     *             @OA\Property(property="contact_person", type="string", example="李小華"),
     *             @OA\Property(property="contact_phone", type="string", example="0912345678"),
     *             @OA\Property(property="mobile_phone", type="string", example="0987654321"),
     *             @OA\Property(property="contact_email", type="string", example="a151815058@gmail.com"),
     *             @OA\Property(property="currency_id", type="string", example="TWD"),
     *             @OA\Property(property="taxtype", type="string", example="T001"),
     *             @OA\Property(property="paymentterm_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="user_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
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
                'client_no'         => 'required|string|max:255|unique:clients,client_no',
                'client_shortnm'    => 'required|string|max:255',
                'client_fullnm'     => 'required|string|max:255',
                'zip_code1'           => 'required|string|max:20',
                'address1'           => 'required|string|max:255',
                'zip_code2'           => 'nullable|string|max:20',
                'address2'           => 'nullable|string|max:255',
                'taxid'              => 'required|string|max:255', 
                'responsible_person'  => 'required|string|max:255',   
                'established_date'    => 'required|string|max:20',  
                'phone'              => 'required|string|max:20',  
                'fax'                => 'nullable|string|max:10',  
                'contact_person'      => 'required|string|max:255',  
                'contact_phone'       => 'required|string|max:255',  
                'mobile_phone'        => 'required|string|max:255',  
                'contact_email'       => 'required|string|max:255',  
                'currency_id'         => 'required|string|max:255',  
                'taxtype'            => 'required|string|max:255',  
                'paymentterm_id'      => 'required|string|max:255',    
                'user_id'             => 'required|string|max:255',     
                'note'               => 'nullable|string|max:255',
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
                'client_no'     => $request['client_no'],
                'client_shortnm'     => $request['client_shortnm'],
                'client_fullnm'   => $request['client_fullnm'],
                'zip_code1'   => $request['zip_code1'],
                'address1' => $request['address1'],
                'zip_code2'   => $request['zip_code2']?? null,
                'address2' => $request['address2']?? null,
                'taxid'   => $request['taxid'],
                'responsible_person'  => $request['responsible_person'],
                'established_date'   => $request['established_date'],
                'phone' => $request['phone'],
                'fax'   => $request['fax']?? null,
                'contact_person'  => $request['contact_person'],
                'contact_phone'   => $request['contact_phone'],
                'mobile_phone' => $request['mobile_phone'],
                'contact_email'   => $request['contact_email'],
                'currency_id'  => $request['currency_id'],
                'taxtype'  => $request['taxtype'],
                'paymentterm_id'  => $request['paymentterm_id'],
                'user_id'  => $request['user_id'],
                'note'       => $request['note'] ?? null,
                'is_valid'    => $request['is_valid']
            ]);

            // 回應 JSON
            if (!$Client) {
                return response()->json([
                    'status' => false,
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
     *     path="/api/Client/{clientNo}",
     *     summary="查詢特定客戶資料",
     *     description="查詢特定客戶資料",
     *     operationId="getClient",
     *     tags={"Base_Client"},
     *     @OA\Parameter(
     *         name="clientNo",
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
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="client_no", type="string", example="S003"),
     *             @OA\Property(property="client_shortnm", type="string", example="測試客戶1"),
     *             @OA\Property(property="client_fullnm", type="string", example="測試客戶1"),
     *             @OA\Property(property="zip_code1", type="string", example="12345"),
     *             @OA\Property(property="address1", type="string", example="台北市信義區"),
     *             @OA\Property(property="zip_code2", type="string", example="54321"),
     *             @OA\Property(property="address2", type="string", example="台北市大安區"),
     *             @OA\Property(property="taxid", type="string", example="12345678"),
     *             @OA\Property(property="responsible_person", type="string", example="王小明"),
     *             @OA\Property(property="established_date", type="string", example="2025-03-31"),
     *             @OA\Property(property="phone", type="string", example="02-12345678"),
     *             @OA\Property(property="fax", type="string", example="02-87654321"),
     *             @OA\Property(property="contact_person", type="string", example="李小華"),
     *             @OA\Property(property="contact_phone", type="string", example="0912345678"),
     *             @OA\Property(property="mobile_phone", type="string", example="0987654321"),
     *             @OA\Property(property="contact_email", type="string", example="a151815058@gmail.com"),
     *             @OA\Property(property="currency_id", type="string", example="TWD"),
     *             @OA\Property(property="taxtype", type="string", example="T001"),
     *             @OA\Property(property="paymentterm_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="user_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
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
            $Client = Client::findByclientNo($clientNo);
            
            if (!$Client) {
                return response()->json([
                    'status' => false,
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
     *     path="/api/Clients/valid",
     *     summary="查詢所有有效客戶",
     *     description="查詢所有有效客戶",
     *     operationId="GetAllClient",
     *     tags={"Base_Client"},
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="client_no", type="string", example="S003"),
     *             @OA\Property(property="client_shortnm", type="string", example="測試客戶1"),
     *             @OA\Property(property="client_fullnm", type="string", example="測試客戶1"),
     *             @OA\Property(property="zip_code1", type="string", example="12345"),
     *             @OA\Property(property="address1", type="string", example="台北市信義區"),
     *             @OA\Property(property="zip_code2", type="string", example="54321"),
     *             @OA\Property(property="address2", type="string", example="台北市大安區"),
     *             @OA\Property(property="taxid", type="string", example="12345678"),
     *             @OA\Property(property="responsible_person", type="string", example="王小明"),
     *             @OA\Property(property="established_date", type="string", example="2025-03-31"),
     *             @OA\Property(property="phone", type="string", example="02-12345678"),
     *             @OA\Property(property="fax", type="string", example="02-87654321"),
     *             @OA\Property(property="contact_person", type="string", example="李小華"),
     *             @OA\Property(property="contact_phone", type="string", example="0912345678"),
     *             @OA\Property(property="mobile_phone", type="string", example="0987654321"),
     *             @OA\Property(property="contact_email", type="string", example="a151815058@gmail.com"),
     *             @OA\Property(property="currency_id", type="string", example="TWD"),
     *             @OA\Property(property="taxtype", type="string", example="T001"),
     *             @OA\Property(property="paymentterm_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="user_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
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
    public function getValidClients()
    {
        try {
            $Client = Client::getValidClients();
            if ($Client->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => '未找到有效客戶',
                    'output'    => null
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
     *     path="/api/Client/{clientNo}/disable",
     *     summary="刪除特定客戶",
     *     description="刪除特定客戶",
     *     operationId="DeleteClient",
     *     tags={"Base_Client"},
     *     @OA\Parameter(
     *         name="clientNo",
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
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="client_no", type="string", example="S003"),
     *             @OA\Property(property="client_shortnm", type="string", example="測試客戶1"),
     *             @OA\Property(property="client_fullnm", type="string", example="測試客戶1"),
     *             @OA\Property(property="zip_code1", type="string", example="12345"),
     *             @OA\Property(property="address1", type="string", example="台北市信義區"),
     *             @OA\Property(property="zip_code2", type="string", example="54321"),
     *             @OA\Property(property="address2", type="string", example="台北市大安區"),
     *             @OA\Property(property="taxid", type="string", example="12345678"),
     *             @OA\Property(property="responsible_person", type="string", example="王小明"),
     *             @OA\Property(property="established_date", type="string", example="2025-03-31"),
     *             @OA\Property(property="phone", type="string", example="02-12345678"),
     *             @OA\Property(property="fax", type="string", example="02-87654321"),
     *             @OA\Property(property="contact_person", type="string", example="李小華"),
     *             @OA\Property(property="contact_phone", type="string", example="0912345678"),
     *             @OA\Property(property="mobile_phone", type="string", example="0987654321"),
     *             @OA\Property(property="contact_email", type="string", example="a151815058@gmail.com"),
     *             @OA\Property(property="currency_id", type="string", example="TWD"),
     *             @OA\Property(property="taxtype", type="string", example="T001"),
     *             @OA\Property(property="paymentterm_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="user_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="string", example="0"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
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
            $Client = Client::findByclientNo($clientNo);
            
            if (!$Client) {
                return response()->json([
                    'status' => false,
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
     *     path="/api/Clients/showConst",
     *     summary="列出所有客戶需要的常用(下拉、彈窗)",
     *     description="列出所有客戶需要的常用(下拉、彈窗)",
     *     operationId="Show_Client_ALL_Const",
     *     tags={"Base_Client"},
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
    public function showConst($constant='all'){
        // 查詢 '所有有效幣別資料' 的資料
        $SysCode = Currency::where('is_valid', '1')->get();
        // 查詢 '所有稅別資料' 的資料
        $SysCode1 = SysCode::where('param_sn', '04')->get();
        // 查詢 '所有有效付款條件' 的資料
        $SysCode2 = PaymentTerm::where('is_valid', '1')->get();
        // 查詢 '所有有效人員' 的資料
        $SysCode3 = SysUser::with('depts')->where('is_valid', '1')->get();
        try {
            // 檢查是否有結果
            if ($SysCode->isEmpty() ) {
                return response()->json([
                    'status' => false,
                    'message' => '常用資料未找到',
                    'currencyOption' => null,
                    'taxtypeOption' => null,
                    'paymenttermOption' => null,
                    'sysuserOption' => null
                ], 404);
            }
    
            // 返回查詢結果
            return response()->json([
                'status' => true,
                'message' => 'success',
                'currencyOption' => $SysCode,
                'taxtypeOption' => $SysCode1,
                'paymenttermOption' => $SysCode2,
                'sysuserOption' => $SysCode3
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
