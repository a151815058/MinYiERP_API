<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Currency;
use App\Models\PaymentTerm;
use App\Models\SysCode;
use App\Models\SysUser;
use App\Models\MMtown;
use App\Models\MMcity;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
require_once base_path('app/Models/connect.php'); 
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ValidationHelper;
use Carbon\Carbon;

class SupplierController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/createsupplier",
     *     summary="新增供應商資料",
     *     description="新增供應商資料",
     *     operationId="createsupplier",
     *     tags={"base_supplier"},
    *   @OA\Parameter(name="supplier_no", in="query", required=true, description="供應商編號", @OA\Schema(type="string")),
    *   @OA\Parameter(name="supplier_shortnm", in="query", required=true, description="供應商簡稱", @OA\Schema(type="string")),
    *   @OA\Parameter(name="supplier_fullnm", in="query", required=true, description="供應商全名", @OA\Schema(type="string")),
    *   @OA\Parameter(name="supplier_type", in="query", required=true, description="供應商類型 (公司、個體戶、外商等)", @OA\Schema(type="string")),
    *   @OA\Parameter(name="Classification", in="query", required=true, description="供應商分類(原物料、零件、服務、代理商)", @OA\Schema(type="string")),
    *   @OA\Parameter(name="responsible_person", in="query", required=false, description="負責人", @OA\Schema(type="string")),
    *   @OA\Parameter(name="contact_person", in="query", required=false, description="聯絡人", @OA\Schema(type="string")),
    *   @OA\Parameter(name="zipcode1", in="query", required=false, description="郵遞區號 1", @OA\Schema(type="string")),
    *   @OA\Parameter(name="city_id", in="query", required=false, description="縣市", @OA\Schema(type="string")),
    *   @OA\Parameter(name="town_id", in="query", required=false, description="區域", @OA\Schema(type="string")),
    *   @OA\Parameter(name="address1", in="query", required=false, description="公司地址 1", @OA\Schema(type="string")),
    *   @OA\Parameter(name="zipcode2", in="query", required=true, description="郵遞區號 2", @OA\Schema(type="string")),
    *   @OA\Parameter(name="city_id2", in="query", required=true, description="縣市 2", @OA\Schema(type="string")),
    *   @OA\Parameter(name="town_id2", in="query", required=true, description="區域 2", @OA\Schema(type="string")),
    *   @OA\Parameter(name="address2", in="query", required=true, description="公司地址 2", @OA\Schema(type="string")),
    *   @OA\Parameter(name="currencyid", in="query", required=false, description="幣別", @OA\Schema(type="string")),
    *   @OA\Parameter(name="payment_termid", in="query", required=false, description="付款條件", @OA\Schema(type="string")),
    *   @OA\Parameter(name="phone", in="query", required=false, description="公司電話", @OA\Schema(type="string")),
    *   @OA\Parameter(name="phone2", in="query", required=false, description="聯絡電話2", @OA\Schema(type="string")),
    *   @OA\Parameter(name="fax", in="query", required=false, description="公司傳真", @OA\Schema(type="string")),
    *   @OA\Parameter(name="mobile_phone", in="query", required=false, description="行動電話", @OA\Schema(type="string")),
    *   @OA\Parameter(name="contact_email", in="query", required=false, description="聯絡人信箱", @OA\Schema(type="string")),
    *   @OA\Parameter(name="user_id", in="query", required=false, description="業務人員", @OA\Schema(type="string")),
    *   @OA\Parameter(name="account_category", in="query", required=false, description="科目別", @OA\Schema(type="string")),
    *   @OA\Parameter(name="taxid", in="query", required=true, description="統一編號", @OA\Schema(type="string")),
    *   @OA\Parameter(name="established_date", in="query", required=false, description="成立時間", @OA\Schema(type="string", format="date-time")),
    *   @OA\Parameter(name="is_valid", in="query", required=false, description="是否有效 0:失效 1:有效", @OA\Schema(type="string")),
    *   @OA\Parameter(name="create_user", in="query", required=false, description="建立人員", @OA\Schema(type="string")),
    *   @OA\Parameter(name="create_time", in="query", required=false, description="建立時間", @OA\Schema(type="string", format="date-time")),
    *   @OA\Parameter(name="update_user", in="query", required=false, description="異動人員", @OA\Schema(type="string")),
    *   @OA\Parameter(name="update_time", in="query", required=false, description="異動時間", @OA\Schema(type="string", format="date-time")),
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
     *             @OA\Property(property="city_id", type="string", example="台北市"),
     *             @OA\Property(property="town_id", type="string", example="信義區"),
     *             @OA\Property(property="address1", type="string", example="台北市信義區"),
     *             @OA\Property(property="zipcode2", type="string", example="54321"),
     *             @OA\Property(property="city_id2", type="string", example="台北市"),
     *             @OA\Property(property="town_id2", type="string", example="大安區"),
     *             @OA\Property(property="address2", type="string", example="台北市大安區"),
     *             @OA\Property(property="taxid", type="string", example="12345678"),
     *             @OA\Property(property="responsible_person", type="string", example="王小明"),
     *             @OA\Property(property="established_date", type="string", example="2025-03-31"),
     *             @OA\Property(property="phone", type="string", example="02-12345678"),
     *             @OA\Property(property="phone2", type="string", example="02-23456789"),
     *             @OA\Property(property="fax", type="string", example="02-87654321"),
     *             @OA\Property(property="contact_person", type="string", example="李小華"),
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
        $errors1 = [];
        try{

            // 客戶代碼為必填
            if (!$request->filled('supplier_no')) {
                $errors1['supplier_no_err'] = '供應商編號為必填';
            }else {
                // 判斷客戶代碼不能存在空白、""、''、"、'
                if (!ValidationHelper::isValidText($request->input('supplier_no'))) {
                    $errors1['supplier_no_err'] = '供應商編號不得為空字串或*';
                }
                // 檢查供應商編號是否已存在
                $existingClient = Supplier::where('supplier_no', $request->input('supplier_no'))->first();
                if ($existingClient) {
                    $errors1['supplier_no_err'] = '供應商編號已存在';
                }
            }

            // 供應商名稱為必填
            if (!$request->filled('supplier_fullnm')) {
                $errors1['supplier_fullnm_err'] = '供應商全名為必填';
            }
            //判斷供應商名稱不能存在空白、""、''、"、'
            if (!ValidationHelper::isValidText($request->input('supplier_fullnm'))) {
                $errors1['supplier_fullnm_err'] = '供應商名稱不得為空字串或*';
            }

            //供應商全名為必填
            if (!$request->filled('supplier_shortnm')) {
                $errors1['supplier_shortnm_err'] = '供應商簡稱為必填';
            }

            //判斷供應商簡稱不能存在空白、""、''、"、'
            if (!ValidationHelper::isValidText($request->input('supplier_shortnm'))) {
                $errors1['supplier_shortnm_err'] = '供應商簡稱不得為空字串或*';
            }

            //供應商型態為必填
            if (!$request->filled('supplier_type')) {
                $errors1['supplier_type_err'] = '供應商型態為必填';
            }
            //供應商型態須為參數檔資料
            if (!$request->filled('supplier_type') && !SysCode::where('param_sn', '08')->where('uuid', $request->input('supplier_type'))->exists()) {
                $errors1['supplier_type_err'] = '供應商型態不存在，請選擇正確的供應商型態';
            }
            //供應商分類為必填
            if (!$request->filled('Classification')) {
                $errors1['Classification_err'] = '供應商分類為必填';
            }
            //供應商分類須為參數檔資料
            if (!$request->filled('Classification') && !SysCode::where('param_sn', '09')->where('uuid', $request->input('Classification'))->exists()) {
                $errors1['Classification_err'] = '供應商分類不存在，請選擇正確的供應商分類';
            }
            //郵遞區號一不可為中文
            if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $request->input('zip_code1'))) {
                $errors1['zip_code1_err'] = '郵遞區號一不可包含中文';
            }
 
            //郵遞區號二為必填
            if (!$request->filled('zipcode2')) {
                $errors1['zipcode2_err'] = '郵遞區號二為必填';
            }

            //判斷郵遞區號二不能存在空白、""、''、"、'
            if (ValidationHelper::isValidText($request->input('zipcode2'))) {
                $errors1['zipcode2_err'] = '郵遞區號二不得為空字串或*';
            }

            //郵遞區號二不可為中文
            if (!$request->filled('zipcode2') && preg_match('/[\x{4e00}-\x{9fa5}]/u', $request->input('zipcode2'))) {
                $errors1['zipcode2_err'] = '郵遞區號二不可包含中文';
            }
            //公司地址二為必填
            if (!$request->filled('address2')) {
                $errors1['address2_err'] = '公司地址二為必填';
            }

            //判斷公司地址二不能存在空白、""、''、"、'
            if (!ValidationHelper::isValidText($request->input('address2'))) {
                $errors1['address2_err'] = '公司地址二不得為空字串或*';
            }   

            //幣別須存在
            if ($request->filled('currencyid') ) {
                if(!Currency::where('uuid', $request->input('currencyid'))->exists()){
                    $errors1['currencyid_err'] = '幣別不存在，請選擇正確的幣別';
                }
            }

            //付款條件須存在
            if ($request->filled('payment_termid')) {
                if(!PaymentTerm::where('uuid', $request->input('payment_termid'))->exists()){
                    $errors1['payment_termid_err'] = '付款條件不存在，請選擇正確的付款條件';
                }
            }
            //公司電話不可為中文
            if ($request->filled('phone') ) {
                if(!preg_match('/[\x{4e00}-\x{9fa5}]/u', $request->input('phone'))){
                    $errors1['phone_err'] = '公司電話不可包含中文';
                }
                //公司電話須符合格式
                if(!preg_match('/^0\d{1,2}-?\d{6,8}$/', $request->filled('phone'))){
                    $errors1['phone_err'] = '公司電話須符合格式';
                }
            }

            //公司傳真不可為中文
            if ($request->filled('fax')) {
                if(!preg_match('/[\x{4e00}-\x{9fa5}]/u', $request->input('fax'))){
                    $errors1['fax_err'] = '公司傳真不可包含中文';
                }
                //公司傳真須符合格式
                if(!preg_match('/^0\d{1,2}-?\d{6,8}$/', $request->filled('fax'))){
                    $errors1['fax_err'] = '公司傳真須符合格式';
                }
            }
            //連絡電話2不可為中文
            if ($request->filled('phone2') ) {
                if(!preg_match('/[\x{4e00}-\x{9fa5}]/u', $request->input('phone2'))){
                    $errors1['phone2_err'] = '連絡電話2不可包含中文';
                }
                //連絡電話2須符合格式
                if(!preg_match('/^0\d{1,2}-?\d{6,8}$/', $request->filled('phone2'))){
                    $errors1['phone2_err'] = '連絡電話2須符合格式';
                }
            }
            //行動電話不可為中文
            if ($request->filled('mobile_phone')) {
                if(!preg_match('/[\x{4e00}-\x{9fa5}]/u', $request->input('mobile_phone'))){
                    $errors1['mobile_phone_err'] = '行動電話不可包含中文';
                }
                //行動電話須符合格式
                if(!preg_match('/^09\d{2}-?\d{3}-?\d{3}$/', $request->filled('mobile_phone'))){
                    $errors1['mobile_phone_err'] = '行動電話須符合格式';
                }                  
            }

            //聯絡人信箱不可為中文
            if ($request->filled('contact_email') ) {
                if(!preg_match('/[\x{4e00}-\x{9fa5}]/u', $request->input('contact_email'))){
                    $errors1['contact_email_err'] = '聯絡人信箱不可包含中文';
                }
                //聯絡人信箱須符合格式
                if ($request->filled('contact_email')) {
                    $email = $request->input('contact_email');
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors1['contact_email_err'] = '聯絡人信箱須符合格式';
                    }
                }              
            }

            //業務人員須存在
            if ($request->filled('user_id')  ) {
                if(!SysUser::where('uuid', $request->input('user_id'))->exists()){
                    $errors1['user_id_err'] = '業務人員不存在，請選擇正確的業務人員';
                }
            }

            //科目別須存在
            if ($request->filled('account_category') ) {
                if(!Account::where('uuid', $request->input('account_category'))->where(  'is_valid','1')->exists()){
                    $errors1['account_category_err'] = '科目別不存在，請選擇正確的科目別';
                }
            }
            ///統一編號為必填
            if (!$request->filled('taxid')) {
                $errors1['taxid_err'] = '統一編號為必填';
            }else{
                // 檢查統一編號格式是否正確
                if (strlen($request->input('taxid')) != 8) {
                    $errors1['taxid_err'] = '統一編號格式錯誤，應為8位數字';
                }else{
                    // 權重驗證
                    $taxid = str_split($request->input('taxid'));
                    $weight = [1, 2, 1, 2, 1, 2, 4, 1];
                    $sum = 0;
                    for ($i = 0; $i < 8; $i++) {
                        $digit = (int)$taxid[$i];
                        $product = $digit * $weight[$i];
                        if ($product >= 10) {
                            $product = array_sum(str_split($product));
                        }
                        $sum += $product;
                    }
                    if ($sum ==0 ||$sum % 10 !== 0) {
                        $errors1['taxid_err'] = '統一編號驗證失敗';
                    }
                }
            }

            //established_date為必填且須為年月日
            if (!$request->filled('established_date')) {
                $errors1['established_date_err'] = '成立日期為必填';
            } else {
                $date = $request->input('established_date');
                $date = $date ? Carbon::parse($date)->format('Y-m-d') : null;
                if (!$date) {
                    $errors1['established_date_err'] = '成立日期格式錯誤，應為YYYY-MM-DD';
                }
            }

            //判斷是否有效不能存在空白、""、''、"、'
            if (!ValidationHelper::isValidText($request->input('is_valid'))) {
                $errors1['is_valid_err'] = ' 是否有效不得為空字串或*';
            } 

            // 如果有錯誤，回傳統一格式
            if (!empty($errors1)) {
                return response()->json([
                    'status' => false,
                    'message' => '缺少必填的欄位及欄位格式錯誤',
                    'errors' => $errors1
                ], 400);
            }
  
            // 建立供應商資料
            $Supplier = Supplier::create([
                'uuid' => Str::uuid(),
                'supplier_no' => $request->input('supplier_no'),
                'supplier_shortnm' => $request->input('supplier_shortnm'),
                'supplier_fullnm' => $request->input('supplier_fullnm'),
                'supplier_type' => $request->input('supplier_type'),
                'Classification' => $request->input('Classification'),
                'responsible_person' => $request->input('responsible_person'),
                'contact_person' => $request->input('contact_person'),
                'zipcode1' => $request->input('zipcode1') ?? null,
                'city_id' => $request->input('city_id') ?? null,
                'town_id' => $request->input('town_id') ?? null,
                'address1' => $request->input('address1') ?? null,
                'zipcode2' => $request->input('zipcode2'),
                'city_id2' => $request->input('city_id2'),
                'town_id2' => $request->input('town_id2'),
                'address2' => $request->input('address2'),
                'currencyid' => $request->input('currencyid'),
                'payment_termid' => $request->input('payment_termid'),
                'phone' => $request->input('phone'),
                'phone2' => $request->input('phone2'),
                'fax' => $request->input('fax'),
                'mobile_phone' => $request->input('mobile_phone'),
                'contact_email' => $request->input('contact_email'),
                'user_id' => $request->input('user_id'),
                'account_category' => $request->input('account_category'),
                'taxid' => $request->input('taxid'),
                'established_date' => $date,
                'is_valid' => (int)$request->filled('is_valid') ? 1 : 0,
                'create_user'=> $request->input('create_user', 'admin'),
                'create_time' => $request->input('create_time', now()),
                'update_user' => $request->input('update_user', 'admin'),
                'update_time' => $request->input('update_time', now())
            ]);

            // 回應 JSON
            if (!$Supplier) {
                return response()->json([
                    'status' => false,
                    'message' => '供應商資料建立失敗',
                    'output'    => null
                ], status: 404);
            }else {
                // 回應 JSON
                return response()->json([
                    'status' => true,
                    'message' => 'success',
                    'output'    => $Supplier
                ], 400);
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
            Log::error('建立供應商資料錯誤：' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        }   
    }
    /**
     * @OA\POST(
     *     path="/api/updatesupplier",
     *     summary="更新供應商資料",
     *     description="更新供應商資料",
     *     operationId="updatesupplier",
     *     tags={"base_supplier"},
    *   @OA\Parameter(name="supplier_no", in="query", required=true, description="供應商編號", @OA\Schema(type="string")),
    *   @OA\Parameter(name="supplier_shortnm", in="query", required=true, description="供應商簡稱", @OA\Schema(type="string")),
    *   @OA\Parameter(name="supplier_fullnm", in="query", required=true, description="供應商全名", @OA\Schema(type="string")),
    *   @OA\Parameter(name="supplier_type", in="query", required=true, description="供應商類型 (公司、個體戶、外商等)", @OA\Schema(type="string")),
    *   @OA\Parameter(name="Classification", in="query", required=true, description="供應商分類(原物料、零件、服務、代理商)", @OA\Schema(type="string")),
    *   @OA\Parameter(name="responsible_person", in="query", required=false, description="負責人", @OA\Schema(type="string")),
    *   @OA\Parameter(name="contact_person", in="query", required=false, description="聯絡人", @OA\Schema(type="string")),
    *   @OA\Parameter(name="zipcode1", in="query", required=false, description="郵遞區號 1", @OA\Schema(type="string")),
    *   @OA\Parameter(name="city_id", in="query", required=false, description="縣市", @OA\Schema(type="string")),
    *   @OA\Parameter(name="town_id", in="query", required=false, description="區域", @OA\Schema(type="string")),
    *   @OA\Parameter(name="address1", in="query", required=false, description="公司地址 1", @OA\Schema(type="string")),
    *   @OA\Parameter(name="zipcode2", in="query", required=true, description="郵遞區號 2", @OA\Schema(type="string")),
    *   @OA\Parameter(name="city_id2", in="query", required=true, description="縣市 2", @OA\Schema(type="string")),
    *   @OA\Parameter(name="town_id2", in="query", required=true, description="區域 2", @OA\Schema(type="string")),
    *   @OA\Parameter(name="address2", in="query", required=true, description="公司地址 2", @OA\Schema(type="string")),
    *   @OA\Parameter(name="currencyid", in="query", required=false, description="幣別", @OA\Schema(type="string")),
    *   @OA\Parameter(name="payment_termid", in="query", required=false, description="付款條件", @OA\Schema(type="string")),
    *   @OA\Parameter(name="phone", in="query", required=false, description="公司電話", @OA\Schema(type="string")),
    *   @OA\Parameter(name="phone2", in="query", required=false, description="聯絡電話2", @OA\Schema(type="string")),
    *   @OA\Parameter(name="fax", in="query", required=false, description="公司傳真", @OA\Schema(type="string")),
    *   @OA\Parameter(name="mobile_phone", in="query", required=false, description="行動電話", @OA\Schema(type="string")),
    *   @OA\Parameter(name="contact_email", in="query", required=false, description="聯絡人信箱", @OA\Schema(type="string")),
    *   @OA\Parameter(name="user_id", in="query", required=false, description="業務人員", @OA\Schema(type="string")),
    *   @OA\Parameter(name="account_category", in="query", required=false, description="科目別", @OA\Schema(type="string")),
    *   @OA\Parameter(name="taxid", in="query", required=true, description="統一編號", @OA\Schema(type="string")),
    *   @OA\Parameter(name="established_date", in="query", required=false, description="成立時間", @OA\Schema(type="string", format="date-time")),
    *   @OA\Parameter(name="is_valid", in="query", required=false, description="是否有效 0:失效 1:有效", @OA\Schema(type="string")),
    *   @OA\Parameter(name="create_user", in="query", required=false, description="建立人員", @OA\Schema(type="string")),
    *   @OA\Parameter(name="create_time", in="query", required=false, description="建立時間", @OA\Schema(type="string", format="date-time")),
    *   @OA\Parameter(name="update_user", in="query", required=false, description="異動人員", @OA\Schema(type="string")),
    *   @OA\Parameter(name="update_time", in="query", required=false, description="異動時間", @OA\Schema(type="string", format="date-time")),
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
     *             @OA\Property(property="city_id", type="string", example="台北市"),
     *             @OA\Property(property="town_id", type="string", example="信義區"),
     *             @OA\Property(property="address1", type="string", example="台北市信義區"),
     *             @OA\Property(property="zipcode2", type="string", example="54321"),
     *             @OA\Property(property="city_id2", type="string", example="台北市"),
     *             @OA\Property(property="town_id2", type="string", example="大安區"),
     *             @OA\Property(property="address2", type="string", example="台北市大安區"),
     *             @OA\Property(property="taxid", type="string", example="12345678"),
     *             @OA\Property(property="responsible_person", type="string", example="王小明"),
     *             @OA\Property(property="established_date", type="string", example="2025-03-31"),
     *             @OA\Property(property="phone", type="string", example="02-12345678"),
     *             @OA\Property(property="phone2", type="string", example="02-23456789"),
     *             @OA\Property(property="fax", type="string", example="02-87654321"),
     *             @OA\Property(property="contact_person", type="string", example="李小華"),
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
    //更新供應商資料
    public function update(Request $request){
        try {
        $errors1 = [];
        try{

            // 供應商名稱為必填
            if (!$request->filled('supplier_fullnm')) {
                $errors1['supplier_fullnm_err'] = '供應商全名為必填';
            }
            //判斷供應商名稱不能存在空白、""、''、"、'
            if (!ValidationHelper::isValidText($request->input('supplier_fullnm'))) {
                $errors1['supplier_fullnm_err'] = '供應商名稱不得為空字串或*';
            }

            //供應商全名為必填
            if (!$request->filled('supplier_shortnm')) {
                $errors1['supplier_shortnm_err'] = '供應商簡稱為必填';
            }

            //判斷供應商簡稱不能存在空白、""、''、"、'
            if (!ValidationHelper::isValidText($request->input('supplier_shortnm'))) {
                $errors1['supplier_shortnm_err'] = '供應商簡稱不得為空字串或*';
            }

            //供應商型態為必填
            if (!$request->filled('supplier_type')) {
                $errors1['supplier_type_err'] = '供應商型態為必填';
            }
            //供應商型態須為參數檔資料
            if (!$request->filled('supplier_type') && !SysCode::where('param_sn', '08')->where('uuid', $request->input('supplier_type'))->exists()) {
                $errors1['supplier_type_err'] = '供應商型態不存在，請選擇正確的供應商型態';
            }
            //供應商分類為必填
            if (!$request->filled('Classification')) {
                $errors1['Classification_err'] = '供應商分類為必填';
            }
            //供應商分類須為參數檔資料
            if (!$request->filled('Classification') && !SysCode::where('param_sn', '09')->where('uuid', $request->input('Classification'))->exists()) {
                $errors1['Classification_err'] = '供應商分類不存在，請選擇正確的供應商分類';
            }
            //郵遞區號一不可為中文
            if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $request->input('zip_code1'))) {
                $errors1['zip_code1_err'] = '郵遞區號一不可包含中文';
            }
 
            //郵遞區號二為必填
            if (!$request->filled('zipcode2')) {
                $errors1['zipcode2_err'] = '郵遞區號二為必填';
            }

            //判斷郵遞區號二不能存在空白、""、''、"、'
            if (!ValidationHelper::isValidText($request->input('zipcode2'))) {
                $errors1['zipcode2_err'] = '郵遞區號二不得為空字串或*';
            }    

            //郵遞區號二不可為中文
            if (!$request->filled('zipcode2') && preg_match('/[\x{4e00}-\x{9fa5}]/u', $request->input('zipcode2'))) {
                $errors1['zipcode2_err'] = '郵遞區號二不可包含中文';
            }
            //公司地址二為必填
            if (!$request->filled('address2')) {
                $errors1['address2_err'] = '公司地址二為必填';
            }

            //判斷公司地址二不能存在空白、""、''、"、'
            if (!ValidationHelper::isValidText($request->input('address2'))) {
                $errors1['address2_err'] = '公司地址二不得為空字串或*';
            }   
            //幣別須存在
            if ($request->filled('currencyid') ) {
                if(!Currency::where('uuid', $request->input('currencyid'))->exists()){
                    $errors1['currencyid_err'] = '幣別不存在，請選擇正確的幣別';
                }
            }

            //付款條件須存在
            if ($request->filled('payment_termid')) {
                if(!PaymentTerm::where('uuid', $request->input('payment_termid'))->exists()){
                    $errors1['payment_termid_err'] = '付款條件不存在，請選擇正確的付款條件';
                }
            }             
            //公司電話不可為中文
            if ($request->filled('phone') ) {
                if(!preg_match('/[\x{4e00}-\x{9fa5}]/u', $request->input('phone'))){
                    $errors1['phone_err'] = '公司電話不可包含中文';
                }
                //公司電話須符合格式
                if(!preg_match('/^0\d{1,2}-?\d{6,8}$/', $request->filled('phone'))){
                    $errors1['phone_err'] = '公司電話須符合格式';
                }
            }
            //連絡電話2不可為中文
            if ($request->filled('phone2') ) {
                if(!preg_match('/[\x{4e00}-\x{9fa5}]/u', $request->input('phone2'))){
                    $errors1['phone2_err'] = '連絡電話2不可包含中文';
                }
                //連絡電話2須符合格式
                if(!preg_match('/^0\d{1,2}-?\d{6,8}$/', $request->filled('phone2'))){
                    $errors1['phone2_err'] = '連絡電話2須符合格式';
                }
            }
            
            //公司傳真不可為中文
            if ($request->filled('fax')) {
                if(!preg_match('/[\x{4e00}-\x{9fa5}]/u', $request->input('fax'))){
                    $errors1['fax_err'] = '公司傳真不可包含中文';
                }
                //公司傳真須符合格式
                if(!preg_match('/^0\d{1,2}-?\d{6,8}$/', $request->filled('fax'))){
                    $errors1['fax_err'] = '公司傳真須符合格式';
                }
            }

            //行動電話不可為中文
            if ($request->filled('mobile_phone')) {
                if( !preg_match('/[\x{4e00}-\x{9fa5}]/u', $request->input('mobile_phone'))){
                    $errors1['mobile_phone_err'] = '行動電話不可包含中文';
                }
                //行動電話須符合格式
                if(!preg_match('/^09\d{2}-?\d{3}-?\d{3}$/', $request->filled('mobile_phone'))){
                    $errors1['mobile_phone_err'] = '行動電話須符合格式';
                }                  
            }

            //聯絡人信箱不可為中文
            if ($request->filled('contact_email') ) {
                if(!preg_match('/[\x{4e00}-\x{9fa5}]/u', $request->input('contact_email'))){
                    $errors1['contact_email_err'] = '聯絡人信箱不可包含中文';
                }
                //聯絡人信箱須符合格式
                if (!filter_var($request->filled('contact_email'), FILTER_VALIDATE_EMAIL)) {
                    $errors1['contact_email_err'] = '聯絡人信箱須符合格式';
                }                
            }

            //業務人員須存在
            if ($request->filled('user_id')  ) {
                if(!SysUser::where('uuid', $request->input('user_id'))->exists()){
                    $errors1['user_id_err'] = '業務人員不存在，請選擇正確的業務人員';
                }
            }

            //科目別須存在
            if ($request->filled('account_category') ) {
                if(!Account::where('uuid', $request->input('account_category'))->where(  'is_valid','1')->exists()){
                    $errors1['account_category_err'] = '科目別不存在，請選擇正確的科目別';
                }
            }
            ///統一編號為必填
            if (!$request->filled('taxid')) {
                $errors1['taxid_err'] = '統一編號為必填';
            }else{
                // 檢查統一編號格式是否正確
                if (strlen($request->input('taxid')) != 8) {
                    $errors1['taxid_err'] = '統一編號格式錯誤，應為8位數字';
                }else{
                    // 權重驗證
                    $taxid = str_split($request->input('taxid'));
                    $weight = [1, 2, 1, 2, 1, 2, 4, 1];
                    $sum = 0;
                    for ($i = 0; $i < 8; $i++) {
                        $digit = (int)$taxid[$i];
                        $product = $digit * $weight[$i];
                        if ($product >= 10) {
                            $product = array_sum(str_split($product));
                        }
                        $sum += $product;
                    }
                    if ($sum ==0 ||$sum % 10 !== 0) {
                        $errors1['taxid_err'] = '統一編號驗證失敗';
                    }
                }
            }

            //判斷是否有效不能存在空白、""、''、"、'
            if (!ValidationHelper::isValidText($request->input('is_valid'))) {
                $errors1['is_valid_err'] = ' 是否有效不得為空字串或*';
            } 

            //established_date為必填且須為年月日
            if (!$request->filled('established_date')) {
                $errors1['established_date_err'] = '成立日期為必填';
            } else {
                $date = $request->input('established_date');
                $date = $date ? Carbon::parse($date)->format('Y-m-d') : null;
                if (!$date) {
                    $errors1['established_date_err'] = '成立日期格式錯誤，應為YYYY-MM-DD';
                }
            }

            // 如果有錯誤，回傳統一格式
            if (!empty($errors1)) {
                return response()->json([
                    'status' => false,
                    'message' => '缺少必填的欄位及欄位格式錯誤',
                    'errors' => $errors1
                ], 400);
            }
  
            // 查詢供應商資料client_uuid
            $Supplier = Supplier::where('uuid', $request->input('uuid'))->first();
            if (!$Supplier) {
                return response()->json([
                    'status' => false,
                    'message' => '欄位資料錯誤',
                    'Supplier_uuid_err'    =>  '供應商資料未找到',
                ], 400);
            }
            // 更新供應商資料
            $Supplier->update([
                'supplier_shortnm' => $request->input('supplier_shortnm'),
                'supplier_fullnm' => $request->input('supplier_fullnm'),
                'supplier_type' => $request->input('supplier_type'),
                'Classification' => $request->input('Classification'),
                'responsible_person' => $request->input('responsible_person'),
                'contact_person' => $request->input('contact_person'),
                'zipcode1' => $request->input('zipcode1') ?? null,
                'city_id' => $request->input('city_id') ?? null,
                'town_id' => $request->input('town_id') ?? null,
                'address1' => $request->input('address1') ?? null,
                'zipcode2' => $request->input('zipcode2'),
                'city_id2' => $request->input('city_id2'),
                'town_id2' => $request->input('town_id2'),
                'address2' => $request->input('address2'),
                'currencyid' => $request->input('currencyid'),
                'payment_termid' => $request->input('payment_termid'),
                'phone' => $request->input('phone'),
                'phone2' => $request->input('phone2'),
                'fax' => $request->input('fax'),
                'mobile_phone' => $request->input('mobile_phone'),
                'contact_email' => $request->input('contact_email'),
                'user_id' => $request->input('user_id'),
                'account_category' => $request->input('account_category'),
                'taxid' => $request->input('taxid'),
                'tax_type' => $request->input('tax_type'),
                'established_date' => date($request->filled('established_date')),
                'is_valid' => (int)$request->filled('is_valid') ? 1 : 0,
                'update_user'=> $request->input('update_user', 'admin'),
                'update_time' => now(),
            ]);
            // 儲存更新
            $Supplier->save();

            // 回應 JSON
            if (!$Supplier) {
                return response()->json([
                    'status' => false,
                    'message' => '供應商資料建立失敗',
                    'output'    => null
                ], 404);
            }else {
                // 回應 JSON
                return response()->json([
                    'status' => true,
                    'message' => 'success',
                    'output'    => $Supplier
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
            Log::error('建立供應商資料錯誤：' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
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
            Log::error('更新供應商資料錯誤：' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        }
    }
    /**
     * @OA\GET(
     *     path="/api/supplier/{supplierno}",
     *     summary="查詢特定供應商資料",
     *     description="查詢特定供應商資料",
     *     operationId="getsupplier",
     *     tags={"base_supplier"},
     *     @OA\Parameter(
     *         name="supplierno",
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
            $Supplier = Supplier::where('supplier_no', $supplierNo)->where('is_valid','1')->first();
        
            if (!$Supplier) {
                return response()->json([
                    'status' => true,
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
     *     path="/api/supplier3/valid",
     *     summary="查詢所有有效供應商(含關鍵字查詢，供應商代碼、供應商簡稱、供應商全稱、統一編號、負責人、公司地址一)",
     *     description="查詢所有有效供應商(含關鍵字查詢，供應商代碼、供應商簡稱、供應商全稱、統一編號、負責人、公司地址一)",
     *     operationId="getallsupplier",
     *     tags={"base_supplier"},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         required=false,
     *         description="關鍵字查詢",
     *         @OA\Schema(type="string")
     *     ),
    * @OA\Response(
    *     response=200,
    *     description="成功取得分頁供應商清單",
    *     @OA\JsonContent(
    *         type="object",
    *         @OA\Property(property="atPage", type="integer", example=1),
    *         @OA\Property(property="total", type="integer", example=10),
    *         @OA\Property(property="totalPages", type="integer", example=1),
    *         @OA\Property(
    *             property="data",
    *             type="array",
    *             @OA\Items(
    *                 type="object",
	*             		@OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
	*             		@OA\Property(property="supplier_no", type="string", example="S003"),
	*             		@OA\Property(property="supplier_shortnm", type="string", example="測試供應商1"),
	*             		@OA\Property(property="supplier_fullnm", type="string", example="測試供應商1"),
	*             		@OA\Property(property="zipcode1", type="string", example="12345"),
	*             		@OA\Property(property="address1", type="string", example="台北市信義區"),
	*             		@OA\Property(property="zipcode2", type="string", example="54321"),
	*             		@OA\Property(property="address2", type="string", example="台北市大安區"),
	*             		@OA\Property(property="taxid", type="string", example="12345678"),
	*             		@OA\Property(property="responsible_person", type="string", example="王小明"),
	*             		@OA\Property(property="established_date", type="string", example="2025-03-31"),
	*             		@OA\Property(property="phone", type="string", example="02-12345678"),
	*             		@OA\Property(property="fax", type="string", example="02-87654321"),
	*             		@OA\Property(property="contact_person", type="string", example="李小華"),
	*             		@OA\Property(property="mobile_phone", type="string", example="0987654321"),
	*             		@OA\Property(property="contact_email", type="string", example="a151815058@gmail.com"),
	*             		@OA\Property(property="currencyid", type="string", example="TWD"),
	*             		@OA\Property(property="tax_type", type="string", example="T001"),
	*             		@OA\Property(property="payment_termid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
	*             		@OA\Property(property="user_id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
	*             		@OA\Property(property="note", type="string", example=""),
	*             		@OA\Property(property="is_valid", type="boolean", example=true),
	*             		@OA\Property(property="create_user", type="string", example="admin"),
	*             		@OA\Property(property="update_user", type="string", example="admin"),
	*             		@OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
	*             		@OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
    *             )
    *         )
    *     )
    * ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到有效供應商"
     *     )
     * )
     */
    // 🔍 查詢所有有效供應商
    public function getvalidsuppliers(Request $request)
    {
        try {
            $pdo = getPDOConnection();
            $keyword = $request->query('keyword'); // 可為 null
            $page = $request->query('page'); // 當前頁碼
            $pageSize = $request->query('pageSize'); // 一頁顯示幾筆數值
            $page = $page ? (int)$page : 1; // 預設為第 1 頁
            $pageSize = $pageSize ? (int)$pageSize : 30; // 預設每頁顯示 30 筆資料
            //查詢目前頁數的資料
            $offset = ($page - 1) * $pageSize;
            //LIMIT 30：每次最多回傳 30 筆資料
            //OFFSET 0：從第 0 筆開始取，也就是第一頁的第 1 筆
            //LIMIT 30 OFFSET 0  -- 取第 1~30 筆
            //LIMIT 30 OFFSET 30 -- 取第 31~60 筆
            //LIMIT 30 OFFSET 60 -- 取第 61~90 筆
            $sql_data = "select  *
                        from supplier
                        where supplier.is_valid = '1'  
                        and ( supplier.supplier_no LIKE ? 
                          OR supplier.supplier_shortnm LIKE ?
                          OR supplier.supplier_fullnm LIKE ?
                          OR supplier.taxid LIKE ?
                          OR supplier.responsible_person LIKE ?
                          OR supplier.address1 LIKE ?)
                        order by update_time,create_time asc
                        LIMIT ? OFFSET ?;";
            $likeKeyword = '%' . $keyword . '%';
            $Supplier = DB::select($sql_data, [$likeKeyword, $likeKeyword,$likeKeyword, $likeKeyword,$likeKeyword, $likeKeyword, $pageSize, $offset]);

            //取得總筆數與總頁數   
            $sql_count = "
                    SELECT COUNT(*) as total
                    from supplier
                        where supplier.is_valid = '1'  
                        and ( supplier.supplier_no LIKE ? 
                          OR supplier.supplier_shortnm LIKE ?
                          OR supplier.supplier_fullnm LIKE ?
                          OR supplier.taxid LIKE ?
                          OR supplier.responsible_person LIKE ?
                          OR supplier.address1 LIKE ?);
                ";
            $stmt = $pdo->prepare($sql_count);
            $stmt->execute([$likeKeyword, $likeKeyword,$likeKeyword, $likeKeyword,$likeKeyword, $likeKeyword]);
            $total = $stmt->fetchColumn();
            $totalPages = ceil($total / $pageSize); // 計算總頁數    

            if (!$Supplier) {
                return response()->json([
                    'status' => true,
                    'atPage' => $page,
                    'total' => $total,
                    'totalPages' => $totalPages,
                    'message' => '未找到有效供應商',
                    'output'    => $Supplier
                ], 404);
            }
            return response()->json([                
                'status' => true,
                'atPage' => $page,
                'total' => $total,
                'totalPages' => $totalPages,
                'message' => 'success',
                'output'    => $Supplier
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
            Log::error('建立資料錯誤：' . $e->getMessage());
        
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        }
    }
    /**
     * @OA\patch(
     *     path="/api/supplier/{supplierno}/disable",
     *     summary="刪除特定供應商",
     *     description="刪除特定供應商",
     *     operationId="deletesupplier",
     *     tags={"base_supplier"},
     *     @OA\Parameter(
     *         name="supplierno",
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
        try{
            $Supplier = Supplier::where('supplier_no', $supplierNo)->where('is_valid','1')->first();

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
     * @OA\get(
     *     path="/api/supplier4/showconst",
     *     summary="列出所有供應商需要的常用(下拉、彈窗)",
     *     description="列出所有供應商需要的常用(下拉、彈窗)",
     *     operationId="show_supplier_all_const",
     *     tags={"base_supplier"},
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
    public function showconst($constant='all'){
        // 查詢 '所有有效幣別資料' 的資料
        $SysCode = Currency::where('is_valid', '1')->get();
        // 查詢 '所有稅別資料' 的資料(不需發票資訊)
        //$SysCode1 = SysCode::where('param_sn', '02')->where('is_valid','1')->get();
        // 查詢 '所有有效付款條件' 的資料
        $SysCode2 = PaymentTerm::where('is_valid', '1')->get();
        // 查詢 '所有有效人員' 的資料
        $SysCode3 = SysUser::with('depts')->where('is_valid', '1')->get();
        // 發票寄送方式(不需發票資訊)
        //$SysCode4 = SysCode::where('param_sn', '04')->where('is_valid','1')->get();
        // 公司類型
        $SysCode5 = SysCode::where('param_sn', '08')->where('is_valid','1')->get();
        // 供應商分類
        $SysCode6 = SysCode::where('param_sn', '09')->where('is_valid','1')->get();
        // 科目別 
        $Account = Account::where('is_valid','1')->get();

        // 縣市資料
        $MMcity = MMcity::get();
        // 區域資料
        $MMtown = MMtown::get();
        
        try {
            // 檢查是否有結果
            if ($SysCode->isEmpty() && 
                $SysCode2->isEmpty() && 
                $SysCode3->isEmpty() &&
                $SysCode5->isEmpty() &&
                $SysCode6->isEmpty() &&
                $Account->isEmpty() &&
                $MMcity->isEmpty() &&
                $MMtown->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => '常用資料未找到',
                    'currencyOption' => [],
                    'paymenttermOption' => [],
                    'sysuserOption' => [],
                    'suppliertypeOption' => [],
                    'ClassificationOption' => [],
                    'accountOption' =>[],
                    'MMcityOption' => [],
                    'MMtownOption' => []
                ], 404);
            }
    
            // 返回查詢結果
            return response()->json([
                'status' => true,
                'message' => 'success',
                'currencyOption' => $SysCode,
                'paymenttermOption' => $SysCode2,
                'sysuserOption' => $SysCode3,
                'suppliertypeOption' => $SysCode5,
                'ClassificationOption' => $SysCode6,
                'accountOption' => $Account,
                'MMcityOption' => $MMcity,
                'MMtownOption' => $MMtown
            ], 400);
    
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
