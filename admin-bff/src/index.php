<?php

/*******************************************************************
 * index.php ― DBSC 練習用ワンファイル版
 * ---------------------------------------------------------------
 * すべてのクラス・ルーティング・ミドルウェアを 1 ファイルに集約。
 * ストレージは ./storage/dbsc_keys.json に公開鍵を保存します。
 *******************************************************************/

// ===============================
// 0. Composer autoload / .env
// ===============================
// src/index.php の先頭
error_log('🔥 index.php accessed');

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// ===============================
// 1. ヘルパークラス
// ===============================
class Response
{
  public static function success(array $data): void
  {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
  }
  public static function error(string $msg, int $code = 400): void
  {
    http_response_code($code);
    self::success(['error' => $msg]);
  }
}

// ===============================
// 2. 軽量公開鍵ストア (JSON)
// ===============================
class DbscKeyStore
{
  private const FILE = __DIR__ . '/storage/dbsc_keys.json';

  private static function load(): array
  {
    error_log('🍎 DbscKeyStore load');
    if (!file_exists(self::FILE)) return [];
    return json_decode(file_get_contents(self::FILE) ?: '[]', true) ?? [];
  }
  private static function save(array $data): void
  {
    error_log('🍎 DbscKeyStore save');
    file_put_contents(self::FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
  }
  public static function put(string $sid, string $pubKey): void
  {
    $all = self::load();
    $all[$sid] = $pubKey;
    self::save($all);
  }
  public static function get(string $sid): ?string
  {
    error_log('🍎 DbscKeyStore get');
    $all = self::load();
    return $all[$sid] ?? null;
  }
}

// ===============================
// 3. DBSC ミドルウェア
// ===============================
class DbscMiddleware
{
  public static function handle(): void
  {
    $sid = $_SERVER['HTTP_SEC_SESSION_ID']        ?? null;
    $sig = $_SERVER['HTTP_SEC_SESSION_SIGNATURE'] ?? null;
    if (!$sid || !$sig) {
      http_response_code(401);
      echo 'DBSC header missing';
      exit;
    }
    $pubKey = DbscKeyStore::get($sid);
    if (!$pubKey || openssl_verify($sid, base64_decode($sig), $pubKey, OPENSSL_ALGO_SHA256) !== 1) {
      http_response_code(401);
      echo 'Invalid DBSC signature';
      exit;
    }
  }
}

// ===============================
// 4. AuthModel (Auth0 用)
// ===============================
class AuthModel
{
  private string $domain;
  private string $clientId;
  private string $clientSecret;
  private string $redirectUri;
  private string $storageFile = __DIR__ . '/storage/users.json';

