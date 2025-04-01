<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
    /**
     * @OA\Get(
     *     path="/product/{ProductNO}",
     *     summary="取得單一部門資訊",
     *     description="根據部門編號查詢部門資訊",
     *     operationId="getProduct",
     *     tags={"ProductNO"},
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
class ProductController extends Controller
{
    // 儲存品號
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
        
    
        // 建立品號資料
        $Product = Product::create([
            'ProductNO'     => $request['ProductNO'],
            'ProductNM'     => $request['ProductNM'],
            'Specification'   => $request['Specification'],
            'Barcode'   => $request['Barcode'],
            'Price_1' => $request['Price_1'],
            'Price_2'   => $request['Price_2'],
            'Price_3' => $request['Price_3'],
            'Cost_1'   => $request['Cost_1'],
            'Cost_2'  => $request['Cost_2'],
            'Cost_3'   => $request['Cost_3'],
            'Batch_control' => $request['Batch_control'],
            'Valid_days'   => $request['Valid_days'],
            'Effective_date'  => $request['Effective_date'],
            'Stock_control'   => $request['Stock_control'],
            'Safety_stock' => $request['Safety_stock'],
            'Expiry_date'   => $request['Expiry_date'],
            'Description'  => $request['Description'],
            'IsValid'    => $request['IsValid'],
            'Createuser' => $request['Createuser'],
            'UpdateUser' => $request['UpdateUser'],
            'CreateTime' => now(),
            'UpdateTime' => now()
        ]);

        // 回應 JSON
        return response()->json([
            'message'  => '品號建立成功',
            'supplier' => $Product
        ], 201);

    }

    // 🔍 查詢單一品號
    public function show($ProductNO)
    {
        $ProductNO = Product::findByProductNO($ProductNO);
        
        if (!$ProductNO) {
            return response()->json(['message' => '品號未找到'], 404);
        }

        return response()->json($ProductNO);
    }

    // 🔍 查詢所有有效品號
    public function getValidProducts()
    {
        return response()->json(Product::where('IsVaild', '1')->get());
    }

}
