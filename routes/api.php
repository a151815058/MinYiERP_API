<?php

use App\Http\Controllers\PaymentTermController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeptController;
use App\Http\Controllers\SysuserController;
use App\Http\Controllers\DeptSysUserController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\BillInfoController;
use App\Http\Controllers\SupplierController;

//部門相關  API
Route::post('/createdept', [DeptController::class, 'store']); // 新增部門資訊
Route::get('/dept/{deptNo}', [DeptController::class, 'show']);  // 透過 DeptNo 查詢
Route::get('/depts/valid', [DeptController::class, 'getValidDepts']);  // 查詢所有有效部門


//人員相關  API
Route::post('/createuser', [SysuserController::class, 'store']);// 新增人員資訊
Route::get('/user/{UsrNo}', [SysuserController::class, 'show']);  // 透過 UsrNo 查詢
Route::get('/users/valid', [SysuserController::class, 'getValidusers']);  // 查詢所有有效人員

//幣別相關  API
Route::post('/createCurrency', [CurrencyController::class, 'store']);// 新增貨幣資訊
Route::get('/Currency/{CurrencyNo}', [CurrencyController::class, 'show']);  // 透過 CurrencyNo 查詢
Route::get('/Currencys/valid', [CurrencyController::class, 'getValidCurrencys']);  // 查詢所有有效幣別
Route::get('/exchange-rate/{currency?}', [CurrencyController::class, 'getExchangeRate']); // 讀取匯率

//付款條件相關  API
Route::post('/createPaymentTerm', [PaymentTermController::class, 'store']);// 新增付款條件
Route::get('/PaymentTerm/{deptNo}', [PaymentTermController::class, 'show']);  // 透過 TermsNo 查詢
Route::get('/PaymentTerms/valid', [PaymentTermController::class, 'getValidTerms']);  // 查詢所有有效付款條件

//單據資料相關  API
Route::post('/createBillInfo', [BillInfoController::class, 'store']);// 新增單據資料
Route::get('/BillInfo/{BilltNo}', [BillInfoController::class, 'show']);  // 透過 TermsNo 查詢
Route::get('/BillInfos/valid', [BillInfoController::class, 'getValidBillNos']);  // 查詢所有有效付款條件

//供應商資料相關  API
Route::post('/createsupplier', [SupplierController::class, 'store']);// 新增供應商資料
Route::get('/supplier/{supplierNo}', [SupplierController::class, 'show']);  // 透過 supplierNo 查詢
Route::get('/supplier/valid', [SupplierController::class, 'getValidsuppliers']);  // 查詢所有有效供應商


// 新增人員部門關聯
Route::post('/assign-userdept', [DeptSysUserController::class, 'store']);
// 讀取部門成員
Route::get('/dept-users/{deptId}', [DeptSysUserController::class, 'getUsersByDept']); 
// 讀取使用者部門
Route::get('/user-depts/{userId}', [DeptSysUserController::class, 'getDeptsByUser']); 


Route::post('/', function () {
    return response()->json(['message' => 'Hello World!'], 200);
});