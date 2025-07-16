<?php

namespace App\controllers;

use App\core\error\CustomException;
use App\core\Response;
use App\services\AuthService;

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

    if (!isset($data['code']) || $data['code'] === '' || isset($data['error'])) {
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
    // $sub = $this->authService->validAccessToken($_COOKIE['session_token'] ?? null);
    // if (!$sub) {
    //   Response::error($result['message'], $result['status']);
    //   return;
    // }
    try {
      $this->authService->validAccessToken($_COOKIE['session_token'] ?? null);
    } catch (CustomException $e) {
      return Response::json($e->toArray(), $e->getCode());
    }
  }
}
