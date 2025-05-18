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
use App\Http\Controllers\InvoiceInfoController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\OrderController;

//部門相關  API
Route::post('/createdept', [DeptController::class, 'store']); // 新增部門資訊
Route::get('/dept/{deptNo}', [DeptController::class, 'showno']);  // 透過 DeptNo 查詢
Route::get('/dept2/{deptNM}', [DeptController::class, 'shownm']);  // 透過 DeptNM 查詢
Route::get('/depts/valid', [DeptController::class, 'getvaliddepts']);  // 查詢所有有效部門
Route::patch('/dept/{deptNo}/disable', [DeptController::class, 'disable']); // 軟刪除部門


//人員相關  API
Route::post('/createuser', [SysuserController::class, 'store']);// 新增人員資訊
Route::get('/user/{UsrNo}', [SysuserController::class, 'showno']);  // 透過 UsrNo 查詢
Route::get('/user2/{UsrNM}', [SysuserController::class, 'shownm']);  // 透過 UsrNo 查詢
Route::get('/user3/{dept_id}', [SysuserController::class, 'showdeptuser']);  // 透過 UsrNo 查詢
Route::get('/users/valid', [SysuserController::class, 'getvalidusers']);  // 查詢所有有效人員
Route::patch('/user/{UsrNo}/disable', [SysuserController::class, 'disable']); // 軟刪除人員
Route::get('/users/showconst', [SysuserController::class, 'showconst']);  // 列出所有貨幣需要的常用(下拉、彈窗)

// 新增人員部門關聯
Route::post('/assign-userdept', [DeptSysUserController::class, 'store']);
// 讀取部門成員
Route::get('/dept-users/{deptId}', [DeptSysUserController::class, 'getusersbydept']); 
// 讀取使用者部門
Route::get('/user-depts/{userId}', [DeptSysUserController::class, 'getDeptsByUser']); 


//幣別相關  API
Route::post('/createCurrency', [CurrencyController::class, 'store']);// 新增貨幣資訊
Route::get('/currency/{CurrencyNo}', [CurrencyController::class, 'show']);  // 透過 CurrencyNo 查詢
Route::get('/currency2/{CurrencyNM}', [CurrencyController::class, 'shownm']);  // 透過 CurrencyNM 查詢
Route::get('/currencys/valid', [CurrencyController::class, 'getvalidcurrencys']);  // 查詢所有有效幣別
Route::get('/exchange-rate/{currency}', [CurrencyController::class, 'getexchangerate']); // 讀取匯率
Route::patch('/currencys/{CurrencyNo}/disable', [CurrencyController::class, 'disable']); // 軟刪除貨幣資訊
Route::get('/currencys/showconst', [CurrencyController::class, 'showconst']);  // 列出所有貨幣需要的常用(下拉、彈窗)


//付款條件相關  API
Route::post('/createpaymentterm', [PaymentTermController::class, 'store']);// 新增付款條件
Route::get('/paymentterm/{TermNo}', [PaymentTermController::class, 'show']);  // 透過 TermsNo 查詢
Route::get('/paymentterm2/{TermNM}', [PaymentTermController::class, 'shownm']);  // 透過 TermsNM 查詢
Route::get('/paymentterms/valid', [PaymentTermController::class, 'getvalidterms']);  // 查詢所有有效付款條件
Route::patch('/paymentterm/{TermNo}/disable', [PaymentTermController::class, 'disable']); // 軟刪除付款條件資訊
Route::get('/paymentterms/showconst', [PaymentTermController::class, 'showconst']);  // 列出所有貨幣需要的常用(下拉、彈窗)


//單據資料相關  API
Route::post('/createbillinfo', [BillInfoController::class, 'store']);// 新增單據資料
Route::get('/billinfo/{billno}', [BillInfoController::class, 'show']);  // 透過 BillNo 查詢
Route::get('/billinfo2/{billnm}', [BillInfoController::class, 'shownm']);  // 透過 BillNM 查詢
Route::get('/billinfo1/valid', [BillInfoController::class, 'getvalidbillnos']);  // 查詢所有有效付款條件
Route::patch('/billinfo/{BillNo}/disable', [BillInfoController::class, 'disable']); // 軟刪除單據資訊
Route::get('/billinfo3/showconst', [BillInfoController::class, 'showconst']);  // 列出所有單據需要的常用(下拉、彈窗)


