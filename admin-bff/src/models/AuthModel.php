<?php
class AuthModel
{
  private string $storageFile = __DIR__ . '/../storage/users.json';
  private string $domain;
  private string $clientId;
  private string $clientSecret;
  private string $redirectUri;

  public function __construct()
  {
    $this->domain = $_ENV['AUTH0_DOMAIN'];
    $this->clientId = $_ENV['AUTH0_CLIENT_ID'];
    $this->clientSecret = $_ENV['AUTH0_CLIENT_SECRET'];
    $this->redirectUri = $_ENV['AUTH0_REDIRECT_URI'];
  }

  /**
   * Google認証用のAuth0ログインURLを生成する
   */
  public function getGoogleSSOLink(): string
  {
    return "https://$this->domain/authorize?" . http_build_query([
      'client_id' => $this->clientId,
      'response_type' => 'code',
      'scope' => 'openid profile email',
      'redirect_uri' => $this->redirectUri,
      'connection' => 'google-oauth2',
    ]);
  }

  /**
   * 認可コードをアクセストークンに交換する
   */
  public function exchangeCodeForToken($code): string
  {
    $payload = [
      'grant_type' => 'authorization_code',
      'client_id' => $this->clientId,
      'client_secret' => $this->clientSecret,
      'code' => $code,
      'redirect_uri' => $this->redirectUri,
    ];

    $options = [
      'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json",
        'content' => json_encode($payload),
        'ignore_errors' => true,
      ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents("https://{$this->domain}/oauth/token", false, $context);

    if ($response === false) {
      throw new Exception("Failed to get token from Auth0");
    }

    $data = json_decode($response, true);

    if (!isset($data['access_token'])) {
      throw new Exception("Token not found in response: " . $response);
    }

    return $data['access_token'];
  }

  /**
   * アクセストークンからユーザー情報を取得しDBに保存する
   */
  public function syncUserFromToken(string $accessToken): array
  {
    error_log("🍎🍎🍎");
    $options = [
      'http' => [
        'method' => 'GET',
        'header' => "Authorization: Bearer {$accessToken}",
        'ignore_errors' => true,
      ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents("https://{$this->domain}/oauth/userinfo", false, $context);

    if ($response === false) {
      throw new Exception("Failed to get userInfo from Auth0");
    }

    $userInfo = json_decode($response, true);

    if (!$userInfo || !isset($userInfo['sub'])) {
      throw new Exception("Invalid userinfo response");
    }

    $sub = $userInfo['sub'];

    $users = $this->loadUsers();
    if (isset($users[$sub])) {
      return $users[$sub];
    }

    $users[$sub] = [
      'sub' => $sub,
      'email' => $userInfo['email'] ?? '',
      'name' => $userInfo['name'] ?? '',
      'picture' => $userInfo['picture'] ?? '',
      'created_at' => date('c'),
    ];

    $result = file_put_contents(
      $this->storageFile,
      json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );

    if ($result === false) {
      throw new Exception("❌ Failed to write to storage file: {$this->storageFile}");
    }

    return $users[$sub];
  }

  /**
   * 保存されたユーザーデータをファイルから読み込む
   */
  private function loadUsers(): array
  {
    if (!file_exists($this->storageFile)) {
      return [];
    }

    $json = file_get_contents($this->storageFile);
    return json_decode($json, true) ?? [];
  }
}
