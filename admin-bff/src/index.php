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
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Expose-Headers: Content-Type');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/core/routing/Router.php';
require_once __DIR__ . '/core/Response.php';
require_once __DIR__ . '/core/error/CustomException.php';

use App\core\error\CustomException;
use App\core\Response;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


try {
  $router = new Router();
  $router->registerRoutes();
  $router->dispatch();
} catch (CustomException $e) {
  http_response_code($e->getCode());
  header('Content-Type: application/json');
  return Response::error($e->getMessage(), $e->getCode(), get_class($e));
} catch (Throwable $e) {
  error_log("☠️☠️☠️" . $e);
  http_response_code(500);
  header('Content-Type: application/json');
  return Response::error('Internal Server Error', 500, get_class($e));
}
