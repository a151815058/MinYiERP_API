<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

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
     *         name="supplierNo",
     *         in="query",
     *         required=true,
     *         description="客戶編號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="supplierShortNM",
     *         in="query",
     *         required=true,
     *         description="客戶簡稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="supplierFullNM",
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
     *         required=true,
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
     *             @OA\Property(property="supplierNo", type="string", example="S003"),
     *             @OA\Property(property="supplierShortNM", type="string", example="測試供應商1"),
     *             @OA\Property(property="supplierFullNM", type="string", example="測試供應商1"),
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
     *         description="供應商建立失敗"
     *     )
     * )
     */
    // 儲存供應商
    public function store(Request $request)
    {
        // 驗證請求
         $validated = $request->validate([
             'supplierNo'         => 'required|string|max:255|unique:supplier,supplierNo',
             'supplierShortNM'    => 'required|string|max:255',
             'supplierFullNM'     => 'required|string|max:255',
             'ZipCode1'           => 'required|string|max:20',
             'Address1'           => 'required|string|max:255',
             'ZipCode2'           => 'nullable|string|max:20',
             'Address2'           => 'nullable|string|max:255',
             'TaxID'              => 'required|string|max:255', 
             'ResponsiblePerson'  => 'required|string|max:255',   
             'EstablishedDate'    => 'required|string|max:20',  
             'Phone'              => 'required|string|max:20',  
             'Fax'                => 'required|string|max:10',  
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
        
    
        // 建立供應商資料
        $supplier = Supplier::create([
            'supplierNo'     => $validated['supplierNo'],
            'supplierShortNM'     => $validated['supplierShortNM'],
            'supplierFullNM'   => $validated['supplierFullNM'],
            'ZipCode1'   => $validated['ZipCode1'],
            'Address1' => $validated['Address1'],
            'ZipCode2'   => $validated['ZipCode2']?? null,
            'Address2' => $validated['Address2']?? null,
            'TaxID'   => $validated['TaxID'],
            'ResponsiblePerson'  => $validated['ResponsiblePerson'],
            'EstablishedDate'   => $validated['EstablishedDate'],
            'Phone' => $validated['Phone'],
            'Fax'   => $validated['Fax'],
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
     *             @OA\Property(property="supplierNo", type="string", example="S003"),
     *             @OA\Property(property="supplierShortNM", type="string", example="測試供應商1"),
     *             @OA\Property(property="supplierFullNM", type="string", example="測試供應商1"),
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
     *         description="未找到供應商資料"
     *     )
     * )
     */
    // 🔍 查詢供應商
    public function show($supplierNo)
    {
        $supplierNo = Supplier::findBysupplierNo($supplierNo);
        
        if (!$supplierNo) {
            return response()->json([
                'status' => false,
                'message' => '供應商未找到',
                'output'    => null
            ], 404);
        }

        return response()->json([                
            'status' => true,
            'message' => 'success',
            'output'    => $supplierNo
        ],200);
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
     *             @OA\Property(property="supplierNo", type="string", example="S003"),
     *             @OA\Property(property="supplierShortNM", type="string", example="測試供應商1"),
     *             @OA\Property(property="supplierFullNM", type="string", example="測試供應商1"),
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
     *             @OA\Property(property="DeptNo", type="string", example="A02"),
     *             @OA\Property(property="DeptNM", type="string", example="財務處"),
     *             @OA\Property(property="Note", type="string", example="測試測試"),
     *             @OA\Property(property="IsValid", type="boolean", example=false),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
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

        $Supplier->IsValid = 0;
        $Supplier->UpdateUser = 'admin';
        $Supplier->UpdateTime = now();
        $Supplier->save();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'output'    => $Supplier
        ], 200);
    }
}
