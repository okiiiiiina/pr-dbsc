<?php
// ────────────────────────────────────────────────────────────
// CORS ― pre-flight (OPTIONS)
// ────────────────────────────────────────────────────────────
// プリフライトリクエスト
// ブラウザ側から「この条件でリクエストしてもいい？」という確認の問い合わせ。
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  header('Access-Control-Allow-Origin: https://localhost:3101');
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type');
  exit;
}

// ────────────────────────────────────────────────────────────
// 共通レスポンスヘッダー
// ────────────────────────────────────────────────────────────
// ▼ ここから実レスポンス用の共通ヘッダーを追加
header('Access-Control-Allow-Origin: https://localhost:3101');
header('Access-Control-Allow-Credentials: true');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/core/routing/Router.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$router = new Router();
$router->registerRoutes();
$router->dispatch();
