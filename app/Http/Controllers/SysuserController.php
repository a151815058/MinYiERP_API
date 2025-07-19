<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sysuser;
use App\Models\SysCode;
use Illuminate\Support\Facades\Auth;
require_once base_path('app/Models/connect.php'); 
use App\Models\Dept;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ValidationHelper;
use Carbon\Carbon;

class SysuserController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/createuser",
     *     summary="新增人員資訊",
     *     description="新增人員資訊",
     *     operationId="createuser",
     *     tags={"base_user"},
     *     @OA\Parameter(
     *         name="user_no",
     *         in="query",
     *         required=true,
     *         description="人員代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="user_nm",
     *         in="query",
     *         required=true,
     *         description="人員名稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="user_dept",
     *         in="query",
     *         required=false,
     *         description="人員所在部門uuid(開窗選擇)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="note",
     *         in="query",
     *         required=false,
     *         description="備註",
     *         @OA\Schema(type="string")
     *     ),
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
     *             @OA\Property(property="user_no", type="string", example="U001"),
     *             @OA\Property(property="user_nm", type="string", example="姚佩彤"),
     *             @OA\Property(property="user_dept", type="string", example="D001,D002"),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="客戶端請求錯誤"
     *     )
     * )
     */
    // 儲存人員資料
    public function store(Request $request)
    {
        $errors1 = [];

        // 使用者代碼為必填
        if (!$request->filled('user_no')) {
            $errors1['user_no_err'] = '使用者代碼為必填';
        }else {
            // 判斷使用者代碼不能存在空白、""、''、"、'
            if (!ValidationHelper::isValidText($request->input('user_no'))) {
                $errors1['user_no_err'] = '使用者代碼不得為空字串或*';
            }
            // 檢查使用者代碼是否已存在
            $existingUser = Sysuser::where('user_no', $request->input('user_no'))->first();
            if ($existingUser) {
                $errors1['user_no_err'] = '使用者代碼已存在';
            }
        }

        // 隸屬部門為必填
        if (!$request->filled('user_dept')) {
            $errors1['user_dept_err'] = '隸屬部門為必填';
        }

        // 如果有錯誤，回傳統一格式
        if (!empty($errors1)) {
            return response()->json([
                'status' => false,
                'message' => '缺少必填的欄位及欄位格式錯誤',
                'errors' => $errors1
            ], 400);
        }
    
        DB::beginTransaction(); // 開始交易
    
        try {
            // 建立使用者
            $user = Sysuser::create([
                'uuid'     => Str::uuid(),
                'user_no'  => $request['user_no'],
                'user_nm'  => $request['user_nm'],
                'note'     => $request['note'] ?? null,
                'is_valid' => $request['is_valid'],
                'create_user'     => Auth::user()->username ?? 'admin',
                'update_user'     => Auth::user()->username ?? 'admin',
                'create_time'     => now(),
                'update_time'     => now()
            ]);

            DB::commit(); // 成功則提交
    
            $attachedDepts = [];
    
            // 建立部門關聯
            if (!empty($request['user_dept'])) {
                $deptNos = explode(',', $request['user_dept']);
                foreach ($deptNos as $deptNo) {
                    $dept = Dept::where('uuid', trim($deptNo))->first();
                    if ($dept) {
                        $user->depts()->attach($dept->uuid, [
                            'uuid'         => Str::uuid(),
                            'dept_id'      => $dept->uuid,
                            'user_id'      => $user->uuid,
                            'is_valid'     => 1,
                            'create_user'  => Auth::user()->username ?? 'admin',
                            'create_time'  => now(),
                            'update_user'  => Auth::user()->username ?? 'admin',
                            'update_time'  => now()
                        ]);
                        $attachedDepts[] = $dept->dept_no;
                    }
                }
            }
    
            
    
            return response()->json([
                'status'  => true,
                'message' => 'success',
                'User'    => $user,
                'Depts'   => $attachedDepts
            ], 201);
    
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
     *     path="/api/updateuser",
     *     summary="更新人員資訊",
     *     description="更新人員資訊",
     *     operationId="updateuser",
     *     tags={"base_user"},
     *     @OA\Parameter(
     *         name="user_no",
     *         in="query",
     *         required=true,
     *         description="人員代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="user_nm",
     *         in="query",
     *         required=true,
     *         description="人員名稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="user_dept",
     *         in="query",
     *         required=false,
     *         description="人員所在部門uuid(開窗選擇)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="note",
     *         in="query",
     *         required=false,
     *         description="備註",
     *         @OA\Schema(type="string")
     *     ),
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
     *             @OA\Property(property="user_no", type="string", example="U001"),
     *             @OA\Property(property="user_nm", type="string", example="姚佩彤"),
     *             @OA\Property(property="user_dept", type="string", example="D001,D002"),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="客戶端請求錯誤"
     *     )
     * )
     */
    // 更新人員資料
    public function update(Request $request, $id)
    {
        $errors1 = [];

        $errors1 = [];

        // 使用者代碼為必填
        if (!$request->filled('user_no')) {
            $errors1['user_no_err'] = '使用者代碼為必填';
        }else {
            // 判斷使用者代碼不能存在空白、""、''、"、'
            if (!ValidationHelper::isValidText($request->input('user_no'))) {
                $errors1['user_no_err'] = '使用者代碼不得為空字串或*';
            }
            // 檢查使用者代碼是否已存在
            $existingUser = Sysuser::where('user_no', $request->input('user_no'))->first();
            if ($existingUser) {
                $errors1['user_no_err'] = '使用者代碼已存在';
            }
        }

        // 隸屬部門為必填
        if (!$request->filled('user_dept')) {
            $errors1['user_dept_err'] = '隸屬部門為必填';
        }

        DB::beginTransaction(); // 開始交易

        try {
            // 查詢使用者資料user_uuid
            $Client = Sysuser::where('uuid', $request->input('uuid'))->first();
            if (!$Client) {
                return response()->json([
                    'status' => false,
                    'message' => '欄位資料錯誤',
                    'user_no_err'    =>  '使用者資料未找到',
                ], 400);
            }

            $user = Sysuser::findOrFail($Client->uuid);
            $user->update([
                'user_no'  => $request['user_no'],
                'user_nm'  => $request['user_nm'],
                'note'     => $request['note'] ?? null,
                'is_valid' => $request['is_valid'],
                'update_user'     => Auth::user()->username ?? 'admin',
                'update_time'     => now()
            ]);

            // 清除舊的部門關聯
            $user->depts()->detach();

            // 建立新的部門關聯
            if (!empty($request['user_dept'])) {
                $deptNos = explode(',', $request['user_dept']);
                foreach ($deptNos as $deptNo) {
                    $dept = Dept::where('uuid', trim($deptNo))->first();
                    if ($dept) {
                        $user->depts()->attach($dept->uuid, [
                            'uuid'         => Str::uuid(),
                            'dept_id'      => $dept->uuid,
                            'user_id'      => $user->uuid,
                            'is_valid'     => 1,
                            'create_user'  => Auth::user()->username ?? 'admin',
                            'create_time'  => now(),
                            'update_user'  => Auth::user()->username ?? 'admin',
                            'update_time'  => now()
                        ]);
                    }
                }
            }

            DB::commit(); // 提交交易

            return response()->json([
                'status' => true,
                'message' => '更新成功',
                'User'    => $user
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
     *     path="/api/user/{userno}",
     *     summary="查詢特定人員資訊",
     *     operationId="getuser",
     *     tags={"base_user"},
     *     @OA\Parameter(
     *         name="userno",
     *         in="path",
     *         required=true,
     *         description="人員代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="user_no", type="string", example="U001"),
     *             @OA\Property(property="user_nm", type="string", example="姚佩彤"),
     *             @OA\Property(property="user_dept", type="string", example="D001"),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到人員"
     *     )
     * )
     */
    // 🔍 查詢單一人員
    public function showno($UsrNo)
    {
        try{
            $user = SysUser::with('depts')->where('user_no', $UsrNo)->where('is_valid','1')->first();

            // 回應 JSON
            if (!$user) {
                return response()->json([
                    'status' => true,
                    'message' => '未找到人員',
                    'User'    => null
                ], status: 404);
            }else {
                // 回應 JSON
                return response()->json([
                    'status' => true,
                    'message' => 'success',
                    'User'    => $user
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
     *     path="/api/user3/{dept_id}",
     *     summary="查詢部門下面的人員",
     *     description="查詢部門下面的人員",
     *     operationId="getdeptuser",
     *     tags={"base_user"},
     *     @OA\Parameter(
     *         name="dept_id",
     *         in="path",
     *         required=true,
     *         description="部門id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="user_no", type="string", example="U001"),
     *             @OA\Property(property="user_nm", type="string", example="姚佩彤"),
     *             @OA\Property(property="user_dept", type="string", example="D001"),
     *             @OA\Property(property="note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到人員"
     *     )
     * )
     */
    // 🔍 查詢部門下面的人員
    public function showdeptuser($dept_id)
    {
        try{
            $decodedName = urldecode($dept_id);
            $user = Dept::with('sysusers')->where('uuid', $dept_id)->where('is_valid','1')->first();
            #$dept = Dept::findByDeptNM($decodedName);
            // 查詢特定發票資訊(以期別查詢，只要起迄其中符合即可)
            
            if (!$user) {
                return response()->json([
                    'status' => true,
                    'message' => '人員未找到',
                    'output'    => null
                ], 404);
            }
    
            return response()->json([                
                'status' => true,
                'message' => 'success',
                'output'    => $user
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
     *     path="/api/users/valid",
     *     summary="查詢所有有效人員資訊(含關鍵字查詢，隸屬部門、人員代碼、人員名稱)",
     *     description="查詢所有有效人員資訊(含關鍵字查詢，隸屬部門、人員代碼、人員名稱)",
     *     operationId="getalluser",
     *     tags={"base_user"},
     *     @OA\Parameter(
     *         name="dept_id",
     *         in="query",
     *         required=false,
     *         description="隸屬部門",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         required=false,
     *         description="關鍵字查詢",
     *         @OA\Schema(type="string")
     *     ),
    * @OA\Response(
    *     response=200,
    *     description="成功取得分頁使用者清單",
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
    *                 @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
    *                 @OA\Property(property="UsrNo", type="string", example="U001"),
    *                 @OA\Property(property="UsrNM", type="string", example="姚佩彤"),
    *                 @OA\Property(property="Note", type="string", example=""),
    *                 @OA\Property(property="is_valid", type="boolean", example=true),
    *                 @OA\Property(property="Createuser", type="string", example="admin"),
    *                 @OA\Property(property="UpdateUser", type="string", example="admin"),
    *                 @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
    *                 @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
    *             )
    *         )
    *     )
    * ),
     *     @OA\Response(
     *         response=404,
     *         description="未有效找到人員"
     *     )
     * )
     */
    // 🔍 查詢所有有效人員
    public function getvalidusers(Request $request)
    {
        try{
            $pdo = getPDOConnection();
            $keyword = $request->query('keyword'); // 可為 null
            $dept_id = $request->query('dept_id'); // 可為 null
            $page = $request->query('page'); // 當前頁碼
            $pageSize = $request->query('pageSize'); // 一頁顯示幾筆數值
            $page = $page ? (int)$page : 1; // 預設為第 1 頁
            $pageSize = $pageSize ? (int)$pageSize : 30; // 預設每頁顯示 30 筆資料

            
            //進行關鍵字查詢
            if ($keyword != null && $keyword != '') {
                $likeKeyword = '%' . $keyword . '%';
            } else {
                $likeKeyword = '%'; // 如果沒有關鍵字，則查詢所有
            }

            //進行隸屬部門查詢
            if ($dept_id != null && $dept_id != '') {
                $dept_id = $dept_id; // 如果有部門ID，則查詢
            } else {
                $dept_id = null; // 如果沒有部門ID，則查詢所有部門
            }

            // 進行關鍵字、隸屬部門查詢
            if($keyword != null && $keyword != '') {
                $likeKeyword = '%' . $keyword . '%';
                // 如果有部門ID，則查詢該部門下的有效人員
                if ($dept_id != null && $dept_id != '') {
                    $dept_id = $dept_id; // 如果有部門ID，則查詢
                } else {
                    $dept_id = null; // 如果沒有部門ID，則查詢所有部門
                }
            }

            //取得總筆數與總頁數
            $sql_count = "
                    SELECT COUNT(*) as total
                    FROM sysusers
                    WHERE sysusers.is_valid = '1'
                    AND EXISTS (
                    SELECT sysuser_depts.dept_id,
                        sysuser_depts.user_id
                    FROM sysuser_depts
                    left JOIN depts ON depts.`uuid` = sysuser_depts.dept_id  and (depts.uuid = ? OR ? IS NULL)
                    WHERE sysusers.`uuid` = sysuser_depts.user_id
                    )
                    and (sysusers.user_no LIKE ? OR sysusers.user_nm LIKE ?)
                ";
                $stmt = $pdo->prepare($sql_count);
                $stmt->execute([$likeKeyword, $likeKeyword, $dept_id, $dept_id]);
                $total = $stmt->fetchColumn();
                $totalPages = ceil($total / $pageSize); // 計算總頁數                  

                //查詢目前頁數的資料
                $offset = ($page - 1) * $pageSize;
                //LIMIT 30：每次最多回傳 30 筆資料
                //OFFSET 0：從第 0 筆開始取，也就是第一頁的第 1 筆
                //LIMIT 30 OFFSET 0  -- 取第 1~30 筆
                //LIMIT 30 OFFSET 30 -- 取第 31~60 筆
                //LIMIT 30 OFFSET 60 -- 取第 61~90 筆
                $sql_data = "select  *
                    FROM sysusers
                    INNER JOIN sysuser_depts ON sysuser_depts.user_id = sysusers.`uuid`
                    INNER JOIN depts ON depts.`uuid` = sysuser_depts.dept_id
                    WHERE sysusers.is_valid = '1'
                    AND (
                        sysusers.user_no LIKE ? OR sysusers.user_nm LIKE ?
                    )
                    AND (
                        depts.uuid = ? OR ? IS NULL
                    )
                    ORDER BY sysusers.user_no
                    LIMIT ? OFFSET ?
                    ;";      
                $rawUsers = DB::select($sql_data, [$likeKeyword, $likeKeyword, $dept_id, $dept_id, $pageSize, $offset]);

                // 將 stdClass 轉成 array（optional）
                $users = json_decode(json_encode($rawUsers), true);

                // 重組資料：以 user_id 分組，整合部門資料
                $groupedUsers = [];

                foreach ($users as $row) {
                    $userId = $row['user_id'];

                    if (!isset($groupedUsers[$userId])) {
                        $groupedUsers[$userId] = [
                            'user_id'       => $row['user_id'],
                            'user_no'       => $row['user_no'],
                            'user_nm'       => $row['user_nm'],
                            'note'          => $row['note'],
                            'is_valid'      => $row['is_valid'],
                            'create_user'   => $row['create_user'],
                            'create_time'   => $row['create_time'],
                            'update_user'   => $row['update_user'],
                            'update_time'   => $row['update_time'],
                            'depts'   => [],
                        ];
                    }

                    $groupedUsers[$userId]['depts'][] = [
                        'dept_id'  => $row['dept_id'],
                        'dept_no'  => $row['dept_no'],
                        'dept_nm'  => $row['dept_nm'],
                        'uuid'     => $row['uuid'],
                        'is_valid' => $row['is_valid'],
                        'note'     => $row['note'] ?? null,
                    ];
                }

                // 轉成 array values 給前端（移除 user_id 為 key）
                $output = array_values($groupedUsers);

                // 回應 JSON
                if (empty($output)) {
                    return response()->json([
                        'status' => true,
                        'atPage' => $page,
                        'total' => $total,
                        'totalPages' => $totalPages,
                        'message' => '未有效找到人員',
                        'output' => []
                    ], 404);
                } else {
                    return response()->json([
                        'status' => true,
                        'atPage' => $page,
                        'total' => $total,
                        'totalPages' => $totalPages,
                        'message' => 'success',
                        'output' => $output
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
     *     path="/api/user/{userno}/disable",
     *     summary="刪除特定人員資訊",
     *     description="刪除特定人員資訊",
     *     operationId="deleteuser",
     *     tags={"base_user"},
     *     @OA\Parameter(
     *         name="userno",
     *         in="path",
     *         required=true,
     *         description="人員代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="UsrNo", type="string", example="U001"),
     *             @OA\Property(property="UsrNM", type="string", example="姚佩彤"),
     *             @OA\Property(property="Note", type="string", example="測試測試"),
     *             @OA\Property(property="is_valid", type="boolean", example=false),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )   
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到人員"
     *     )
     * )
     */
    // 🔍 刪除特定部門
    public function disable($UsrNo)
    {
        try{
            $user = SysUser::where('user_no', $UsrNo)->where('is_valid','1')->first();
        
            if (!$user) {
                return response()->json([
                    'status' => true,
                    'message' => '人員未找到',
                    'Dept'    => null
                ], 404);
            }
    
            $user->is_valid = 0;
            $user->update_user = 'admin';
            $user->update_time = now();
            $user->save();
    
            return response()->json([
                'status' => true,
                'message' => 'success',
                'Dept'    => $user
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
     *     path="/api/users/showconst",
     *     summary="列出所有人員需要的常用(下拉、彈窗)",
     *     description="列出所有人員需要的常用(下拉、彈窗)",
     *     operationId="show_user_all_const",
     *     tags={"base_user"},
     *     @OA\Response(
     *         response=200,
     *         description="成功"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="人員需要的常用未找到"
     *     )
     * )
     */
    // 列出所有人員需要的常用(下拉、彈窗)
    public function showconst($constant='all'){
        // 查詢 '所有有效部門資料' 的資料
        $SysCode = Dept::where('is_valid', '1')->get();
        try {
            // 檢查是否有結果
            if (!$SysCode) {
                return response()->json([
                    'status' => true,
                    'message' => '常用資料未找到',
                    'deptoption' => null
                ], 404);
            }
    
            // 返回查詢結果
            return response()->json([
                'status' => true,
                'message' => 'success',
                'deptoption' => $SysCode
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
