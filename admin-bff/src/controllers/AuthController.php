<?php
require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../core/Response.php';

use Firebase\JWT\JWT;

class AuthController
{
  /**
   * Google SSO 開始用リンクを返す
   */
  public function handleGetGoogleSSOLink(): void
  {
    $model = new AuthModel();
    $link  = $model->getGoogleSSOLink();

    // DBSC で必須のポリシーヘッダー
    header('Permissions-Policy: secure-credentials=()');

    Response::success(['url' => $link]);
  }

  /**
   * Google からのリダイレクト後に呼ばれる
   *  - 認可コードを受け取りトークン交換
   *  - ユーザ情報を同期
   *  - Device-Bound なセッションクッキーを発行
   */
  public function handleLoginCallback(): void
  {
    // --- ① ボディ取得・バリデーション ----------------------------
    $raw  = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);

    if (!isset($data['code']) || $data['code'] === '') {
      Response::error('Missing authorization code', 400);
      return;
    }
    $authCode = $data['code'];

    // --- ② Google からトークン取得 & ユーザ同期 --------------------
    $model      = new AuthModel();
    $tokenInfo  = $model->exchangeCodeForToken($authCode);
    $currentUser = $model->syncUserFromToken($tokenInfo);

    // --- ③ JWT 作成 ---------------------------------------------
    $exp     = time() + 60 * 60;          // 1 時間後
    $payload = ['sub' => $currentUser['sub'], 'exp' => $exp];
    $jwt     = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

    // --- ④ Device-Bound セッションクッキー発行 -------------------
    $cookieOpts = [
      'expires'  => $exp,
      'path'     => '/',
      'domain'   => $_ENV['COOKIE_DOMAIN'] ?? 'localhost',
      'secure'   => true,
      'httponly' => true,
      // ★ 同一サイトなら 'Lax' / クロスサイト運用なら 'None'
      'samesite' => $_ENV['COOKIE_SAMESITE'] ?? 'None',
    ];
    setcookie('session_token', $jwt, $cookieOpts);

    // --- ⑤ レスポンス -------------------------------------------
    header('Permissions-Policy: secure-credentials=()');
    Response::success(['me' => $currentUser]);
  }

  /**
   * ブラウザからの公開鍵登録 (自動 POST)
   */
  public function handleDbscRegistration(): void
  {
    $raw  = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);

    if (empty($data['id']) || empty($data['publicKey'])) {
      Response::error('Invalid registration payload', 400);
      return;
    }

    DbscKeyStore::put($data['id'], $data['publicKey']);
    Response::success(['status' => 'registered']);
  }
}
