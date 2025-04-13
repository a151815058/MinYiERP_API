<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Currency;
use App\Models\PaymentTerm;
use App\Models\SysCode;
use App\Models\SysUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/createsupplier",
     *     summary="新增供應商資料",
     *     description="新增供應商資料",
     *     operationId="createSupplier",
     *     tags={"Base_Supplier"},
     *     @OA\Parameter(
     *         name="supplier_no",
     *         in="query",
     *         required=true,
     *         description="供應商編號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="supplier_shortnm",
     *         in="query",
     *         required=true,
     *         description="供應商簡稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="supplier_fullnm",
     *         in="query",
     *         required=true,
     *         description="供應商全名",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="zipcode1",
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
     *         name="zipcode2",
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
     *         name="currencyid",
     *         in="query",
     *         required=true,
     *         description="幣別id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="tax_type",
     *         in="query",
     *         required=true,
     *         description="稅別 (參數代碼param_sn=04)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="payment_termid",
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
     *             @OA\Property(property="supplier_no", type="string", example="S003"),
     *             @OA\Property(property="supplier_shortnm", type="string", example="測試供應商1"),
     *             @OA\Property(property="supplier_fullnm", type="string", example="測試供應商1"),
     *             @OA\Property(property="zipcode1", type="string", example="12345"),
     *             @OA\Property(property="address1", type="string", example="台北市信義區"),
     *             @OA\Property(property="zipcode2", type="string", example="54321"),
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
     *             @OA\Property(property="currencyid", type="string", example="TWD"),
     *             @OA\Property(property="tax_type", type="string", example="T001"),
     *             @OA\Property(property="payment_termid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="user_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="供應商建立失敗"
     *     )
     * )
     */
    // 儲存供應商
    public function store(Request $request)
    {
        try {
            // 驗證請求
            $validator = Validator::make($request->all(),[
                'supplier_no'         => 'required|string|max:255|unique:supplier,supplier_no',
                'supplier_shortnm'    => 'required|string|max:255',
                'supplier_fullnm'     => 'required|string|max:255',
                'zipcode1'           => 'required|string|max:20',
                'address1'           => 'required|string|max:255',
                'zipcode2'           => 'nullable|string|max:20',
                'address2'           => 'nullable|string|max:255',
                'taxid'              => 'required|string|max:255', 
                'responsible_person'  => 'required|string|max:255',   
                'established_date'    => 'required|string|max:20',  
                'phone'              => 'required|string|max:20',  
                'fax'                => 'required|string|max:10',  
                'contact_person'      => 'required|string|max:255',  
                'contact_phone'       => 'required|string|max:255',  
                'mobile_phone'        => 'required|string|max:255',  
                'contact_email'       => 'required|string|max:255',  
                'currencyid'         => 'required|string|max:255',  
                'tax_type'            => 'required|string|max:255',  
                'payment_termid'      => 'required|string|max:255',    
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
    
        // 建立供應商資料
        $supplier = Supplier::create([
            'supplier_no'     => $request['supplier_no'],
            'supplier_shortnm'     => $request['supplier_shortnm'],
            'supplier_fullnm'   => $request['supplier_fullnm'],
            'zipcode1'   => $request['zipcode1'],
            'address1' => $request['address1'],
            'zipcode2'   => $request['zipcode2']?? null,
            'address2' => $request['address2']?? null,
            'taxid'   => $request['taxid'],
            'responsible_person'  => $request['responsible_person'],
            'established_date'   => $request['established_date'],
            'phone' => $request['phone'],
            'fax'   => $request['fax'],
            'contact_person'  => $request['contact_person'],
            'contact_phone'   => $request['contact_phone'],
            'mobile_phone' => $request['mobile_phone'],
            'contact_email'   => $request['contact_email'],
            'currencyid'  => $request['currencyid'],
            'tax_type'  => $request['tax_type'],
            'payment_termid'  => $request['payment_termid'],
            'user_id'  => $request['user_id'],
            'note'       => $request['note'] ?? null,
            'is_valid'    => $request['is_valid']
        ]);

        // 回應 JSON
        if (!$supplier) {
            return response()->json([
                'status' => false,
                'message' => '供應商資料建失敗',
                'output'    => null
            ], status: 404);
        }else {
            // 回應 JSON
            return response()->json([
                'status' => true,
                'message' => 'success',
                'output'    => $supplier
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
            Log::error('建立單據資料錯誤：' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        }           


    }
    /**
     * @OA\GET(
     *     path="/api/Supplier/{supplierNo}",
     *     summary="查詢特定供應商資料",
     *     description="查詢特定供應商資料",
     *     operationId="getSupplier",
     *     tags={"Base_Supplier"},
     *     @OA\Parameter(
     *         name="supplierNo",
     *         in="path",
     *         required=true,
     *         description="供應商代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="supplier_no", type="string", example="S003"),
     *             @OA\Property(property="supplier_shortnm", type="string", example="測試供應商1"),
     *             @OA\Property(property="supplier_fullnm", type="string", example="測試供應商1"),
     *             @OA\Property(property="zipcode1", type="string", example="12345"),
     *             @OA\Property(property="address1", type="string", example="台北市信義區"),
     *             @OA\Property(property="zipcode2", type="string", example="54321"),
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
     *             @OA\Property(property="currencyid", type="string", example="TWD"),
     *             @OA\Property(property="tax_type", type="string", example="T001"),
     *             @OA\Property(property="payment_termid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="user_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到供應商資料"
     *     )
     * )
     */
    // 🔍 查詢供應商
    public function show($supplierNo)
    {
        try{
            $Supplier = Supplier::where('supplier_no', $supplierNo)->first();
        
            if (!$Supplier) {
                return response()->json([
                    'status' => false,
                    'message' => '供應商未找到',
                    'output'    => null
                ], 404);
            }
    
            return response()->json([                
                'status' => true,
                'message' => 'success',
                'output'    => $Supplier
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
     *     path="/api/Supplier2/{Keyword}",
     *     summary="查詢關鍵字",
     *     description="查詢關鍵字",
     *     operationId="getSupplier2",
     *     tags={"Base_Supplier"},
     *     @OA\Parameter(
     *         name="Keyword",
     *         in="path",
     *         required=true,
     *         description="關鍵字查詢(統一編號、廠商名稱、地址)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="supplier_no", type="string", example="S003"),
     *             @OA\Property(property="supplier_shortnm", type="string", example="測試供應商1"),
     *             @OA\Property(property="supplier_fullnm", type="string", example="測試供應商1"),
     *             @OA\Property(property="zipcode1", type="string", example="12345"),
     *             @OA\Property(property="address1", type="string", example="台北市信義區"),
     *             @OA\Property(property="zipcode2", type="string", example="54321"),
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
     *             @OA\Property(property="currencyid", type="string", example="TWD"),
     *             @OA\Property(property="tax_type", type="string", example="T001"),
     *             @OA\Property(property="payment_termid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="user_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到供應商資料"
     *     )
     * )
     */
    // 🔍 查詢供應商
    public function show2($Keyword)
    {
        try{
            // 使用關鍵字查詢品號
            $Supplier = Supplier::where('taxid', 'like', '%' . $Keyword . '%')
                ->orWhere('supplier_shortnm', 'like', '%' . $Keyword . '%')
                ->orWhere('supplier_fullnm', 'like', '%' . $Keyword . '%')
                ->orWhere('address1', 'like', '%' . $Keyword . '%')
                ->orWhere('address2', 'like', '%' . $Keyword . '%')
                ->get();       
            if (!$Supplier) {
                return response()->json([
                    'status' => false,
                    'message' => '供應商未找到',
                    'output'    => null
                ], 404);
            }
    
            return response()->json([                
                'status' => true,
                'message' => 'success',
                'output'    => $Supplier
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
     *     path="/api/Supplier/valid",
     *     summary="查詢所有有效供應商",
     *     description="查詢所有有效供應商",
     *     operationId="GetAllSupplier",
     *     tags={"Base_Supplier"},
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="supplier_no", type="string", example="S003"),
     *             @OA\Property(property="supplier_shortnm", type="string", example="測試供應商1"),
     *             @OA\Property(property="supplier_fullnm", type="string", example="測試供應商1"),
     *             @OA\Property(property="zipcode1", type="string", example="12345"),
     *             @OA\Property(property="address1", type="string", example="台北市信義區"),
     *             @OA\Property(property="zipcode2", type="string", example="54321"),
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
     *             @OA\Property(property="currencyid", type="string", example="TWD"),
     *             @OA\Property(property="tax_type", type="string", example="T001"),
     *             @OA\Property(property="payment_termid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="user_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到有效供應商"
     *     )
     * )
     */
    // 🔍 查詢所有有效供應商
    public function getValidsuppliers()
    {
        try {
            $Supplier = Supplier::getValidsuppliers();
            if ($Supplier->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => '未找到有效供應商',
                    'output'    => null
                ], 404);
            }
            return response()->json([                
                'status' => true,
                'message' => 'success',
                'output'    => $Supplier
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => '資料查詢錯誤',
                'output' => null
            ], 500);
        }
    }
    /**
     * @OA\patch(
     *     path="/api/Supplier/{supplierNo}/disable",
     *     summary="刪除特定供應商",
     *     description="刪除特定供應商",
     *     operationId="DeleteSupplier",
     *     tags={"Base_Supplier"},
     *     @OA\Parameter(
     *         name="supplier_no",
     *         in="path",
     *         required=true,
     *         description="供應商代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="supplier_no", type="string", example="S003"),
     *             @OA\Property(property="supplier_shortnm", type="string", example="測試供應商1"),
     *             @OA\Property(property="supplier_fullnm", type="string", example="測試供應商1"),
     *             @OA\Property(property="zipcode1", type="string", example="12345"),
     *             @OA\Property(property="address1", type="string", example="台北市信義區"),
     *             @OA\Property(property="zipcode2", type="string", example="54321"),
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
     *             @OA\Property(property="currencyid", type="string", example="TWD"),
     *             @OA\Property(property="tax_type", type="string", example="T001"),
     *             @OA\Property(property="payment_termid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="user_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )   
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到供應商"
     *     )
     * )
     */
    // 🔍 刪除特定供應商
    public function disable($supplierNo)
    {
        $Supplier = Supplier::findBysupplierNo($supplierNo);
        
        if (!$Supplier) {
            return response()->json([
                'status' => false,
                'message' => '供應商未找到',
                'output'    => null
            ], 404);
        }

        $Supplier->is_valid = 0;
        $Supplier->update_user = 'admin';
        $Supplier->update_time = now();
        $Supplier->save();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'output'    => $Supplier
        ], 200);
    }
    /**
     * @OA\get(
     *     path="/api/Suppliers/showConst",
     *     summary="列出所有供應商需要的常用(下拉、彈窗)",
     *     description="列出所有供應商需要的常用(下拉、彈窗)",
     *     operationId="Show_Supplier_ALL_Const",
     *     tags={"Base_Supplier"},
     *     @OA\Response(
     *         response=200,
     *         description="成功"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="供應商需要的常用未找到"
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
        // 付款條件(當月、次月的常數資料)
        $SysCode4 = PaymentTerm::where('is_valid', '1')->get();
        // 查詢 '所有有效人員' 的資料
        $SysCode3 = SysUser::with('depts')->where('is_valid', '1')->get();
        // 付款條件(當月、次月的常數資料)
        $SysCode4 = PaymentTerm::where('is_valid', '1')->get();
        
        try {
            // 檢查是否有結果
            if ($SysCode->isEmpty() ) {
                return response()->json([
                    'status' => false,
                    'message' => '常用資料未找到',
                    'currencyOption' => null,
                    'taxtypeOption' => null,
                    'paymenttermOption' => null,
                    'sysuserOption' => null,
                    'paymentterm2Option' => null
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
