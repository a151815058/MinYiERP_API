<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;
    /**
     * @OA\Get(
     *     path="/Supplier/{supplierNo}",
     *     summary="取得單一部門資訊",
     *     description="根據部門編號查詢部門資訊",
     *     operationId="getsupplierNo",
     *     tags={"supplier"},
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
class SupplierController extends Controller
{
    // 儲存供應商
    public function store(Request $request)
    {
        // 驗證請求
        // $validated = $request->validate([
        //     'supplierNo'         => 'required|string|max:255|unique:supplier,supplierNo',
        //     'supplierShortNM'    => 'required|string|max:255',
        //     'supplierFullNM'     => 'required|string|max:255',
        //     'ZipCode1'           => 'nullable|string|max:20',
        //     'Address1'           => 'nullable|string|max:255',
        //     'ZipCode2'           => 'required|string|max:20',
        //     'Address2'           => 'required|string|max:255',
        //     'TaxID'              => 'required|string|max:255', 
        //     'ResponsiblePerson'  => 'required|string|max:255',   
        //     'EstablishedDate'    => 'required|string|max:20',  
        //     'Phone'              => 'required|string|max:20',  
        //     'Fax'                => 'required|string|max:10',  
        //     'ContactPerson'      => 'required|string|max:255',  
        //     'ContactPhone'       => 'required|string|max:255',  
        //     'MobilePhone'        => 'required|string|max:255',  
        //     'ContactEmail'       => 'required|string|max:255',  
        //     'CurrencyID'         => 'required|string|max:255',  
        //     'TaxType'            => 'required|string|max:255',  
        //     'PaymentTermID'      => 'required|string|max:255',    
        //     'UserID'             => 'required|string|max:255',     
        //     'Note'               => 'nullable|string|max:255',
        //     'IsValid'            => 'required|boolean',
        //     'Createuser'         => 'required|string|max:255',
        //     'UpdateUser'         => 'required|string|max:255',
        // ]);
        
    
        // 建立供應商資料
        $supplier = Supplier::create([
            'supplierNo'     => $request['supplierNo'],
            'supplierShortNM'     => $request['supplierShortNM'],
            'supplierFullNM'   => $request['supplierFullNM'],
            'ZipCode1'   => $request['ZipCode1'],
            'Address1' => $request['Address1'],
            'ZipCode2'   => $request['ZipCode2'],
            'Address2' => $request['Address2'],
            'TaxID'   => $request['TaxID'],
            'ResponsiblePerson'  => $request['ResponsiblePerson'],
            'EstablishedDate'   => $request['EstablishedDate'],
            'Phone' => $request['Phone'],
            'Fax'   => $request['Fax'],
            'ContactPerson'  => $request['ContactPerson'],
            'ContactPhone'   => $request['ContactPhone'],
            'MobilePhone' => $request['MobilePhone'],
            'ContactEmail'   => $request['ContactEmail'],
            'CurrencyID'  => $request['CurrencyID'],
            'TaxType'  => $request['TaxType'],
            'PaymentTermID'  => $request['PaymentTermID'],
            'UserID'  => $request['UserID'],
            'Note'       => $request['Note'] ?? null,
            'IsValid'    => $request['IsValid'],
            'Createuser' => $request['Createuser'],
            'UpdateUser' => $request['UpdateUser'],
            'CreateTime' => now(),
            'UpdateTime' => now()
        ]);

        // 回應 JSON
        return response()->json([
            'message'  => '供應商資料建立成功',
            'supplier' => $supplier
        ], 201);

    }

    // 🔍 查詢供應商
    public function show($supplierNo)
    {
        $supplierNo = Supplier::findBysupplierNo($supplierNo);
        
        if (!$supplierNo) {
            return response()->json(['message' => '供應商未找到'], 404);
        }

        return response()->json($supplierNo);
    }

    // 🔍 查詢所有有效供應商
    public function getValidsuppliers()
    {
        if (!Supplier::getValidsuppliers()) {
            return response()->json(['message' => '供應商未找到123'], 404);
        }

        return response()->json(Supplier::getValidsuppliers());
    }
}
