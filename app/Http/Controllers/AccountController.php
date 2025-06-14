<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\SysCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
require_once base_path('app/Models/connect.php'); 
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/createaccount",
     *     summary="新增會計科目",
     *     description="新增會計科目",
     *     operationId="createaccount",
     *     tags={"base_account"},
     *     @OA\Parameter(name="account_no",in="query",required=true,description="會計科目代碼", @OA\Schema(type="string")),
     *     @OA\Parameter( name="account_name",in="query",required=true, description="會計科目名稱", @OA\Schema(type="string")),
     *     @OA\Parameter(name="Puuid",in="query",required=false, description="父層UUID",@OA\Schema(type="string")),
     *     @OA\Parameter(name="tier",in="query",required=true,description="層級",@OA\Schema(type="string")),
     *     @OA\Parameter(name="dc",in="query", required=false,description="借貸方向",@OA\Schema(type="string")),
     *     @OA\Parameter(name="note", in="query", required=false,  description="備註", @OA\Schema(type="string")),
     *     @OA\Parameter(name="is_valid",in="query",required=true,description="是否有效", @OA\Schema(type="string", example=1)),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="account_no", type="string", example="1"),
     *             @OA\Property(property="account_name", type="string", example="資產"),
     *             @OA\Property(property="Puuid", type="string", example=""),
     *             @OA\Property(property="tier", type="string", example="1"),
     *             @OA\Property(property="dc", type="string", example="Assets"),
     *             @OA\Property(property="note", type="string", example="指因過去事項所產生之資源，該資源由商業控制，並預期帶來經濟效益之流入。"),    
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="會計科目失敗"
     *     )
     * )
     */
    // 儲存會計科目
    public function store(Request $request)
    {
        $errors1 = [];
        $errors1 = [];
        try {
            // 會計代碼為必填
            if (!$request->has('account_no')) {
                $errors1['account_no_err'] = '會計代碼為必填';
            }else {
                // 檢查會計代碼不為空字串
                if(empty($request['account_no']) || str_contains($request['account_no'] , '*') ){
                    $errors1['account_no_err'] = '會計代碼不得為空字串或*';
                }
                // 檢查會計代碼是否已存在
                $existingClient = Account::where('account_no', $request->input('account_no'))->first();
                if ($existingClient) {
                    $errors1['account_no_err'] = '會計代碼已存在';
                }
            }

            // 會計科目名稱必填
            if (empty($request['account_name'])) {
                $errors1['account_name_err'] = '會計科目為必填';
            }
            // 檢查會計科目不為空字串
            if(empty($request['account_name']) || str_contains($request['account_name'] , '*') ){
                $errors1['account_name_err'] = '會計科目不得為空字串或*';
            }
            //科目層級必填
            if (!$request->has('tier')) {
                $errors1['tier_err'] = '科目層級為必填';
            }
            //科目層級須包含在參數檔
            if (!$request->has('tier') && !SysCode::where('param_sn', '06')->where('uuid', $request->input('tier'))->exists()) {
                $errors1['tier_err'] = '科目層級不存在，請選擇正確的科目層級';
            }

            //如果科目層級不為第一層，則上層科目代號須為必填
            if($request['tier']=='aa1f0ed9-48be-11f0-b9d0-002248c47290' && !$request->has('Puuid')){
                $errors1['Puuid'] = '科目層級不為第一層，則上層科目代號須為必填';
            }

            //上層科目需存在在Account資料表中
            if(!Account::where('uuid', $request->input('Puuid'))->exists()){
                $errors1['Puuid'] = '上層科目需存在在科目資料表中';
            }

            //英文別名不為中文
            if ($request->has('alter_name') && preg_match('/[\x{4e00}-\x{9fa5}]/u', $request->input('alter_name')) && str_contains($request['alter_name'] , '*')) {
                $errors1['alter_name_err'] = '英文別名不可包含中文';
            }

            //借貸方僅第四層級為必填欄位，其他層級預設為空值
            if($request['tier']=='52cc568c-48c3-11f0-a3b4-c0185091d167' &&  !$request->has('dc')){
                $errors1['Puuid'] = '科目層級為第四層，需必填';
            }
            //借貸方存在在參數檔
            if (!$request->has('dc') && !SysCode::where('param_sn', '07')->where('uuid', $request->input('dc'))->exists()) {
                $errors1['dc_err'] = '科目層級不存在，請選擇正確的客戶型態';
            }            


            //是否有效不為空字串
            if(empty($request['is_valid']) || str_contains($request['is_valid'] , '*')  ){
                $errors1['is_valid_err'] = ' 是否有效不得為空字串或*';
            } 
            // 如果有錯誤，回傳統一格式
            if (!empty($errors1)) {
                return response()->json([
                    'status' => false,
                    'message1' => '缺少必填的欄位及欄位格式錯誤',
                    'errors' => $errors1
                ], 400);
            }            

            // 建立會計科目
            $Account = Account::create([
                'account_no'      => $request['account_no'],
                'account_name'    => $request['account_name'],
                'Puuid'           => $request['Puuid']?? null,
                'tier'            => $request['tier'],
                'alter_name'      => $request['alter_name']?? null,
                'dc'              => $request['dc'],
                'note'            => $request['note']?? null,
                'is_valid'        => $request['is_valid']
            ]);
    
            if (!$Account) {
                return response()->json([
                    'status' => false,
                    'message' => '資料建立失敗',
                    'output' => null
                ], 404);
            }
    
            return response()->json([
                'status' => true,
                'message' => 'success',
                'output' => $Account
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
     * @OA\POST(
     *     path="/api/updateaccount",
     *     summary="更新會計科目(UUID)",
     *     description="更新會計科目(UUID)",
     *     operationId="updateaccount",
     *     tags={"base_account"},
     *     @OA\Parameter(name="account_no",in="query",required=true,description="會計科目代碼", @OA\Schema(type="string")),
     *     @OA\Parameter(name="account_name",in="query",required=true,description="會計科目名稱",@OA\Schema(type="string")),
     *     @OA\Parameter(name="Puuid",in="query",required=false,description="父層UUID", @OA\Schema(type="string")),
     *     @OA\Parameter(name="tier",in="query",required=false,description="層級",@OA\Schema(type="string")),
     *     @OA\Parameter(name="dc",in="query", required=false,description="借貸方向", @OA\Schema(type="string")),
     *     @OA\Parameter(name="note", in="query", required=false, description="備註", @OA\Schema(type="string")),
     *     @OA\Parameter(name="is_valid",in="query", required=true,description="是否有效", @OA\Schema(type="string", example=1)),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="account_no", type="string", example="1"),
     *             @OA\Property(property="account_name", type="string", example="資產"),
     *             @OA\Property(property="Puuid", type="string", example=""),
     *             @OA\Property(property="tier", type="string", example="1"),
     *             @OA\Property(property="dc", type="string", example="Assets"),
     *             @OA\Property(property="note", type="string", example="指因過去事項所產生之資源，該資源由商業控制，並預期帶來經濟效益之流入。"),    
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="會計科目失敗"
     *     )
     * )
     */
    // 更新會計科目
    public function update(Request $request)
    {
        try{
            // 會計代碼為必填
            if (!$request->has('account_no')) {
                $errors1['account_no_err'] = '會計代碼為必填';
            }else {
                // 檢查會計代碼不為空字串
                if(empty($request['account_no']) || str_contains($request['account_no'] , '*') ){
                    $errors1['account_no_err'] = '會計代碼不得為空字串或*';
                }
                // 檢查會計代碼是否已存在
                $existingClient = Account::where('account_no', $request->input('account_no'))->first();
                if ($existingClient) {
                    $errors1['account_no_err'] = '會計代碼已存在';
                }
            }

            // 會計科目名稱必填
            if (empty($request['account_name'])) {
                $errors1['account_name_err'] = '會計科目為必填';
            }
            // 檢查會計科目不為空字串
            if(empty($request['account_name']) || str_contains($request['account_name'] , '*') ){
                $errors1['account_name_err'] = '會計科目不得為空字串或*';
            }
            //科目層級必填
            if (!$request->has('tier')) {
                $errors1['tier_err'] = '科目層級為必填';
            }
            //科目層級須包含在參數檔
            if (!$request->has('tier') && !SysCode::where('param_sn', '06')->where('uuid', $request->input('tier'))->exists()) {
                $errors1['tier_err'] = '科目層級不存在，請選擇正確的科目層級';
            }

            //如果科目層級不為第一層，則上層科目代號須為必填
            if($request['tier']=='aa1f0ed9-48be-11f0-b9d0-002248c47290' && !$request->has('Puuid')){
                $errors1['Puuid'] = '科目層級不為第一層，則上層科目代號須為必填';
            }

            //上層科目需存在在Account資料表中
            if(!Account::where('uuid', $request->input('Puuid'))->exists()){
                $errors1['Puuid'] = '上層科目需存在在科目資料表中';
            }

            //英文別名不為中文
            if ($request->has('alter_name') && preg_match('/[\x{4e00}-\x{9fa5}]/u', $request->input('alter_name')) && str_contains($request['alter_name'] , '*')) {
                $errors1['alter_name_err'] = '英文別名不可包含中文';
            }

            //借貸方僅第四層級為必填欄位，其他層級預設為空值
            if($request['tier']=='52cc568c-48c3-11f0-a3b4-c0185091d167' &&  !$request->has('dc')){
                $errors1['Puuid'] = '科目層級為第四層，需必填';
            }
            //借貸方存在在參數檔
            if (!$request->has('dc') && !SysCode::where('param_sn', '07')->where('uuid', $request->input('dc'))->exists()) {
                $errors1['dc_err'] = '科目層級不存在，請選擇正確的客戶型態';
            }            


            //是否有效不為空字串
            if(empty($request['is_valid']) || str_contains($request['is_valid'] , '*')  ){
                $errors1['is_valid_err'] = ' 是否有效不得為空字串或*';
            } 
            // 如果有錯誤，回傳統一格式
            if (!empty($errors1)) {
                return response()->json([
                    'status' => false,
                    'message1' => '缺少必填的欄位及欄位格式錯誤',
                    'errors' => $errors1
                ], 400);
            }   

            // 查詢會計科目UUID
            $account = Account::where('uuid', $request['uuid'])->first();
    
            if (!$account) {
                return response()->json([
                    'status' => true,
                    'message' => '會計科目未找到',
                    'output'    => null
                ], 404);
            }
    
            // 更新會計科目
            $account->account_name = $request['account_name'];
            $account->Puuid = $request['Puuid'];
            $account->tier = $request['tier'];
            $account->alter_name = $request['alter_name'];
            $account->dc = $request['dc'];
            $account->note = $request['note'] ?? null;
            $account->is_valid = $request['is_valid'];
            $account->update_user = 'admin';
            $account->update_time = now();
    
            $account->save();
    
            return response()->json([
                'status' => true,
                'message' => 'success',
                'output'    => $account
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
     * @OA\GET(
     *     path="/api/account1/{AccNo}",
     *     summary="查詢特定會計科目資訊",
     *     description="查詢特定會計科目資訊",
     *     operationId="getaccount",
     *     tags={"base_account"},
     *     @OA\Parameter(
     *         name="AccNo",
     *         in="path",
     *         required=true,
     *         description="會計科目代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="account_no", type="string", example="1"),
     *             @OA\Property(property="account_name", type="string", example="資產"),
     *             @OA\Property(property="Puuid", type="string", example=""),
     *             @OA\Property(property="tier", type="string", example="1"),
     *             @OA\Property(property="dc", type="string", example="Assets"),
     *             @OA\Property(property="note", type="string", example="指因過去事項所產生之資源，該資源由商業控制，並預期帶來經濟效益之流入。"),    
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到會計科目"
     *     )
     * )
     */
    // 🔍 查詢單一會計科目
    public function showno($AccNo)
    {
        try{
            $account = Account::getValidAccount($AccNo)->where('is_valid', '1')->first();
        
            if (!$account) {
                 return response()->json([
                     'status' => false,
                     'message' => '會計科目未找到',
                     'output'    => null
                 ], 404);
             }
     
             return response()->json([                
                 'status' => true,
                 'message' => 'success',
                 'output'    => $account
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
     *     path="/api/account/valid",
     *     summary="查詢所有有效會計科目(含關鍵字查詢，會計科目代號、會計科目名稱)",
     *     description="查詢所有有效會計科目(含關鍵字查詢，會計科目代號、會計科目名稱)",
     *     operationId="getallaccount",
     *     tags={"base_account"},
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
    *             		@OA\Property(property="account_no", type="string", example="1"),
    *             		@OA\Property(property="account_name", type="string", example="資產"),
    *             		@OA\Property(property="Puuid", type="string", example=""),
    *             		@OA\Property(property="tier", type="string", example="1"),
    *             		@OA\Property(property="dc", type="string", example="Assets"),
    *             		@OA\Property(property="note", type="string", example="指因過去事項所產生之資源，該資源由商業控制，並預期帶來經濟效益之流入。"),    
    *             		@OA\Property(property="is_valid", type="string", example="1"),
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
     *         description="未找到有效會計科目"
     *     )
     * )
     */
    // 🔍 查詢所有有效會計科目
    public function getvaildaccount(Request $request)
    {
        try{
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

            $sql = "select  *
                    from account
                    where account.is_valid = '1'  
                    and ( account.account_no LIKE ? 
                        OR account.account_name LIKE ? )
                    order by update_time,create_time asc
                    LIMIT ? OFFSET ?;";
            $likeKeyword = '%' . $keyword . '%';

            $account = DB::select($sql, [$likeKeyword, $likeKeyword, $pageSize, $offset]);

            //取得總筆數與總頁數   
            $sql_count = "
                    SELECT COUNT(*) as total
                    from account
                    where account.is_valid = '1'  
                    and ( account.account_no LIKE ? 
                        OR account.account_name LIKE ? )
                    order by update_time,create_time asc;
                ";
            $stmt = $pdo->prepare($sql_count);
            $stmt->execute([$likeKeyword, $likeKeyword]);
            $total = $stmt->fetchColumn();
            $totalPages = ceil($total / $pageSize); // 計算總頁數    

            if (!$account) {
                return response()->json([
                    'status' => true,
                    'atPage' => $page,
                    'total' => $total,
                    'totalPages' => $totalPages,                      
                    'message' => '未找到有效會計科目',
                    'output'    => $account
                ], 404);
            }
            return response()->json([                
                'status' => true,
                'atPage' => $page,
                'total' => $total,
                'totalPages' => $totalPages,                  
                'message' => 'success',
                'output'    => $account
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
     * @OA\patch(
     *     path="/api/account3/{AccNo}/disable",
     *     summary="刪除特定庫別資訊",
     *     description="刪除特定庫別資訊",
     *     operationId="deleteaccount",
     *     tags={"base_account"},
     *     @OA\Parameter(
     *         name="AccNo",
     *         in="path",
     *         required=true,
     *         description="會計科目代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="account_no", type="string", example="1"),
     *             @OA\Property(property="account_name", type="string", example="資產"),
     *             @OA\Property(property="Puuid", type="string", example=""),
     *             @OA\Property(property="tier", type="string", example="1"),
     *             @OA\Property(property="dc", type="string", example="Assets"),
     *             @OA\Property(property="note", type="string", example="指因過去事項所產生之資源，該資源由商業控制，並預期帶來經濟效益之流入。"),    
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )   
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到會計科目"
     *     )
     * )
     */
    // 🔍 刪除特定會計科目
    public function disable($AccNo)
    {
        try{
            $account = Account::where('is_valid', '1')->where('account_no',$AccNo)->first();

            if (!$account) {
                return response()->json([
                    'status' => true,
                    'message' => '會計科目未找到',
                    'output'    => null
                ], 404);
            }
    
            $account->is_valid = 0;
            $account->update_user = 'admin';
            $account->update_time = now();
            $account->save();
    
            return response()->json([
                'status' => true,
                'message' => 'success',
                'output'    => $account
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
     *     path="/api/account2/showconst",
     *     summary="列出所有會計科目需要的常用(下拉、彈窗)",
     *     description="列出所有會計科目需要的常用(下拉、彈窗)",
     *     operationId="show_account_aLL_const",
     *     tags={"base_account"},
     *     @OA\Response(
     *         response=200,
     *         description="成功"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="會計科目需要的常用未找到"
     *     )
     * )
     */
    // 列出所有會計科目需要的常用(下拉、彈窗)
    public function showconst($constant='all'){
        // 查詢 '會計科目階層' 的資料
        $SysCode = SysCode::where('param_sn', '06')->where('is_valid','1')->get();
        // 查詢 '借貸方' 的資料
        $SysCode2 = SysCode::where('param_sn', '07')->where('is_valid','1')->get();
        // 查詢 '所有會計科目' 的資料
        $SysCode3 = Account::where('is_valid','1')->get();
        try {
            // 檢查是否有結果
            if ($SysCode->isEmpty() ) {
                return response()->json([
                    'status' => false,
                    'message' => '常用資料未找到',
                    'levelOption' => null,
                    'dcOption' => null,
                    'accountOption'=> null,
                ], 404);
            }
    
            // 返回查詢結果
            return response()->json([
                'status' => true,
                'message' => 'success',
                'levelOption' => $SysCode,
                'dcOption' => $SysCode2,
                'accountOption'=> $SysCode3,
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
