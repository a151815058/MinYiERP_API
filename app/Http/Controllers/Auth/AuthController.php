<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\JWTService;
use Mews\Captcha\Facades\Captcha;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /** 
     * @OA\Get(
     *   path="/api/getcaptcha",
     *   tags={"base_auth"},
     *   summary="取得驗證碼（base64 + key）",
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function captcha()
    {
        $captcha = Captcha::create('default', true);
        return response()->json([
            'status'  => true,
            'message' => '取得驗證碼成功',
            'output'  => [
                'captcha_img' => $captcha['img'],
                'key'         => $captcha['key'],
            ]
        ]);
    }

    /** 
     * @OA\Post(
     *   path="/api/auth/login",
     *   tags={"base_auth"},
     *   summary="登入（簽發 Access Token、設定 Refresh Token Cookie）",
     *   @OA\Parameter(name="useraccount", in="query", required=true, @OA\Schema(type="string")),
     *   @OA\Parameter(name="password",    in="query", required=true, @OA\Schema(type="string")),
     *   @OA\Parameter(name="captcha_input", in="query", required=true, @OA\Schema(type="string")),
     *   @OA\Parameter(name="captcha_key",   in="query", required=true, @OA\Schema(type="string")),
     *   @OA\Parameter(name="remember", in="query", required=false, @OA\Schema(type="boolean")),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function login(Request $request, JWTService $jwt)
    {
        try{
            $request->validate([
                'useraccount'  => 'required|string',
                'password'     => 'required|string',
                'captcha_input'=> 'required|string',
                'captcha_key'  => 'required|string',
                'remember'     => 'nullable|in:true,false,1,0',
            ]);

            if (!Captcha::check_api($request->captcha_input, $request->captcha_key)) {
                return response()->json(['status'=>false,'message'=>'驗證碼錯誤','output'=>[]], 422);
            }

            $user = User::where('useraccount', $request->useraccount)->first();
            if (!$user) {
                return response()->json(['status'=>false,'message'=>'帳號不存在','output'=>[]], 404);
            }
            if (!Hash::check($request->password, $user->password_hash)) {
                return response()->json(['status'=>false,'message'=>'帳號或密碼錯誤','output'=>[]], 401);
            }
            
            // 取得使用者角色和菜單資料
            $sql_data = "SELECT user.id ,
                            user.useraccount ,
                            role.`code` as role_code
                        FROM user
                        INNER JOIN user_roles ON user_roles.user_id = user.id
                        INNER JOIN role ON role.`uuid` = user_roles.role_id
                        WHERE user.id = ?";
            $sql_data = DB::select($sql_data, [$user->id]);
            if (empty($sql_data)) {
                return response()->json(['status'=>false,'message'=>'使用者無任何角色，請聯絡管理員','output'=>[]], 403);
            }

            // 1) 簽發 Access Token（最小化 Claims）
            $accessToken = $jwt->signAccessToken([
                'sub'     => $sql_data[0]->id,                 // 使用者唯一ID
                'user_no' => $sql_data[0]->useraccount,
                'role'    => $sql_data[0]->role_code ?? 'user',  // 視你的欄位而定
                // 'tenant_id' => $user->tenant_id ?? null,
                'menu_ver'=> now()->toDateString(),       // 可用於前端是否更新選單
            ]);
            


            // 2) 建立 Refresh Token（HttpOnly Cookie）
            $remember = $request->boolean('remember', true); 
            $cookie   = null;
            
            if ($remember) {
                
                $rawRt   = bin2hex(random_bytes(32));           // 256-bit
                $rtHash  = hash('sha256', $rawRt);
                $days    = (int) env('JWT_RT_TTL_DAYS', 30);

                $insertResult = DB::table('refresh_tokens')->insert([
                    'user_id'    => $sql_data[0]->id,
                    'token_hash' => $rtHash,
                    'issued_at'  => now(),
                    'expires_at' => now()->addDays($days),
                    'device_info'=> substr($request->userAgent() ?? '', 0, 255),
                    'ip_address' => $request->ip(),
                ]);
                
                Log::info('Refresh Token Insert Result', [
                    'success' => $insertResult,
                    'user_id' => $sql_data[0]->id,
                    'token_hash' => substr($rtHash, 0, 10) . '...',
                    'ip' => $request->ip()
                ]);

                // 設定 Cookie（/api/auth 路徑可依需求調整）
                $cookie = cookie(
                    'refresh_token',
                    $rawRt,
                    60 * 24 * $days,      // 分鐘
                    '/api/auth',
                    null,
                    true,                 // Secure
                    true,                 // HttpOnly
                    false,
                    'Strict'              // SameSite
                );
            }

            // 3) 回應（不回 remember_token、不回整包 sysmenu）
            $resp = response()->json([
                'status'       => true,
                'message'      => '登入成功',
                'access_token' => $accessToken,
                'token_type'   => 'Bearer',
                'expires_in'   => (int) env('JWT_AT_TTL', 3600),
                'user' => [
                    'uuid'     => $sql_data[0]->id,
                    'user_no'  => $sql_data[0]->useraccount,
                    'role_code'=> $sql_data[0]->role_code ?? 'user',
                ],
                'menu_ver'     => now()->toDateString()
            ]);

            return $cookie ? $resp->cookie($cookie) : $resp;
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
     *   path="/api/auth/refresh",
     *   tags={"base_auth"},
     *   summary="用 Cookie 中的 RT 兌換新的 AT（旋轉 RT）",
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function refresh(Request $request, JWTService $jwt)
    {
        $rawRt = $request->cookie('refresh_token');
        if (!$rawRt) return response()->json(['message'=>'缺少 refresh token'], 401);

        $row = DB::table('refresh_tokens')
            ->where('token_hash', hash('sha256', $rawRt))
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$row) return response()->json(['message'=>'refresh token 無效或過期'], 401);

        // 撤銷舊 RT
        DB::table('refresh_tokens')->where('id', $row->id)->update(['revoked_at' => now()]);

        // 重新查使用者
        $user = User::where('id', $row->user_id)->first();
        if (!$user) return response()->json(['message'=>'使用者不存在'], 401);

        // 新 AT
        $accessToken = $jwt->signAccessToken([
            'sub'     => $user->id,
            'user_no' => $user->useraccount,
            'role'    => $user->role_code ?? 'user',
            'menu_ver'=> now()->toDateString(),
        ]);

        // 旋轉新 RT
        $newRt  = bin2hex(random_bytes(32));
        DB::table('refresh_tokens')->insert([
            'user_id'    => $user->id,
            'token_hash' => hash('sha256', $newRt),
            'issued_at'  => now(),
            'expires_at' => now()->addDays((int) env('JWT_RT_TTL_DAYS', 30)),
            'device_info'=> substr($request->userAgent() ?? '', 0, 255),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'access_token' => $accessToken,
            'token_type'   => 'Bearer',
            'expires_in'   => (int) env('JWT_AT_TTL', 3600),
        ])->cookie(
            'refresh_token', $newRt, 60*24*(int)env('JWT_RT_TTL_DAYS',30),
            '/api/auth', null, true, true, false, 'Strict'
        );
    }

    /**
     * @OA\get(
     *   path="/api/auth/logout",
     *   tags={"base_auth"},
     *   summary="登出（撤銷當前 RT、清 Cookie）",
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function logout(Request $request)
    {
        $rawRt = $request->cookie('refresh_token');
        if ($rawRt) {
            DB::table('refresh_tokens')
              ->where('token_hash', hash('sha256', $rawRt))
              ->update(['revoked_at' => now()]);
        }

        Auth::logout(); // 若你同時使用 Laravel session 登入
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['status'=>true,'message'=>'登出成功','output'=>[]])
               ->withoutCookie('refresh_token', '/api/auth');
    }

    /**
     * @OA\Post(
     *   path="/api/me/menus",
     *   tags={"base_auth"},
     *   @OA\Parameter(name="access_token", in="query", required=true, @OA\Schema(type="string")),
     *   summary="依 Access Token 取得當前使用者選單",
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function meMenus(Request $request, JWTService $jwt)
    {
        // 從 Authorization: Bearer <token> 解析使用者
        $auth = $request['access_token'];
        if (!$auth) return response()->json(['message'=>'缺少 access token'], 401);

        try {
            $decoded = $jwt->verify($auth);
        } catch (\Throwable $e) {
            return response()->json(['message'=>'access token 無效'], 401);
        }

        $userId = $decoded->sub ?? null;
        if (!$userId) return response()->json(['message'=>'access token 缺少 sub'], 401);

        // 依你原 SQL 取得選單
        $sql = "SELECT s.uuid as menu_id, s.no_prog, s.nm_text, s.gn_url
                FROM user u
                JOIN user_roles ur ON ur.user_id = u.id
                JOIN role r ON r.uuid = ur.role_id
                JOIN role_menus rm ON rm.role_id = r.uuid
                JOIN sysmenu s ON s.uuid = rm.menu_id
                WHERE u.id = ?";
        $rows = DB::select($sql, [$userId]);

        return response()->json([
            'sysmenu'  => $rows,
            'menu_ver' => now()->toDateString()
        ]);
    }
}