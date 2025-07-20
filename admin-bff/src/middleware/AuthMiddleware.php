<?php

use App\core\AuthContext;
use App\core\error\CustomException;
use App\services\AuthService;
use App\services\MemberService;
use App\repositories\AuthRepository;
use App\repositories\UserRepository;
use App\repositories\MemberRepository;
use App\models\MeModel;

class AuthMiddleware
{
  public static function handle()
  {
    error_log("🍆🍆🍆middleware🍆🍆🍆 ENV:" . $_ENV['ENV']);

    // ローカル環境なら認証スキップしてテストユーザーをセット
    if ($_ENV['ENV'] === 'test') {
      $me = new MeModel([
        'userID' => 'auth0|67bade478a6c6c144c19cc4a',
        'email' => 'airi.yoshizaki@leverages.jp',
        'logoPath' => 'https://s.gravatar.com/avatar/919e6bc66971f71047d752ff8b35d679?s=480&r=pg&d=https%3A%2F%2Fcdn.auth0.com%2Favatars%2Fai.png',
        'updatedAt' => '2025-06-14T13:59:55+00:00',
        'role' => 'owner',
        'refreshToken' => 'v1.MRoAjWz3lmb0iWEpzKZfZ5dO3TLJhVz1mUXwZPCfuv0Rm6JHN0pfqsSJB3Fndt4qGTG_Alt2OVnz9Z4Cq4krdSA'
      ]);
      AuthContext::setMe($me);
      return;
    }

    // 各依存の初期化（テスト環境用）
    $userRepo = new UserRepository();
    $authRepo = new AuthRepository();
    $memRepo = new MemberRepository();

    $memService = new MemberService($memRepo);
    $authService = new AuthService($authRepo, $userRepo);

    // アクセストークンの検証（セッショントークンっていう命名にしてしまってるので直す）
    try {
      $sub = $authService->validAccessToken($_COOKIE['session_token'] ?? null);

      $me = $memService->getMe($sub);
      if (!$me) {
        throw new CustomException(403, 'Forbidden', 'User not found');
      }

      error_log("🍎 ログインユーザ:" . json_encode($me->toArray(), true));

      AuthContext::setMe($me);
    } catch (CustomException $e) {
      throw $e;
    } catch (Exception $e) {
      throw new CustomException(
        $e->getCode(),
        $e->getMessage(),
        get_class($e),
      );
    }

    // $authService
    // getMeでワークスペースに所属するmemberを取得する

    // 認証が成功していればユーザー情報を返す
    // return Response::success($authService->setAuthContext());
  }
}