//供應商資料相關  API
Route::post('/createsupplier', [SupplierController::class, 'store']);// 新增供應商資料
Route::get('/supplier/{supplierNo}', [SupplierController::class, 'show']);  // 透過 supplierNo 查詢
Route::get('/supplier2/{Keyword}', [SupplierController::class, 'show2']);  // 透過 Keyword 查詢
Route::get('/supplier3/valid', [SupplierController::class, 'getvalidsuppliers']);// 查詢所有有效供應商
Route::patch('/supplier/{supplierNo}/disable', [SupplierController::class, 'disable']); // 軟刪除供應商資訊
Route::get('/supplier4/showconst', [SupplierController::class, 'showconst']);  // 列出所有供應商需要的常用(下拉、彈窗)

//客戶資料相關  API
Route::post('/createclient', [ClientController::class, 'store']);// 新增客戶資料
Route::get('/cient1/{clientNo}', [ClientController::class, 'show']);  // 透過 clientNo 查詢
Route::get('/client2/{Keyword}', [ClientController::class, 'show2']);  // 透過 Keyword 查詢
Route::get('/clients/valid', [ClientController::class, 'getValidclients']);// 查詢所有有效客戶
Route::patch('/client/{clientNo}/disable', [ClientController::class, 'disable']); // 軟刪除客戶
Route::get('/clients/showconst', [ClientController::class, 'showconst']);  // 列出所有單據需要的常用(下拉、彈窗)


//品號資料相關  API
Route::post('/createproduct', [ProductController::class, 'store']);// 新增品號資料
Route::get('/product/{ProductNO}', [ProductController::class, 'show']);  // 透過 ProductNO 查詢
Route::get('/product2/{keyword}', [ProductController::class, 'shownm']);  // 透過 ProductNO 查詢
Route::get('/product3/valid', [ProductController::class, 'getvalidproduct']);  // 查詢所有有效品號
Route::patch('/product/{ProductNO}/disable', [ProductController::class, 'disable']); // 軟刪除品號資訊
Route::get('/product1/showconst', [ClientController::class, 'showconst']);  // 列出所有單據需要的常用(下拉、彈窗)

//庫別資料相關  API
Route::post('/createinventory', [InventoryController::class, 'store']);// 新增庫別資料
Route::get('/inventory/{InventoryNO}', action: [InventoryController::class, 'showno']);  // 透過 InventoryNO 查詢
Route::get('/inventory2/{InventoryNM}', action: [InventoryController::class, 'shownm']);  // 透過 InventoryNO 查詢
Route::get('/inventorys/valid', [InventoryController::class, 'getvaildinventory']);  // 查詢所有有效庫別
Route::patch('/inventory/{InventoryNO}/disable', [InventoryController::class, 'disable']); // 軟刪除庫別資訊


//發票資料相關  API
Route::post('/createinvoiceinfo', [InvoiceInfoController::class, 'store']);// 新增發票資料
Route::get('/invoiceInfo2/{period}', action: [InvoiceInfoController::class, 'show']);  // 透過期別查詢
Route::get('/invoiceInfo1/valid', [InvoiceInfoController::class, 'getvaildinvoiceinfo']);  // 查詢所有有效發票
Route::patch('/invoiceinfo/{uuid}/disable', [InvoiceInfoController::class, 'disable']); // 軟刪除發票資訊
Route::get('/invoiceInfo/showconst', [InvoiceInfoController::class, 'showconst']);  // 列出所有發票需要的常用(下拉、彈窗)

//會計科目資料相關  API
Route::post('/createaccount', [AccountController::class, 'store']);// 新增會計科目資料
Route::get('/account1/{AccNo}', action: [AccountController::class, 'showno']);  // 透過會計科目代碼查詢
Route::get('/account/valid', [AccountController::class, 'getvaildaccount']);  // 查詢所有有效會計科目
Route::patch('/account3/{AccNo}/disable', [AccountController::class, 'disable']); // 軟刪除會計科目
Route::get('/account2/showconst', [AccountController::class, 'showconst']);  // 列出所有會計科目需要的常用(下拉、彈窗)


//訂單相關  API
Route::post('/createorder', [OrderController::class, 'store']);// 新增訂單相關
Route::get('/orderInfo/{order_no}', action: [OrderController::class, 'showno']);  // 透過訂單單號查詢
Route::get('/orderInfo1/valid', [OrderController::class, 'getvaildorderinfo']);  // 查詢所有有效訂單資訊


Route::post('/', function () {
    return response()->json(['message' => 'Hello World!'], 200);
});