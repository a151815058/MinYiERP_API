<?php

namespace App\Http\Controllers;
use App\Models\Dept;
use App\Models\SysUser;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DeptSysUserController extends Controller
{
     /**
     * 新增部門與使用者關聯
     */
    /**
     * @OA\POST(
     *     path="/api/assign-userdept",
     *     summary="新增人員部門關聯(不對外)",
     *     description="新增人員部門關聯(不對外)",
     *     operationId="assign-userdept",
     *     tags={"base_assignuserdept"},
     *     @OA\Parameter(
     *         name="usrno",
     *         in="query",
     *         required=true,
     *         description="人員代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="deptno",
     *         in="query",
     *         required=true,
     *         description="部門代號",
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
     *             @OA\Property(property="UsrNo", type="string", example="U001"),
     *             @OA\Property(property="DeptNo", type="string", example="A001"),
     *             @OA\Property(property="is_valid", type="boolean", example=true),
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="客戶端請求錯誤"
     *     )
     * )
     */
    // 驗證請求
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'dept_no'   => 'required|exists:depts,dept_no',
                'user_no'   => 'required|exists:sysusers,user_no',
                'is_valid'    => 'required|string'
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => true,
                    'message' => '資料驗證失敗',
                    'errors' => $validator->errors()
                ], 200);
            }

            // 取得使用者與部門ID
            $user = SysUser::where('user_no', $request['user_no'])->where('is_valid','1')->first(); // 使用 `first()` 獲取模型
            $dept = Dept::where('dept_no', $request['dept_no'])->where('is_valid','1')->first(); // 使用 `first()` 獲取模型

            if (!$dept || !$user) {
                return response()->json([
                    'status' => true,
                    'message' => '使用者或部門不存在',
                    'User'    =>  null,
                    'Dept'    =>  null
                ], status: 400);
            }

            // 新增關聯
            $dept->sysusers()->attach($user->uuid, [
                'is_valid'    => $request['is_valid']
            ]);


            return response()->json([
                    'status' => true,
                    'message' => 'success',
                    '' => $user,
                    'dept' => $dept,
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
                Log::error('建立單據資料錯誤：' . $e->getMessage());
        
                return response()->json([
                    'status' => false,
                    'message' => '伺服器發生錯誤，請稍後再試',
                    'error' => $e->getMessage() // 上線環境建議拿掉
                ], 500);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/dept-users/{deptid}",
     *     summary="讀取部門成員(不對外)",
     *     description="讀取部門成員(不對外)",
     *     operationId="dept-users",
     *     tags={"base_assignuserdept"},
     *     @OA\Parameter(
     *         name="deptid",
     *         in="path",
     *         required=true,
     *         description="部門代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="dept", type="string", example="財務部"),
     *             @OA\Property(property="userId", type="string", example="cd7edb27-a1e2-4df1-aa1a-2f0346935cb2"),
     *             @OA\Property(property="userNo", type="string", example="U001"),
     *             @OA\Property(property="username", type="string", example="姚佩彤"),
     *             @OA\Property(property="is_valid", type="boolean", example=true),   
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", format="date-time", example="2023-10-01T12:00:00Z"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="update_time", type="string", format="date-time", example="2023-10-01T12:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="部門未找到人員"
     *     )
     * )
     */
    // 讀取某個部門的所有使用者
    public function getusersbydept($deptNo)
    {
        try{
            $dept = Dept::with('sysusers')->where('dept_no', $deptNo)->where('is_valid','1')->first();

            if (!$dept) {
                return response()->json([
                    'status' => true,
                    'message' => '未找到部門',
                    'dept'    => null,
                    'users'   => null
                ], 404);
            }else{
                return response()->json([
                    'status' => true,
                    'message' => 'success',
                    'dept' => $dept->DeptNM,
                    'output' => $dept->sysusers->map(function ($user) {
                        return [
                            'id' => $user->uuid,
                            'user_no' => $user->UsrNo,
                            'user_nm' => $user->UsrNM,
                            'is_valid' => $user->is_valid,
                            'Createuser' => $user->pivot->Createuser,
                            'CreateTime' => $user->pivot->CreateTime,
                            'update_user' => $user->pivot->update_user,
                            'update_time' => $user->pivot->update_time
                        ];
                    }),
                ]);
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
                Log::error('建立單據資料錯誤：' . $e->getMessage());
        
                return response()->json([
                    'status' => false,
                    'message' => '伺服器發生錯誤，請稍後再試',
                    'error' => $e->getMessage() // 上線環境建議拿掉
                ], 500);
        }

    }


    /**
     * @OA\GET(
     *     path="/api/user-depts/{userid}",
     *     summary="讀取使用者部門(不對外)",
     *     description="讀取使用者部門(不對外)",
     *     operationId="user-depts",
     *     tags={"base_assignuserdept"},
     *     @OA\Parameter(
     *         name="userid",
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
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="user", type="string", example="姚佩彤"),
     *             @OA\Property(property="Deptid", type="string", example="cd7edb27-a1e2-4df1-aa1a-2f0346935cb2"),
     *             @OA\Property(property="DeptNo", type="string", example="A002"),
     *             @OA\Property(property="DeptNM", type="string", example="財務部"),
     *             @OA\Property(property="is_valid", type="boolean", example=true),   
     *             @OA\Property(property="Createuser", type="string", example="admin"),
     *             @OA\Property(property="CreateTime", type="string", format="date-time", example="2023-10-01T12:00:00Z"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="update_time", type="string", format="date-time", example="2023-10-01T12:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="人員未找到部門"
     *     )
     * )
     */
    //讀取某個使用者所屬的部門
    public function getdeptsbyuser($userNo)
    {
        try{
            $user = SysUser::with('depts')->where('UsrNo', $userNo)->where('is_valid','1')->first();

            if (!$user) {
                return response()->json([
                    'status' => true,
                    'message' => '使用者不存在',
                    'user' => $user
                ], 404);
            }
    
            return response()->json([         
                'status' => true,
                'message' => 'success',
                'user' => $user->UsrNM,
                'output' => $user->depts->map(function ($dept) {
                    return [
                        'Deptid' => $dept->uuid,
                        'DeptNo' => $dept->DeptNo,
                        'DeptNM' => $dept->DeptNM,
                        'is_valid' => $dept->pivot->is_valid,
                        'Createuser' => $dept->pivot->create_user,
                        'CreateTime' => $dept->pivot->create_time,
                        'update_user' => $dept->pivot->update_user,
                        'update_time' => $dept->pivot->update_time
                    ];
                }),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
                // 捕捉驗證失敗
                return response()->json([
                    'status' => false,
                    'message' => '驗證錯誤',
                    'errors' => $e->errors()
                ], 422);
        
        } catch (\Exception $e) {
                // 其他例外處理
                Log::error('建立單據資料錯誤：' . $e->getMessage());
        
                return response()->json([
                    'status' => false,
                    'message' => '伺服器發生錯誤，請稍後再試',
                    'error' => $e->getMessage() // 上線環境建議拿掉
                ], 500);
        }


    }
}
