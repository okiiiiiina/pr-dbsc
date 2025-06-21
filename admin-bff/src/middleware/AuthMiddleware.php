<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../core/AuthContext.php';

class AuthMiddleware
{
  public static function handle()
  {
    error_log("🍆🍆🍆middleware s🍆🍆🍆");

    // 各依存の初期化（テスト環境用）
    $userRepo = new UserRepository();
    $authRepo = new AuthRepository();
    $authService = new AuthService($authRepo, $userRepo);
    $authContext = new AuthContext();
    // $authController = new AuthController($authService);

    // アクセストークンの検証
    $res = $authService->validAccessToken($_COOKIE['session_token'] ?? null);
    if (!$res['valid']) {
      error_log("🍆" . json_encode($res));
      return Response::error($res['message'], $res['status']);
    }

    $authContext->setUser($res['user']);

    error_log("🍆🍆🍆middleware e🍆🍆🍆" . json_encode($res));

    // $authService
    // getMeでワークスペースに所属するmemberを取得する

    // 認証が成功していればユーザー情報を返す
    // return Response::success($authService->setAuthContext());
  }
}
