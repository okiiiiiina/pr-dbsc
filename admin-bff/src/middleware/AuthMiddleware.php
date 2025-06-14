<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthMiddleware
{
  public static function handle()
  {
    error_log("🍆🍆🍆middlware🍆🍆🍆");
    // Controllerを直接newして使う（テスト用ならOK）
    $userRepo = new UserRepository();
    $authRepo = new AuthRepository();
    $authService = new AuthService($authRepo, $userRepo);
    $authController = new AuthController($authService);

    return $authController->handleValidAccessToken(); // 認証チェック＆AuthContextにユーザをセット
  }
}
