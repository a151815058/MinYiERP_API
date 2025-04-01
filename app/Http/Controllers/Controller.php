<?php

namespace App\Http\Controllers;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="My Laravel API",
 *     version="1.0",
 *     description="這是我們的 API 文件",
 *     @OA\Contact(
 *         email="xxx"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="本機開發環境"
 * )
 */
abstract class Controller
{
    //
}
