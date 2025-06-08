<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dept;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
require_once base_path('app/Models/connect.php'); 
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
     *     @OA\Parameter(name="dept_no",in="query",required=true,description="部門代號",@OA\Schema(type="string")),
     *     @OA\Parameter(name="dept_nm",in="query",required=true,description="部門名稱",@OA\Schema(type="string")),
     *     @OA\Parameter(name="note",in="query",required=false,description="備註",@OA\Schema(type="string")),
     *     @OA\Parameter(name="is_valid",in="query",required=true,description="是否有效",@OA\Schema(type="string", example=1)),
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
                'note'        => 'nullable|string|max:255',
                'is_valid'    => 'required|boolean'
            ]);
            if($validator->fails()){
                return response()->json([
                    'status' => true,
                    'message' => '必填欄位驗證失敗',
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
     * @OA\POST(
     *     path="/api/updatedept",
     *     summary="更新部門資訊",
     *     description="更新部門資訊",
     *     operationId="createdept",
     *     tags={"base_dept"},
     *     @OA\Parameter(name="dept_no",in="query",required=true,description="部門代號",@OA\Schema(type="string")),
     *     @OA\Parameter(name="dept_nm",in="query",required=true,description="部門名稱",@OA\Schema(type="string")),
     *     @OA\Parameter(name="note",in="query",required=false,description="備註",@OA\Schema(type="string")),
     *     @OA\Parameter(name="is_valid",in="query",required=true,description="是否有效",@OA\Schema(type="string", example=1)),
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
    // 更新部門資料
    public function update(Request $request)
    {
        try {
            // 驗證請求
            $validator = Validator::make($request->all(),[
                'dept_no'     => 'required|string|max:255',
                'dept_nm'     => 'required|string|max:255',
                'note'        => 'nullable|string|max:255',
                'is_valid'    => 'required|boolean'
            ]);
            if($validator->fails()){
                return response()->json([
                    'status' => true,
                    'message' => '必填欄位驗證失敗',
                    'errors' => $validator->errors()
                ], 200);
            }

            // 更新部門資料
            $dept = Dept::findByDeptNo($request['dept_no'])->where('is_valid', '1')->first();
        
            if (!$dept) {
                return response()->json([
                    'status' => true,
                    'message' => '部門未找到',
                    'output'    => null
                ], 404);
            }
    
            $dept->dept_nm = $request['dept_nm'];
            $dept->note = $request['note'] ?? null;
            $dept->is_valid = $request['is_valid'];
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
            Log::error('更新資料錯誤：' . $e->getMessage());
    
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
     *     summary="查詢所有有效部門資訊(含關鍵字查詢，部門代號、部門名稱)",
     *     description="查詢所有有效部門資訊(含關鍵字查詢，部門代號、部門名稱)",
     *     operationId="getalldept",
     *     tags={"base_dept"},
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
    *                 @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
    *                 @OA\Property(property="dept_no", type="string", example="A02"),
    *                 @OA\Property(property="dept_nm", type="string", example="財務處"),
    *                 @OA\Property(property="note", type="string", example="測試測試"),
    *                 @OA\Property(property="is_valid", type="string", example="1"),
    *                 @OA\Property(property="create_user", type="string", example="admin"),
    *                 @OA\Property(property="create_time", type="string", example="admin"),
    *                 @OA\Property(property="update_user", type="string", example="2025-03-31T08:58:52.001975Z"),
    *                 @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
    *             )
    *         )
    *     )
    * ),
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
                    from depts
                    where depts.is_valid = '1'  
                    and ( depts.dept_no LIKE ? OR depts.dept_nm LIKE ?)
                    order by update_time,create_time asc
                    LIMIT ? OFFSET ?;";
            $likeKeyword = '%' . $keyword . '%';

            $depts = DB::select($sql, [$likeKeyword, $likeKeyword, $pageSize, $offset]);

            //取得總筆數與總頁數   
            $sql_count = "
                    SELECT COUNT(*) as total
                    from depts
                        where depts.is_valid = '1'  
                        and ( depts.dept_no LIKE ? OR depts.dept_nm LIKE ?)
                        order by update_time,create_time asc;
                ";
            $stmt = $pdo->prepare($sql_count);
            $stmt->execute([$likeKeyword, $likeKeyword]);
            $total = $stmt->fetchColumn();
            $totalPages = ceil($total / $pageSize); // 計算總頁數  

            if (!$depts) {
                return response()->json([
                    'status' => true,
                    'atPage' => $page,
                    'total' => $total,
                    'totalPages' => $totalPages,                    
                    'message' => '未找到有效部門',
                    'output'    => $depts
                ], 404);
            }
            return response()->json([                
                'status' => true,
                'atPage' => $page,
                'total' => $total,
                'totalPages' => $totalPages,                
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


