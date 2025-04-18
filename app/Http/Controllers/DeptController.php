<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dept;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class DeptController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/createdept",
     *     summary="新增部門資訊",
     *     description="新增部門資訊",
     *     operationId="createdept",
     *     tags={"base_dept"},
     *     @OA\Parameter(
     *         name="dept_no",
     *         in="query",
     *         required=true,
     *         description="部門代號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="dept_nm",
     *         in="query",
     *         required=true,
     *         description="部門名稱",
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
     *             @OA\Property(property="dept_no", type="string", example="A02"),
     *             @OA\Property(property="dept_nm", type="string", example="財務處"),
     *             @OA\Property(property="note", type="string", example="測試測試"),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到部門"
     *     )
     * )
     */
    // 儲存部門資料
    public function store(Request $request)
    {
        try {
            // 驗證請求
            $validator = Validator::make($request->all(),[
                'dept_no'     => 'required|string|max:255|unique:depts,dept_no',
                'dept_nm'     => 'required|string|max:255',
                'note'       => 'nullable|string|max:255',
                'is_valid'    => 'required|boolean'
            ]);
            if($validator->fails()){
                return response()->json([
                    'status' => true,
                    'message' => '資料驗證失敗',
                    'errors' => $validator->errors()
                ], 200);
            }

            // 建立部門資料
            $dept = Dept::create([
                'uuid'       => Str::uuid(),  // 自動生成 UUID
                'dept_no'     => $request['dept_no'],
                'dept_nm'     => $request['dept_nm'],
                'note'       => $request['note'] ?? null,
                'is_valid'    => $request['is_valid']
            ]);

            if (!$dept) {
                return response()->json([
                    'status' => true,
                    'message' => '部門建立失敗',
                    'output'    => null
                ], status: 404);
            }else {
                // 回應 JSON
                return response()->json([
                    'status' => true,
                    'message' => 'success',
                    'output'    => $dept
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
     *     path="/api/dept/{deptno}",
     *     summary="查詢特定部門資訊",
     *     description="查詢特定部門資訊",
     *     operationId="getdeptno",
     *     tags={"base_dept"},
     *     @OA\Parameter(
     *         name="deptno",
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
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="dept_no", type="string", example="A02"),
     *             @OA\Property(property="dept_nm", type="string", example="財務處"),
     *             @OA\Property(property="note", type="string", example="測試測試"),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到部門"
     *     )
     * )
     */
    // 🔍 查詢單一部門
    public function showno($deptNo)
    {
        try{
            $decodedName = urldecode($deptNo);
            $sql = "select  *
                    from depts
                    where depts.dept_no = ? and is_valid = '1'";
    
            $dept = DB::select($sql, [$decodedName]);        
            if (!$dept) {
                return response()->json([
                    'status' => false,
                    'message' => '部門未找到',
                    'output'    => null
                ], 404);
            }
    
            return response()->json([                
                'status' => true,
                'message' => 'success',
                'output'    => $dept
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
     *     path="/api/depts/valid",
     *     summary="查詢所有有效部門資訊(含關鍵字查詢)",
     *     description="查詢所有有效部門資訊(含關鍵字查詢)",
     *     operationId="getalldept",
     *     tags={"base_dept"},
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
     *             @OA\Property(property="dept_no", type="string", example="A02"),
     *             @OA\Property(property="dept_nm", type="string", example="財務處"),
     *             @OA\Property(property="note", type="string", example="測試測試"),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到有效部門"
     *     )
     * )
     */
    // 🔍 查詢所有有效部門
    public function getvaliddepts(Request $request)
    {
        try{
            $keyword = $request->query('keyword'); // 可為 null

            // 使用 DB::select 進行關鍵字查詢
            if($keyword != null) {
                $likeKeyword = '%' . $keyword . '%';
                $sql = "select  *
                        from depts
                        where depts.is_valid = '1'  
                        and ( depts.dept_no LIKE ? OR depts.dept_nm LIKE ?)
                        order by update_time,create_time asc;";

                $depts = DB::select($sql, [$likeKeyword, $likeKeyword]);

            } else {
                $depts = Dept::where('is_valid', '1')->get();
            }

            if (!$depts) {
                return response()->json([
                    'status' => true,
                    'message' => '未找到有效部門',
                    'output'    => $depts
                ], 404);
            }
            return response()->json([                
                'status' => true,
                'message' => 'success',
                'output'    => $depts
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
            Log::error('資料錯誤：' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        }

    }
    /**
     * @OA\patch(
     *     path="/api/dept/{deptno}/disable",
     *     summary="刪除特定部門資訊",
     *     description="刪除特定部門資訊",
     *     operationId="deletedept",
     *     tags={"base_dept"},
     *     @OA\Parameter(
     *         name="deptno",
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
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="dept_no", type="string", example="A02"),
     *             @OA\Property(property="dept_nm", type="string", example="財務處"),
     *             @OA\Property(property="note", type="string", example="測試測試"),
     *             @OA\Property(property="is_valid", type="string", example="0"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )   
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="未找到部門"
     *     )
     * )
     */
    // 🔍 刪除特定部門
    public function disable($deptNo)
    {
        try{
            $dept = Dept::findByDeptNo($deptNo)->where('is_valid', '1')->first();
        
            if (!$dept) {
                return response()->json([
                    'status' => true,
                    'message' => '部門未找到',
                    'output'    => null
                ], 404);
            }
    
            $dept->is_valid = 0;
            $dept->update_user = 'admin';
            $dept->update_time = now();
            $dept->save();
    
            return response()->json([
                'status' => true,
                'message' => 'success',
                'output'    => $dept
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
            Log::error('資料錯誤：' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => '伺服器發生錯誤，請稍後再試',
                'error' => $e->getMessage() // 上線環境建議拿掉
            ], 500);
        }

    }
}


