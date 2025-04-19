<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sysuser;
use App\Models\SysCode;
use App\Models\Dept;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
     *         description="人員所在部門(dept_id，逗號區分)",
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
        // 驗證輸入
        $validator = Validator::make($request->all(),[
            'user_no'   => 'required|string|max:255|unique:sysusers,user_no',
            'user_nm'   => 'required|string|max:255',
            'user_dept' => 'nullable|string|max:255', // 可為多部門，逗號分隔
            'note'      => 'nullable|string|max:255',
            'is_valid'  => 'required|boolean'
        ]);

        // 輸入驗證
        if($validator->fails()){
            return response()->json([
                'status' => true,
                'message' => '資料驗證失敗',
                'errors' => $validator->errors()
            ], 200);
        }  
    
        DB::beginTransaction(); // 開始交易
    
        try {
            // 建立使用者
            $user = Sysuser::create([
                'uuid'     => Str::uuid(),
                'user_no'  => $request['user_no'],
                'user_nm'  => $request['user_nm'],
                'note'     => $request['note'] ?? null,
                'is_valid' => $request['is_valid']
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
                            'create_user'  => 'admin',
                            'create_time'  => now(),
                            'update_user'  => 'admin',
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
     * @OA\GET(
     *     path="/api/user/{userno}",
     *     summary="查詢特定人員資訊",
     *     description="查詢特定人員資訊",
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
     *     summary="查詢所有有效人員資訊(含關鍵字查詢)",
     *     description="查詢所有有效人員資訊(含關鍵字查詢)",
     *     operationId="getalluser",
     *     tags={"base_user"},
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
     *             @OA\Property(property="UsrNo", type="string", example="U001"),
     *             @OA\Property(property="UsrNM", type="string", example="姚佩彤"),
     *             @OA\Property(property="Note", type="string", example=""),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="UpdateUser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="UpdateTime", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
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
            $keyword = $request->query('keyword'); // 可為 null

            // 進行關鍵字查詢
            if($keyword != null) {
                $likeKeyword = '%' . $keyword . '%';
                $user = SysUser::with('depts')
                ->where('is_valid', '1')
                ->where(function ($query) use ($likeKeyword) {
                    $query->where('user_no', 'like', $likeKeyword)
                          ->orWhere('user_nm', 'like', $likeKeyword);
                })
                ->get();

            } else {
                $user = SysUser::with('depts')->where('is_valid', '1')->get();
            }
            
            // 回應 JSON
            if (!$user) {
                return response()->json([
                    'status' => true,
                    'message' => '未有效找到人員',
                    'output'    => $user
                ], status: 404);
            }else {
            // 回應 JSON
                return response()->json([
                    'status' => true,
                    'message' => 'success',
                    'output'    => $user
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
