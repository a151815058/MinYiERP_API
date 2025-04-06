<?php

use App\Http\Controllers\PaymentTermController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeptController;
use App\Http\Controllers\SysuserController;
use App\Http\Controllers\DeptSysUserController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\BillInfoController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ClientController;

//部門相關  API
Route::post('/createdept', [DeptController::class, 'store']); // 新增部門資訊
Route::get('/dept/{deptNo}', [DeptController::class, 'show']);  // 透過 DeptNo 查詢
Route::get('/depts/valid', [DeptController::class, 'getValidDepts']);  // 查詢所有有效部門
Route::patch('/dept/{deptNo}/disable', [DeptController::class, 'disable']); // 軟刪除部門


//人員相關  API
Route::post('/createuser', [SysuserController::class, 'store']);// 新增人員資訊
Route::get('/user/{UsrNo}', [SysuserController::class, 'show']);  // 透過 UsrNo 查詢
Route::get('/users/valid', [SysuserController::class, 'getValidusers']);  // 查詢所有有效人員
Route::patch('/user/{UsrNo}/disable', [SysuserController::class, 'disable']); // 軟刪除人員
Route::get('/users/showConst', [SysuserController::class, 'showConst']);  // 列出所有貨幣需要的常用(下拉、彈窗)

// 新增人員部門關聯
Route::post('/assign-userdept', [DeptSysUserController::class, 'store']);
// 讀取部門成員
Route::get('/dept-users/{deptId}', [DeptSysUserController::class, 'getUsersByDept']); 
// 讀取使用者部門
Route::get('/user-depts/{userId}', [DeptSysUserController::class, 'getDeptsByUser']); 


//幣別相關  API
Route::post('/createCurrency', [CurrencyController::class, 'store']);// 新增貨幣資訊
Route::get('/Currency/{CurrencyNo}', [CurrencyController::class, 'show']);  // 透過 CurrencyNo 查詢
Route::get('/Currencys/valid', [CurrencyController::class, 'getValidCurrencys']);  // 查詢所有有效幣別
Route::get('/exchange-rate/{currency}', [CurrencyController::class, 'getExchangeRate']); // 讀取匯率
Route::patch('/Currencys/{CurrencyNo}/disable', [CurrencyController::class, 'disable']); // 軟刪除貨幣資訊
Route::get('/Currencys/showConst', [CurrencyController::class, 'showConst']);  // 列出所有貨幣需要的常用(下拉、彈窗)


//付款條件相關  API
Route::post('/createPaymentTerm', [PaymentTermController::class, 'store']);// 新增付款條件
Route::get('/PaymentTerm/{TermNo}', [PaymentTermController::class, 'show']);  // 透過 TermsNo 查詢
Route::get('/PaymentTerms/valid', [PaymentTermController::class, 'getValidTerms']);  // 查詢所有有效付款條件
Route::patch('/PaymentTerm/{TermNo}/disable', [PaymentTermController::class, 'disable']); // 軟刪除付款條件資訊
Route::get('/PaymentTerms/showConst', [PaymentTermController::class, 'showConst']);  // 列出所有貨幣需要的常用(下拉、彈窗)


//單據資料相關  API
Route::post('/createBillInfo', [BillInfoController::class, 'store']);// 新增單據資料
Route::get('/BillInfo/{BillNo}', [BillInfoController::class, 'show']);  // 透過 BillNo 查詢
Route::get('/BillInfos/valid', [BillInfoController::class, 'getValidBillNos']);  // 查詢所有有效付款條件
Route::patch('/BillInfo/{BillNo}/disable', [BillInfoController::class, 'disable']); // 軟刪除單據資訊
Route::get('/BillInfos/showConst', [BillInfoController::class, 'showConst']);  // 列出所有單據需要的常用(下拉、彈窗)


//供應商資料相關  API
Route::post('/createsupplier', [SupplierController::class, 'store']);// 新增供應商資料
Route::get('/Supplier/{supplierNo}', [SupplierController::class, 'show']);  // 透過 supplierNo 查詢
Route::get('/Supplier/valid', [SupplierController::class, 'getValidsuppliers']);// 查詢所有有效供應商
Route::patch('/Supplier/{supplierNo}/disable', [SupplierController::class, 'disable']); // 軟刪除供應商資訊

//客戶資料相關  API
Route::post('/createclient', [ClientController::class, 'store']);// 新增客戶資料
Route::get('/Client/{clientNo}', [ClientController::class, 'show']);  // 透過 clientNo 查詢
Route::get('/Clients/valid', [ClientController::class, 'getValidClients']);// 查詢所有有效客戶
Route::patch('/Client/{clientNo}/disable', [ClientController::class, 'disable']); // 軟刪除客戶
Route::get('/Clients/showConst', [ClientController::class, 'showConst']);  // 列出所有單據需要的常用(下拉、彈窗)


//品號資料相關  API
Route::post('/createproduct', [ProductController::class, 'store']);// 新增品號資料
Route::get('/product/{ProductNO}', [ProductController::class, 'show']);  // 透過 ProductNO 查詢
Route::get('/products/valid', [ProductController::class, 'getValidProduct']);  // 查詢所有有效品號
Route::patch('/product/{ProductNO}/disable', [ProductController::class, 'disable']); // 軟刪除品號資訊

//庫別資料相關  API
Route::post('/createInventory', [InventoryController::class, 'store']);// 新增庫別資料
Route::get('/Inventory/{InventoryNO}', action: [InventoryController::class, 'show']);  // 透過 InventoryNO 查詢
Route::get('/Inventorys/Valid', [InventoryController::class, 'getVaildInventory']);  // 查詢所有有效庫別
Route::patch('/Inventory/{InventoryNO}/disable', [InventoryController::class, 'disable']); // 軟刪除庫別資訊



Route::post('/', function () {
    return response()->json(['message' => 'Hello World!'], 200);
});