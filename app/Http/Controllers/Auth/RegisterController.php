<?php 


namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;

class RegisterController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/RegisterAccount",
     *     summary="註冊帳號密碼",
     *     description="註冊帳號密碼",
     *     operationId="RegisterAccount",
     *     tags={"base_auth"},
     *     @OA\Parameter(name="username",in="query",required=true,description="使用者名稱", @OA\Schema(type="string")),
     *     @OA\Parameter(name="useraccount",in="query",required=true,description="使用者帳號", @OA\Schema(type="string")),
     *     @OA\Parameter( name="password",in="query",required=true, description="密碼", @OA\Schema(type="string")),
     *    @OA\Parameter(name="mail",in="query",required=false,description="電子郵件", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="username", type="string", example="wendyyao"),
     *             @OA\Property(property="useraccount", type="string", example="wendyyao"),
     *             @OA\Property(property="password_hash", type="string", example="$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"),
     *             @OA\Property(property="mail", type="string", example="wendyyao@example.com"),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="註冊帳號密碼失敗"
     *     )
     * )
     */
    public function register(Request $request)
    {
        // 使用者帳號和密碼為必填欄位
        if (!$request->has('username') || !$request->has('password')) {
            return response()->json([
                'status' => false,
                'message' => '帳號和密碼為必填欄位',
                'data' => []
            ], 422);
        }

        $user = User::create([
            'id' => Str::uuid(),
            'username' => $request->username,
            'useraccount' => $request->useraccount, // 使用者帳號同使用者名稱
            'mail' => $request->mail ?? null, // 電子郵件可選
            'password_hash' => Hash::make($request->password),
            'status' => 1, // 預設狀態為有效
            'create_user' => 'system', // 系統預設建立者
            'update_user' => 'system', // 系統預設更新者
            'create_time' => now(),
            'update_time' => now()
        ]);

        return response()->json([
            'status' => true,
            'message' => '註冊成功',
            'output' => $user
        ], 201);
    }
}