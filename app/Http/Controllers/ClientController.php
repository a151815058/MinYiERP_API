<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;

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
     *         name="clientNo",
     *         in="query",
     *         required=true,
     *         description="客戶編號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="clientShortNM",
     *         in="query",
     *         required=true,
     *         description="客戶簡稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="clientFullNM",
     *         in="query",
     *         required=true,
     *         description="客戶全名",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="ZipCode1",
     *         in="query",
     *         required=true,
     *         description="郵遞區號 1",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Address1",
     *         in="query",
     *         required=true,
     *         description="公司地址 1",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="ZipCode2",
     *         in="query",
     *         required=false,
     *         description="郵遞區號 2 (選填)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Address2",
     *         in="query",
     *         required=false,
     *         description="公司地址 2 (選填)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="TaxID",
     *         in="query",
     *         required=true,
     *         description="統一編號 (台灣: 8 碼)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="ResponsiblePerson",
     *         in="query",
     *         required=true,
     *         description="負責人",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="EstablishedDate",
     *         in="query",
     *         required=true,
     *         description="成立時間",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Phone",
     *         in="query",
     *         required=true,
     *         description="公司電話",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Fax",
     *         in="query",
     *         required=false,
     *         description="公司傳真 (選填)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="ContactPerson",
     *         in="query",
     *         required=true,
     *         description="聯絡人",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="ContactPhone",
     *         in="query",
     *         required=true,
     *         description="聯絡人電話",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="MobilePhone",
     *         in="query",
     *         required=true,
     *         description="聯絡人行動電話",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="ContactEmail",
     *         in="query",
     *         required=true,
     *         description="聯絡人信箱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="CurrencyID",
     *         in="query",
     *         required=true,
     *         description="幣別 (ISO 3碼: USD, TWD)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="TaxType",
     *         in="query",
     *         required=true,
     *         description="稅別 (應稅內含、應稅外加、免稅、零稅率等)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="PaymentTermID",
     *         in="query",
     *         required=true,
     *         description="付款條件 (付款條件代碼)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="UserID",
     *         in="query",
     *         required=true,
     *         description="負責採購人員",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Note",
     *         in="query",
     *         required=false,
     *         description="備註",
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
     *             @OA\Property(property="clientNo", type="string", example="S003"),
     *             @OA\Property(property="clientShortNM", type="string", example="測試客戶1"),
     *             @OA\Property(property="clientFullNM", type="string", example="測試客戶1"),
     *             @OA\Property(property="ZipCode1", type="string", example="12345"),
     *             @OA\Property(property="Address1", type="string", example="台北市信義區"),
     *             @OA\Property(property="ZipCode2", type="string", example="54321"),
     *             @OA\Property(property="Address2", type="string", example="台北市大安區"),
     *             @OA\Property(property="TaxID", type="string", example="12345678"),
     *             @OA\Property(property="ResponsiblePerson", type="string", example="王小明"),
     *             @OA\Property(property="EstablishedDate", type="string", example="2025-03-31"),
     *             @OA\Property(property="Phone", type="string", example="02-12345678"),
     *             @OA\Property(property="Fax", type="string", example="02-87654321"),
     *             @OA\Property(property="ContactPerson", type="string", example="李小華"),
     *             @OA\Property(property="ContactPhone", type="string", example="0912345678"),
     *             @OA\Property(property="MobilePhone", type="string", example="0987654321"),
     *             @OA\Property(property="ContactEmail", type="string", example="a151815058@gmail.com"),
     *             @OA\Property(property="CurrencyID", type="string", example="TWD"),
     *             @OA\Property(property="TaxType", type="string", example="T001"),
     *             @OA\Property(property="PaymentTermID", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="UserID", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="Note", type="string", example=""),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
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
            $validated = $request->validate([
                'clientNo'         => 'required|string|max:255|unique:clients,clientNo',
                'clientShortNM'    => 'required|string|max:255',
                'clientFullNM'     => 'required|string|max:255',
                'ZipCode1'           => 'required|string|max:20',
                'Address1'           => 'required|string|max:255',
                'ZipCode2'           => 'nullable|string|max:20',
                'Address2'           => 'nullable|string|max:255',
                'TaxID'              => 'required|string|max:255', 
                'ResponsiblePerson'  => 'required|string|max:255',   
                'EstablishedDate'    => 'required|string|max:20',  
                'Phone'              => 'required|string|max:20',  
                'Fax'                => 'nullable|string|max:10',  
                'ContactPerson'      => 'required|string|max:255',  
                'ContactPhone'       => 'required|string|max:255',  
                'MobilePhone'        => 'required|string|max:255',  
                'ContactEmail'       => 'required|string|max:255',  
                'CurrencyID'         => 'required|string|max:255',  
                'TaxType'            => 'required|string|max:255',  
                'PaymentTermID'      => 'required|string|max:255',    
                'UserID'             => 'required|string|max:255',     
                'Note'               => 'nullable|string|max:255',
                'IsValid'            => 'required|boolean'
            ]);
            
        
            // 建立客戶資料
            $Client = Client::create([
                'clientNo'     => $validated['clientNo'],
                'clientShortNM'     => $validated['clientShortNM'],
                'clientFullNM'   => $validated['clientFullNM'],
                'ZipCode1'   => $validated['ZipCode1'],
                'Address1' => $validated['Address1'],
                'ZipCode2'   => $validated['ZipCode2']?? null,
                'Address2' => $validated['Address2']?? null,
                'TaxID'   => $validated['TaxID'],
                'ResponsiblePerson'  => $validated['ResponsiblePerson'],
                'EstablishedDate'   => $validated['EstablishedDate'],
                'Phone' => $validated['Phone'],
                'Fax'   => $validated['Fax']?? null,
                'ContactPerson'  => $validated['ContactPerson'],
                'ContactPhone'   => $validated['ContactPhone'],
                'MobilePhone' => $validated['MobilePhone'],
                'ContactEmail'   => $validated['ContactEmail'],
                'CurrencyID'  => $validated['CurrencyID'],
                'TaxType'  => $validated['TaxType'],
                'PaymentTermID'  => $validated['PaymentTermID'],
                'UserID'  => $validated['UserID'],
                'Note'       => $validated['Note'] ?? null,
                'IsValid'    => $validated['IsValid']
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
     *             @OA\Property(property="clientNo", type="string", example="S003"),
     *             @OA\Property(property="clientShortNM", type="string", example="測試客戶1"),
     *             @OA\Property(property="clientFullNM", type="string", example="測試客戶1"),
     *             @OA\Property(property="ZipCode1", type="string", example="12345"),
     *             @OA\Property(property="Address1", type="string", example="台北市信義區"),
     *             @OA\Property(property="ZipCode2", type="string", example="54321"),
     *             @OA\Property(property="Address2", type="string", example="台北市大安區"),
     *             @OA\Property(property="TaxID", type="string", example="12345678"),
     *             @OA\Property(property="ResponsiblePerson", type="string", example="王小明"),
     *             @OA\Property(property="EstablishedDate", type="string", example="2025-03-31"),
     *             @OA\Property(property="Phone", type="string", example="02-12345678"),
     *             @OA\Property(property="Fax", type="string", example="02-87654321"),
     *             @OA\Property(property="ContactPerson", type="string", example="李小華"),
     *             @OA\Property(property="ContactPhone", type="string", example="0912345678"),
     *             @OA\Property(property="MobilePhone", type="string", example="0987654321"),
     *             @OA\Property(property="ContactEmail", type="string", example="a151815058@gmail.com"),
     *             @OA\Property(property="CurrencyID", type="string", example="TWD"),
     *             @OA\Property(property="TaxType", type="string", example="T001"),
     *             @OA\Property(property="PaymentTermID", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="UserID", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="Note", type="string", example=""),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
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
     *     path="/api/Client/valid",
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
     *             @OA\Property(property="clientNo", type="string", example="S003"),
     *             @OA\Property(property="clientShortNM", type="string", example="測試客戶1"),
     *             @OA\Property(property="clientFullNM", type="string", example="測試客戶1"),
     *             @OA\Property(property="ZipCode1", type="string", example="12345"),
     *             @OA\Property(property="Address1", type="string", example="台北市信義區"),
     *             @OA\Property(property="ZipCode2", type="string", example="54321"),
     *             @OA\Property(property="Address2", type="string", example="台北市大安區"),
     *             @OA\Property(property="TaxID", type="string", example="12345678"),
     *             @OA\Property(property="ResponsiblePerson", type="string", example="王小明"),
     *             @OA\Property(property="EstablishedDate", type="string", example="2025-03-31"),
     *             @OA\Property(property="Phone", type="string", example="02-12345678"),
     *             @OA\Property(property="Fax", type="string", example="02-87654321"),
     *             @OA\Property(property="ContactPerson", type="string", example="李小華"),
     *             @OA\Property(property="ContactPhone", type="string", example="0912345678"),
     *             @OA\Property(property="MobilePhone", type="string", example="0987654321"),
     *             @OA\Property(property="ContactEmail", type="string", example="a151815058@gmail.com"),
     *             @OA\Property(property="CurrencyID", type="string", example="TWD"),
     *             @OA\Property(property="TaxType", type="string", example="T001"),
     *             @OA\Property(property="PaymentTermID", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="UserID", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="Note", type="string", example=""),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
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
     *             @OA\Property(property="clientNo", type="string", example="S003"),
     *             @OA\Property(property="clientShortNM", type="string", example="測試客戶1"),
     *             @OA\Property(property="clientFullNM", type="string", example="測試客戶1"),
     *             @OA\Property(property="ZipCode1", type="string", example="12345"),
     *             @OA\Property(property="Address1", type="string", example="台北市信義區"),
     *             @OA\Property(property="ZipCode2", type="string", example="54321"),
     *             @OA\Property(property="Address2", type="string", example="台北市大安區"),
     *             @OA\Property(property="TaxID", type="string", example="12345678"),
     *             @OA\Property(property="ResponsiblePerson", type="string", example="王小明"),
     *             @OA\Property(property="EstablishedDate", type="string", example="2025-03-31"),
     *             @OA\Property(property="Phone", type="string", example="02-12345678"),
     *             @OA\Property(property="Fax", type="string", example="02-87654321"),
     *             @OA\Property(property="ContactPerson", type="string", example="李小華"),
     *             @OA\Property(property="ContactPhone", type="string", example="0912345678"),
     *             @OA\Property(property="MobilePhone", type="string", example="0987654321"),
     *             @OA\Property(property="ContactEmail", type="string", example="a151815058@gmail.com"),
     *             @OA\Property(property="CurrencyID", type="string", example="TWD"),
     *             @OA\Property(property="TaxType", type="string", example="T001"),
     *             @OA\Property(property="PaymentTermID", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="UserID", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="Note", type="string", example=""),
     *             @OA\Property(property="IsValid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
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

            $Client->IsValid = 0;
            $Client->UpdateUser = 'admin';
            $Client->UpdateTime = now();
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
}
