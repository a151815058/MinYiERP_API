<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\SysCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
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
     *     @OA\Parameter(
     *         name="account_no",
     *         in="query",
     *         required=true,
     *         description="會計科目代碼", 
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="account_name",
     *         in="query",
     *         required=true,
     *         description="會計科目名稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="Puuid",
     *         in="query",
     *         required=false,
     *         description="父層UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="tier",
     *         in="query",
     *         required=true,
     *         description="層級",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="dc",
     *         in="query",
     *         required=false,
     *         description="借貸方向",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          name="note", 
     *          in="query", 
     *          required=false, 
     *          description="備註", 
     *          @OA\Schema(type="string")),
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
        try{
            $validator = Validator::make($request->all(),[
                'account_no'    => 'required|string|max:10',
                'account_name'  => 'required|string|max:100',
                'Puuid'         => 'nullable|string|max:255',
                'tier'          => 'required|string|max:100',
                'dc'            => 'nullable|string|max:255',
                'note'          => 'required|string',
                'is_valid'      => 'required|boolean',
            ]);
            if($validator->fails()){
                return response()->json([
                    'status' => true,
                    'message' => '資料驗證失敗',
                    'errors' => $validator->errors()
                ], 200);
            }

            // 建立會計科目
            $Account = Account::create([
                'account_no'      => $request['account_no'],
                'account_name'    => $request['account_name'],
                'Puuid'           => $request['Puuid'],
                'tier'            => $request['tier'],
                'dc'              => $request['dc'],
                'note'            => $request['note']?? null,
                'is_valid'        => $request['is_valid']
            ]);
    
            if (!$Account) {
                return response()->json([
                    'status' => true,
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
     *     summary="查詢所有有效會計科目(含關鍵字查詢)",
     *     description="查詢所有有效會計科目(含關鍵字查詢)",
     *     operationId="getallaccount",
     *     tags={"base_account"},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         required=false,
     *         description="關鍵字查詢",
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
     *         description="未找到有效會計科目"
     *     )
     * )
     */
    // 🔍 查詢所有有效會計科目
    public function getvaildaccount(Request $request)
    {
        try{
            $keyword = $request->query('keyword'); // 可為 null

            // 使用 DB::select 進行關鍵字查詢
            if($keyword != null) {
                //會計科目代號、會計科目名稱
                // 這裡使用了 SQL 的 LIKE 語法來進行模糊查詢
                $likeKeyword = '%' . $keyword . '%';
                $sql = "select  *
                        from account
                        where account.is_valid = '1'  
                        and ( account.account_no LIKE ? 
                           OR account.account_name LIKE ? )
                        order by update_time,create_time asc;";

                $account = DB::select($sql, [$likeKeyword, $likeKeyword]);

            } else {
                $account = Account::where('is_valid', '1')->get();
            }

            if (!$account) {
                return response()->json([
                    'status' => true,
                    'message' => '未找到有效會計科目',
                    'output'    => $account
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
}
