<?php

require_once __DIR__ . '/../core/response.php';
require_once __DIR__ . '/../core/AuthContext.php';

class AuthController
{
  private AuthService $authService;

  public function __construct(AuthService $authService)
  {
    $this->authService = $authService;
  }

  /**
   * handleGetGoogleSSOLink
   */
  public function handleGetGoogleSSOLink(): void
  {
    $link = $this->authService->getGoogleSSOLink();
    Response::success(['url' => $link]);
  }

  /**
   * handleLoginCallback
   */
  public function handleLoginCallback(): void
  {
    $raw  = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);

    if (!isset($data['code']) || $data['code'] === '') {
      Response::error('Missing authorization code', 400);
      return;
    }

    $result = $this->authService->loginWithCode($data['code']);
    setcookie('session_token', $result['token'], $result['cookieOptions']);

    header('Permissions-Policy: secure-credentials=()');
    Response::success(['me' => $result['user']]);
  }

  /**
   * handleValidAccessToken
   */
  public function handleValidAccessToken()
  {
    $result = $this->authService->validAccessToken($_COOKIE['session_token'] ?? null);
    if (!$result['valid']) {
      Response::error($result['message'], $result['status']);
      return;
    }

    AuthContext::setUser($result['user']);
  }
}
