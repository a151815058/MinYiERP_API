<?php

// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Mews\Captcha\Facades\Captcha;
class LoginController extends Controller
{
    /**
     * @OA\get(
     *     path="/api/getcaptcha",
     *     summary="取得驗證碼",
     *     description="取得驗證碼",
     *     operationId="getcaptcha",
     *     tags={"base_auth"},
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="captcha_img", type="string", example="data:image/png;base64,iVBORw0K..."),
     *             @OA\Property(property="key", type="string", example="f2bb62f0fae1f8b935d...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="取得驗證碼失敗"
     *     )
     * )
     */
    //提供驗證碼給前端
    public function captcha()
    {
        $captcha = Captcha::create('default', true); // 第二參數 true 表示回傳 base64
        return response()->json([
            'captcha_img' => $captcha['img'], // base64 圖片
            'key' => $captcha['key']          // Laravel session 裡會對應此 key
        ]);
    }
    /**
     * @OA\POST(
     *     path="/api/verifyuser",
     *     summary="驗證登入帳號密碼",
     *     description="驗證登入帳號密碼",
     *     operationId="verifyuser",
     *     tags={"base_auth"},
     *     @OA\Parameter(name="useraccount",in="query",required=true,description="使用者名稱", @OA\Schema(type="string")),
     *     @OA\Parameter( name="password",in="query",required=true, description="密碼", @OA\Schema(type="string")),
     *     @OA\Parameter(name="captcha_input",in="query",required=true,description="驗證碼", @OA\Schema(type="string")),
     *     @OA\Parameter( name="captcha_key",in="query",required=true, description="驗證碼 Key", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="成功",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="uuid", type="string", example="0b422f02-5acf-4bbb-bddf-4f6fdd843b08"),
     *             @OA\Property(property="useraccount", type="string", example="wendyyao"),
     *             @OA\Property(property="password_hash", type="string", example="$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"),
     *             @OA\Property(property="is_valid", type="string", example="1"),
     *             @OA\Property(property="create_user", type="string", example="admin"),
     *             @OA\Property(property="update_user", type="string", example="admin"),
     *             @OA\Property(property="create_time", type="string", example="2025-03-31T08:58:52.001975Z"),
     *             @OA\Property(property="update_time", type="string", example="2025-03-31T08:58:52.001986Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="驗證帳號密碼失敗"
     *     )
     * )
     */
    public function verifyuser(Request $request)
    {
        if (!$request->has('useraccount') || !$request->has('password') || !$request->has('captcha_input') || !$request->has('captcha_key')) {
            return response()->json([
                'status' => false,
                'message' => '欄位不完整',
                'output' => []
            ], 422);
        }

        // 驗證碼檢查
        if (!Captcha::check_api($request->captcha_input, $request->captcha_key)) {
            return response()->json([
                'status' => false,
                'message' => '驗證碼錯誤',
                'output' => []
            ], 422);
        }

        // 帳號密碼驗證，是否存在資料表
        $user = User::where('useraccount', $request->useraccount)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => '帳號不存在',
                'output' => []
            ])->setStatusCode(404);
        }
        if ($user && password_verify($request->password, $user->password_hash)) {
            // user寫入remember_token
            $user->remember_token = $user->createToken('auth_token')->plainTextToken;
            // 如果驗證成功，登入使用者
            Auth::login($user,$remember = true);
            return response()->json([
                'status' => true,
                'message' => '登入成功',
                'output' => $user
            ])->setStatusCode(200);

        }else{
            // 如果驗證失敗，返回錯誤訊息
            return response()->json([
                'status' => false,
                'message' => '帳號或密碼錯誤',
                'output' => password_verify($request->password, $user->password)
            ])->setStatusCode(401);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json([
            'status' => true,
            'message' => '登出成功',
            'output' => []
        ])->setStatusCode(200);
    }
}