  public function __construct()
  {
    $this->domain       = $_ENV['AUTH0_DOMAIN'];
    $this->clientId     = $_ENV['AUTH0_CLIENT_ID'];
    $this->clientSecret = $_ENV['AUTH0_CLIENT_SECRET'];
    $this->redirectUri  = $_ENV['AUTH0_REDIRECT_URI'];
  }
  public function getGoogleSSOLink(): string
  {
    error_log('🌱 AuthModel getGoogleSSOLink');
    return "https://{$this->domain}/authorize?" . http_build_query([
      'client_id'     => $this->clientId,
      'response_type' => 'code',
      'scope'         => 'openid profile email',
      'redirect_uri'  => $this->redirectUri,
      'connection'    => 'google-oauth2',
    ]);
  }
  public function exchangeCodeForToken(string $code): string
  {
    error_log('🌱 AuthModel exchangeCodeForToken');
    $payload = [
      'grant_type'    => 'authorization_code',
      'client_id'     => $this->clientId,
      'client_secret' => $this->clientSecret,
      'code'          => $code,
      'redirect_uri'  => $this->redirectUri,
    ];
    $ctx = stream_context_create(['http' => [
      'method'  => 'POST',
      'header'  => "Content-Type: application/json",
      'content' => json_encode($payload),
    ]]);
    $json = file_get_contents("https://{$this->domain}/oauth/token", false, $ctx);
    if (!$json) throw new RuntimeException('Token fetch failed');
    $data = json_decode($json, true);
    return $data['access_token'] ?? throw new RuntimeException('access_token missing');
  }
  public function syncUserFromToken(string $accessToken): array
  {
    error_log('🌱 AuthModel syncUserFromToken');
    $ctx = stream_context_create(['http' => [
      'method'  => 'GET',
      'header'  => "Authorization: Bearer {$accessToken}",
    ]]);
    $json = file_get_contents("https://{$this->domain}/oauth/userinfo", false, $ctx);
    if (!$json) throw new RuntimeException('userinfo fetch failed');
    $info = json_decode($json, true);
    if (!isset($info['sub'])) throw new RuntimeException('invalid userinfo');
    $users = file_exists($this->storageFile) ? json_decode(file_get_contents($this->storageFile), true) ?? [] : [];
    $users[$info['sub']] = $info;
    file_put_contents($this->storageFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    return $info;
  }
}

// ===============================
// 5. コントローラー
// ===============================
use Firebase\JWT\JWT;

class AuthController
{
  public function handleGetGoogleSSOLink(): void
  {
    error_log('🍏 handleGetGoogleSSOLink');
    $url = (new AuthModel())->getGoogleSSOLink();
    Response::success(['url' => $url]);
  }
  public function handleLoginCallback(): void
  {
    $data = json_decode(file_get_contents('php://input') ?: '{}', true);
    if (empty($data['code'])) Response::error('Missing code', 400);
    $model  = new AuthModel();
    $token  = $model->exchangeCodeForToken($data['code']);
    $user   = $model->syncUserFromToken($token);
    $exp    = time() + 3600;
    $jwt    = JWT::encode(['sub' => $user['sub'], 'exp' => $exp], $_ENV['JWT_SECRET'], 'HS256');
    setcookie('session_token', $jwt, [
      'expires'  => $exp,
      'path'     => '/',
      'domain'   => $_ENV['COOKIE_DOMAIN']   ?? 'localhost',
      'secure'   => true,
      'httponly' => true,
      'samesite' => $_ENV['COOKIE_SAMESITE'] ?? 'None',
    ]);
    header('Sec-Session-Registration: (ES256); path="/api/auth/dbsc-register";challenge="12345');
    // header('Sec-Session-Registration: *; path="/api/auth/dbsc-register"');
    Response::success(['me' => $user]);
  }
  public function handleDbscRegistration(): void
  {
    error_log('🍏 handleDbscRegistration');

    $raw = file_get_contents('php://input');
    error_log("🍏 raw body: $raw");

    $data = json_decode($raw ?: '{}', true);
    if (empty($data['id']) || empty($data['publicKey'])) {
      error_log("🍏 DBSC payload 不正");
      Response::error('invalid payload');
    }

    DbscKeyStore::put($data['id'], $data['publicKey']);
    Response::success(['status' => 'registered']);
  }
}
class SecureController
{
  public function index(): void
  {
    Response::success(['message' => '🎉 DBSC-protected endpoint reached']);
  }
}

// ===============================
// 6. ルーター
// ===============================
class Router
{
  private array $routes = [];
  public function registerRoutes(): void
  {
    $this->routes = [
      'GET/api/health'                  => ['handler' => fn() => Response::success(['ok' => true]), 'middleware' => []],
      'GET/api/auth/google-sso'     => ['handler' => fn() => (new AuthController())->handleGetGoogleSSOLink(), 'middleware' => []],
      'POST/api/auth/callback'      => ['handler' => fn() => (new AuthController())->handleLoginCallback(), 'middleware' => []],
      'POST/api/auth/dbsc-register' => ['handler' => fn() => (new AuthController())->handleDbscRegistration(), 'middleware' => []],
      'GET/api/secure-endpoint'     => ['handler' => fn() => (new SecureController())->index(), 'middleware' => [DbscMiddleware::class]],
    ];
  }
  public function dispatch(): void
  {
    $key = $_SERVER['REQUEST_METHOD'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (!isset($this->routes[$key])) {
      http_response_code(404);
      echo 'Not Found';
      return;
    }
    $route = $this->routes[$key];
    foreach ($route['middleware'] as $m) if (is_callable([$m, 'handle'])) $m::handle();
    $route['handler']();
  }
}

// =================================================================
// 7. CORS & DBSC 共通ヘッダー + ルーター起動
// =================================================================
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header('Access-Control-Allow-Origin: https://localhost:3101');
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token, Sec-Session-Id, Sec-Session-Signature');
  http_response_code(200);
  exit;
}
header('Access-Control-Allow-Origin: https://localhost:3101');
header('Access-Control-Allow-Credentials: true');
header('Permissions-Policy: secure-credentials=()');

$router = new Router();
$router->registerRoutes();
$router->dispatch();
