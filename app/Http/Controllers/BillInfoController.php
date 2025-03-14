<?php

namespace App\Http\Controllers;

use App\Models\BillInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BillInfoController extends Controller
{
    // 儲存付款條件
    public function store(Request $request)
    {
        // 驗證請求
        //$validated = $request->validate([
        //    'BillNo'     => 'required|string|max:255|unique:billinfo,BillNo',
        //    'BillNM'     => 'required|string|max:255',
        //    'BillType'   => 'required|integer|max:10',
        //    'BillEncode' => 'required|string|max:10',
        //    'BillCalc'   => 'required|integer|max:10',
        //    'AutoReview' => 'required|integer|max:10',
        //    'GenOrder'   => 'required|string|max:10', 
        //    'OrderType'  => 'required|integer|max:10',           
        //    'Note'       => 'nullable|string|max:255',
        //    'IsValid'    => 'required|boolean',
        //    'Createuser' => 'required|string|max:255',
        //    'UpdateUser' => 'required|string|max:255',
        //]);

    
        // 建立單據資料
        $BillInfo = BillInfo::create([
            'BillNo'     => $request['BillNo'],
            'BillNM'     => $request['BillNM'],
            'BillType'   => $request['BillType'],
            'BillEncode' => $request['BillEncode'],
            'BillCalc'   => $request['BillCalc'],
            'AutoReview' => $request['AutoReview'],
            'GenOrder'   => $request['GenOrder'],
            'OrderType'  => $request['OrderType'],
            'Note'       => $request['Note'] ?? null,
            'IsValid'    => $request['IsValid'],
            'Createuser' => $request['Createuser'],
            'UpdateUser' => $request['UpdateUser'],
            'CreateTime' => now(),
            'UpdateTime' => now()
        ]);

    
        // 回應 JSON
        return response()->json([
            'message'  => '單據資料建立成功',
            'BillInfo' => $BillInfo
        ], 201);
    }
    // 🔍 查詢單一付款條件
    public function show($BillNo)
    {
        $BillNo = BillInfo::findByBillNo($BillNo);
        
        if (!$BillNo) {
            return response()->json(['message' => '付款條件未找到'], 404);
        }

        return response()->json($BillNo);
    }

    // 🔍 查詢所有有效部門
    public function getValidBillNos()
    {
        return response()->json(BillInfo::getValidBillNos());
    }
}
